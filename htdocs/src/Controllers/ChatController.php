<?php
namespace App\Controllers;

use App\Models\ChatMessage;
use App\Models\User;

/**
 * Contrôleur API pour le chat en temps réel
 */
class ChatController {
    private ChatMessage $chatModel;
    private User $userModel;
    
    public function __construct() {
        $this->chatModel = new ChatMessage();
        $this->userModel = new User();
    }
    
    /**
     * Récupère les messages d'un canal
     */
    public function getMessages(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        $channel = $_GET['channel'] ?? 'general';
        $afterId = isset($_GET['after_id']) ? (int)$_GET['after_id'] : null;
        $limit = min((int)($_GET['limit'] ?? 50), 100);
        
        try {
            // Mettre à jour le statut en ligne
            $this->chatModel->updateOnlineStatus(currentUserId(), $channel);
            
            // Récupérer les messages
            if ($afterId) {
                $messages = $this->chatModel->getNewMessages($channel, $afterId);
            } else {
                $messages = $this->chatModel->getByChannel($channel, $limit);
            }
            
            // Formater les messages
            $formatted = array_map(function($msg) {
                return [
                    'id' => (int)$msg['id'],
                    'user_id' => (int)$msg['user_id'],
                    'user_name' => $msg['first_name'] . ' ' . $msg['last_name'],
                    'user_initials' => strtoupper(substr($msg['first_name'], 0, 1) . substr($msg['last_name'], 0, 1)),
                    'message' => $msg['message'],
                    'reply_to' => $msg['reply_to_id'] ? [
                        'id' => (int)$msg['reply_to_id'],
                        'message' => mb_substr($msg['reply_message'] ?? '', 0, 50),
                        'user_name' => ($msg['reply_first_name'] ?? '') . ' ' . ($msg['reply_last_name'] ?? '')
                    ] : null,
                    'created_at' => $msg['created_at'],
                    'time_ago' => $this->timeAgo($msg['created_at']),
                    'is_mine' => (int)$msg['user_id'] === currentUserId()
                ];
            }, $messages);
            
            // Marquer comme lus
            if (!empty($messages)) {
                $this->chatModel->markAsRead(currentUserId(), $channel);
            }
            
            echo json_encode([
                'success' => true,
                'messages' => $formatted,
                'last_id' => !empty($formatted) ? end($formatted)['id'] : ($afterId ?? 0)
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Envoie un message
     */
    public function sendMessage(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $channel = $input['channel'] ?? 'general';
        $message = trim($input['message'] ?? '');
        $replyToId = isset($input['reply_to_id']) ? (int)$input['reply_to_id'] : null;
        
        if (empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Message vide']);
            return;
        }
        
        if (mb_strlen($message) > 1000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Message trop long (max 1000 caractères)']);
            return;
        }
        
        try {
            $userId = currentUserId();
            $messageId = $this->chatModel->send($userId, $channel, $message, $replyToId);
            
            // Récupérer le message formaté
            $user = $this->userModel->find($userId);
            
            echo json_encode([
                'success' => true,
                'message' => [
                    'id' => $messageId,
                    'user_id' => $userId,
                    'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                    'user_initials' => strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)),
                    'message' => $message,
                    'reply_to' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'time_ago' => 'À l\'instant',
                    'is_mine' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Supprime un message
     */
    public function deleteMessage(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $messageId = (int)($input['message_id'] ?? 0);
        
        if (!$messageId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID message requis']);
            return;
        }
        
        try {
            $deleted = $this->chatModel->deleteMessage($messageId, currentUserId(), isAdmin());
            
            echo json_encode([
                'success' => $deleted,
                'error' => $deleted ? null : 'Impossible de supprimer ce message'
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Récupère les utilisateurs en ligne
     */
    public function getOnlineUsers(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        $channel = $_GET['channel'] ?? null;
        
        try {
            $users = $this->chatModel->getOnlineUsers($channel);
            
            $formatted = array_map(function($user) {
                return [
                    'id' => (int)$user['user_id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'initials' => strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)),
                    'channel' => $user['current_channel']
                ];
            }, $users);
            
            echo json_encode([
                'success' => true,
                'users' => $formatted,
                'count' => count($formatted)
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Récupère le nombre de messages non lus
     */
    public function getUnreadCount(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        try {
            $count = $this->chatModel->getTotalUnreadCount(currentUserId());
            
            echo json_encode([
                'success' => true,
                'unread_count' => $count
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Récupère les canaux disponibles
     */
    public function getChannels(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        try {
            $channels = $this->chatModel->getAvailableChannels(currentUserId());
            
            // Ajouter le compteur de non lus pour chaque canal
            foreach ($channels as &$channel) {
                $channel['unread_count'] = $this->chatModel->getUnreadCount(currentUserId(), $channel['channel']);
            }
            
            echo json_encode([
                'success' => true,
                'channels' => $channels
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Calcule le temps écoulé
     */
    private function timeAgo(string $datetime): string {
        // Retourner le timestamp pour calcul côté client
        return $datetime;
    }
}
