<?php
/**
 * DocuFlow - Routeur
 * Gestion des routes de l'application
 */

namespace App;

class Router {
    private array $routes = [];
    private string $basePath;
    
    public function __construct(string $basePath = '') {
        $this->basePath = $basePath;
    }
    
    /**
     * Ajoute une route GET
     */
    public function get(string $path, string $controller, string $method): void {
        $this->addRoute('GET', $path, $controller, $method);
    }
    
    /**
     * Ajoute une route POST
     */
    public function post(string $path, string $controller, string $method): void {
        $this->addRoute('POST', $path, $controller, $method);
    }
    
    /**
     * Ajoute une route pour GET et POST
     */
    public function any(string $path, string $controller, string $method): void {
        $this->addRoute('GET', $path, $controller, $method);
        $this->addRoute('POST', $path, $controller, $method);
    }
    
    /**
     * Ajoute une route
     */
    private function addRoute(string $httpMethod, string $path, string $controller, string $method): void {
        // Convertit les paramètres {id} en regex
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[0-9]+)', $path);
        $pattern = '#^' . $this->basePath . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $httpMethod,
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $method
        ];
    }
    
    /**
     * Résout la route actuelle
     */
    public function resolve(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Supprime le trailing slash sauf pour la racine
        if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
            $requestUri = rtrim($requestUri, '/');
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                $controller = new $route['controller']();
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Convertit les paramètres en entiers si nécessaire
                $params = array_map(function($value) {
                    return is_numeric($value) ? (int) $value : $value;
                }, $params);
                
                call_user_func_array([$controller, $route['action']], $params);
                return;
            }
        }
        
        // 404
        $this->notFound();
    }
    
    /**
     * Page 404
     */
    private function notFound(): void {
        http_response_code(404);
        require __DIR__ . '/Views/pages/404.php';
    }
}
