<?php
/**
 * DocuFlow - Fichier de traductions complet
 * Supporte: Français (fr), Anglais (en)
 * 
 * MISE À JOUR: Ajout des traductions pour Recent Activity
 */

return [
    'fr' => [
        // ==========================================
        // GÉNÉRAL
        // ==========================================
        'app_name' => 'DocuFlow',
        'app_description' => 'Gestion documentaire collaborative',
        'welcome' => 'Bienvenue',
        'home' => 'Accueil',
        'dashboard' => 'Tableau de bord',
        'search' => 'Rechercher...',
        'search_documents' => 'Rechercher des documents...',
        'loading' => 'Chargement...',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'delete' => 'Supprimer',
        'edit' => 'Modifier',
        'create' => 'Créer',
        'add' => 'Ajouter',
        'view' => 'Voir',
        'view_all' => 'Voir tout',
        'back' => 'Retour',
        'close' => 'Fermer',
        'confirm' => 'Confirmer',
        'yes' => 'Oui',
        'no' => 'Non',
        'or' => 'ou',
        'and' => 'et',
        'actions' => 'Actions',
        'settings' => 'Paramètres',
        'language' => 'Langue',
        'french' => 'Français',
        'english' => 'Anglais',
        'other' => 'Autre',
        'system' => 'Système',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'all' => 'Tous',
        'none' => 'Aucun',
        'required' => 'Obligatoire',
        'optional' => 'Optionnel',
        
        // ==========================================
        // NAVIGATION
        // ==========================================
        'nav_documents' => 'Documents',
        'nav_users' => 'Utilisateurs',
        'nav_teams' => 'Équipes',
        'nav_roles' => 'Rôles',
        'nav_profile' => 'Mon profil',
        'nav_logout' => 'Déconnexion',
        'nav_administration' => 'Administration',
        'nav_activity' => 'Activité',
        
        // ==========================================
        // AUTHENTIFICATION
        // ==========================================
        'login' => 'Connexion',
        'login_title' => 'Connexion',
        'login_subtitle' => 'Connectez-vous pour accéder à vos documents',
        'login_button' => 'Se connecter',
        'register' => 'Inscription',
        'register_title' => 'Créer un compte',
        'register_subtitle' => 'Rejoignez DocuFlow pour gérer vos documents',
        'register_button' => 'Créer mon compte',
        'email' => 'Adresse email',
        'password' => 'Mot de passe',
        'password_confirm' => 'Confirmer le mot de passe',
        'remember_me' => 'Se souvenir de moi',
        'forgot_password' => 'Mot de passe oublié ?',
        'no_account' => 'Pas encore de compte ?',
        'have_account' => 'Déjà un compte ?',
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'logout_success' => 'Vous avez été déconnecté.',
        'register_success' => 'Compte créé avec succès ! Vous pouvez vous connecter.',
        'login_error' => 'Email ou mot de passe incorrect.',
        'invalid_credentials' => 'Email ou mot de passe incorrect.',
        'account_deactivated' => 'Compte désactivé. Contactez un administrateur.',
        
        // ==========================================
        // DOCUMENTS
        // ==========================================
        'documents' => 'Documents',
        'document' => 'Document',
        'documents_title' => 'Mes documents',
        'documents_count' => ':count document(s)',
        'upload_document' => 'Uploader un document',
        'new_document' => 'Nouveau document',
        'upload_file' => 'Uploader un fichier',
        'document_title' => 'Titre du document',
        'document_name' => 'Nom du document',
        'document_description' => 'Description',
        'document_file' => 'Fichier PDF',
        'document_category' => 'Catégorie',
        'document_type' => 'Type',
        'document_date' => 'Date',
        'document_uploaded_by' => 'Uploadé par',
        'document_uploaded_at' => 'Uploadé le',
        'document_size' => 'Taille',
        'document_pages' => 'Pages',
        'document_actions' => 'Actions',
        'document_preview' => 'Aperçu',
        'document_download' => 'Télécharger',
        'document_delete' => 'Supprimer',
        'document_share' => 'Partager',
        'no_documents' => 'Aucun document',
        'no_documents_desc' => 'Uploadez votre premier document pour commencer.',
        'drop_files' => 'Glissez-déposez vos fichiers ici',
        'browse_files' => 'Parcourir',
        'delete_document_confirm' => 'Supprimer ce document ?',
        'document_deleted' => 'Document supprimé.',
        'document_created' => 'Document créé avec succès.',
        'document_uploaded' => 'Document uploadé avec succès.',
        'document_processing' => 'Traitement en cours...',
        'upload_first_document' => 'Importez votre premier document pour commencer.',
        'all_types' => 'Tous les types',
        'all_teams' => 'Toutes les équipes',
        'date_format_placeholder' => 'jj/mm/aaaa',
        
        // Page création de document
        'back_to_documents' => 'Retour aux documents',
        'drop_pdf_here' => 'Glissez votre fichier PDF ici',
        'or_click_to_browse' => 'ou cliquez pour parcourir',
        'pdf_only' => 'PDF uniquement',
        'max_size' => 'Max',
        'informations' => 'Informations',
        'document_title_placeholder' => 'Ex: Facture fournisseur Mars 2024',
        'document_description_placeholder' => 'Description optionnelle du document...',
        'reference_number' => 'Numéro de référence',
        'reference_placeholder' => 'Ex: FAC-2024-001',
        'amount' => 'Montant',
        'currency' => 'Devise',
        'attribution' => 'Attribution',
        'team_visibility_hint' => 'Le document sera visible par tous, mais peut être associé à une équipe',
        'pdf_only_alert' => 'Seuls les fichiers PDF sont autorisés.',
        'file_too_large' => 'Le fichier est trop volumineux.',
        
        // Page édition de document
        'edit_document' => 'Modifier le document',
        'back_to_document' => 'Retour au document',
        'uploaded_on' => 'Uploadé le',
        'view_pdf' => 'Voir le PDF',
        'danger_zone' => 'Zone de danger',
        'delete_document_warning' => 'La suppression d\'un document est irréversible. Toutes les zones, liaisons et annotations associées seront également supprimées.',
        'delete_document_confirm_full' => 'Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.',
        'delete_this_document' => 'Supprimer ce document',
        
        // Upload multiple
        'single_document' => 'Document unique',
        'multiple_documents' => 'Plusieurs documents',
        'drop_pdfs_here' => 'Glissez vos fichiers PDF ici',
        'or_click_to_browse_multiple' => 'ou cliquez pour sélectionner plusieurs fichiers',
        'per_file' => 'par fichier',
        'files_selected' => 'fichier(s) sélectionné(s)',
        'clear_all' => 'Tout supprimer',
        'add_more_files' => 'Ajouter d\'autres fichiers',
        'common_options' => 'Options communes',
        'keep_default' => 'Conserver la valeur par défaut',
        'apply_to_all_files' => 'Appliqué à tous les fichiers',
        'use_filename_as_title' => 'Utiliser le nom du fichier comme titre',
        'upload_documents' => 'Uploader les documents',
        'uploading_documents' => 'Upload en cours...',
        'preparing' => 'Préparation',
        'uploading' => 'Upload de',
        'upload_complete' => 'Upload terminé !',
        'files_uploaded_successfully' => 'fichier(s) uploadé(s) avec succès',
        'upload_success' => 'Document uploadé avec succès',
        'upload_error' => 'Erreur lors de l\'upload',
        
        // Types de documents
        'type_invoice' => 'Facture',
        'type_quote' => 'Devis',
        'type_contract' => 'Contrat',
        'type_report' => 'Rapport',
        'type_other' => 'Autre',
        
        // ==========================================
        // ZONES & ANNOTATIONS
        // ==========================================
        'zones' => 'Zones',
        'zone' => 'Zone',
        'zones_count' => ':count zone(s)',
        'new_zone' => 'Nouvelle zone',
        'create_zone' => 'Créer une zone',
        'zone_name' => 'Nom de la zone',
        'zone_value' => 'Valeur extraite',
        'zone_type' => 'Type de zone',
        'zone_color' => 'Couleur',
        'zone_tooltip' => 'Info-bulle',
        'zone_page' => 'Page',
        'annotations' => 'Annotations',
        'annotation' => 'Annotation',
        'new_annotation' => 'Nouvelle annotation',
        'add_annotation' => 'Ajouter une annotation',
        'annotation_text' => 'Texte de l\'annotation',
        'annotation_placeholder' => 'Écrivez votre annotation...',
        'no_zones' => 'Aucune zone définie',
        'no_annotations' => 'Aucune annotation',
        'zone_saved' => 'Zone enregistrée.',
        'zone_deleted' => 'Zone supprimée.',
        'delete_zone_confirm' => 'Supprimer cette zone ?',
        'zones_list' => 'Liste des zones',
        
        // Types de zones
        'zone_text' => 'Texte',
        'zone_number' => 'Nombre',
        'zone_date' => 'Date',
        'zone_amount' => 'Montant',
        'zone_checkbox' => 'Case à cocher',
        
        // ==========================================
        // LIAISONS
        // ==========================================
        'links' => 'Liaisons',
        'link' => 'Liaison',
        'links_count' => ':count liaison(s)',
        'new_link' => 'Nouvelle liaison',
        'create_link' => 'Créer une liaison',
        'link_documents' => 'Lier des documents',
        'link_source' => 'Zone source',
        'link_target' => 'Zone cible',
        'link_type' => 'Type de liaison',
        'link_description' => 'Description',
        'linked_documents' => 'Documents liés',
        'no_links' => 'Aucune liaison',
        'link_created' => 'Liaison créée.',
        'link_deleted' => 'Liaison supprimée.',
        'points_to' => 'Pointe vers',
        'pointed_by' => 'Pointé par',
        
        // ==========================================
        // UTILISATEURS
        // ==========================================
        'users' => 'Utilisateurs',
        'user' => 'Utilisateur',
        'users_title' => 'Gestion des utilisateurs',
        'users_count' => ':count utilisateur(s)',
        'new_user' => 'Nouvel utilisateur',
        'edit_user' => 'Modifier l\'utilisateur',
        'user_role' => 'Rôle',
        'user_team' => 'Équipe',
        'user_status' => 'Statut',
        'user_active' => 'Actif',
        'user_inactive' => 'Inactif',
        'user_last_login' => 'Dernière connexion',
        'user_created_at' => 'Créé le',
        'delete_user_confirm' => 'Désactiver cet utilisateur ?',
        'user_deleted' => 'Utilisateur désactivé.',
        'user_created' => 'Utilisateur créé avec succès.',
        'user_updated' => 'Utilisateur mis à jour.',
        'never_connected' => 'Jamais connecté',
        
        // Formulaire utilisateur
        'back_to_users' => 'Retour aux utilisateurs',
        'personal_info' => 'Informations personnelles',
        'security' => 'Sécurité',
        'role_and_team' => 'Rôle et équipe',
        'username' => 'Nom d\'utilisateur',
        'username_hint' => 'Lettres, chiffres et underscores uniquement',
        'password_leave_empty' => 'Laissez le mot de passe vide pour ne pas le modifier',
        'password_min_chars' => 'Minimum 8 caractères',
        'confirm_password' => 'Confirmer le mot de passe',
        'role_member' => 'Membre',
        'role_admin' => 'Administrateur',
        'admin_access_hint' => 'Les administrateurs ont accès à la gestion des utilisateurs et équipes',
        'user_active_hint' => 'Les utilisateurs inactifs ne peuvent pas se connecter',
        'create_user' => 'Créer l\'utilisateur',
        'passwords_not_match' => 'Les mots de passe ne correspondent pas',
        
        // ==========================================
        // ÉQUIPES
        // ==========================================
        'teams' => 'Équipes',
        'team' => 'Équipe',
        'teams_title' => 'Gestion des équipes',
        'teams_count' => ':count équipe(s)',
        'new_team' => 'Nouvelle équipe',
        'add_team' => 'Ajouter une équipe',
        'edit_team' => 'Modifier l\'équipe',
        'team_name' => 'Nom de l\'équipe',
        'team_description' => 'Description',
        'team_color' => 'Couleur',
        'team_members' => 'Membres',
        'team_members_label' => 'MEMBRE(S)',
        'team_documents_label' => 'DOCUMENT(S)',
        'members' => 'membre(s)',
        'members_label' => 'MEMBRES',
        'no_team' => 'Aucune équipe',
        'no_members_in_team' => 'Aucun membre dans cette équipe',
        'create_first_team' => 'Créez votre première équipe pour commencer.',
        'delete_team_confirm' => 'Supprimer cette équipe ?',
        'team_created' => 'Équipe créée.',
        'team_updated' => 'Équipe mise à jour.',
        'team_deleted' => 'Équipe supprimée.',
        
        // ==========================================
        // RÔLES
        // ==========================================
        'roles' => 'Rôles',
        'role' => 'Rôle',
        'roles_title' => 'Gestion des rôles',
        'roles_count' => ':count rôle(s) configuré(s)',
        'new_role' => 'Nouveau rôle',
        'edit_role' => 'Modifier le rôle',
        'role_name' => 'Identifiant technique',
        'role_display_name' => 'Nom d\'affichage',
        'role_description' => 'Description',
        'role_color' => 'Couleur',
        'role_permissions' => 'Permissions',
        'role_system' => 'Rôle système',
        'role_users_count' => ':count utilisateur(s)',
        'role_users_label' => 'UTILISATEUR(S)',
        'select_all' => 'Tout cocher',
        'deselect_all' => 'Tout décocher',
        'delete_role_confirm' => 'Supprimer ce rôle ? Les utilisateurs seront basculés vers Membre.',
        'permissions' => 'Permissions',
        
        // Noms des rôles par défaut
        'role_admin' => 'Administrateur',
        'role_admin_desc' => 'Accès complet à toutes les fonctionnalités',
        'role_member' => 'Membre',
        'role_member_desc' => 'Accès standard aux documents',
        'role_viewer' => 'Lecteur',
        'role_viewer_desc' => 'Consultation uniquement',
        'role_manager' => 'Manager',
        'role_manager_desc' => 'Gestion des documents et de son équipe',
        
        // Catégories de permissions
        'perm_documents' => 'Documents',
        'perm_zones' => 'Zones & Annotations',
        'perm_links' => 'Liaisons',
        'perm_users' => 'Utilisateurs',
        'perm_teams' => 'Équipes',
        'perm_roles' => 'Rôles & Permissions',
        'perm_admin' => 'Administration',
        
        // ==========================================
        // ACTIVITÉ - RECENT ACTIVITY (NOUVEAU)
        // ==========================================
        'activity' => 'Activité',
        'recent_activity' => 'Activité récente',
        'no_recent_activity' => 'Aucune activité récente',
        'activity_log' => 'Journal d\'activité',
        'activity_view_all' => 'Voir toute l\'activité',
        
        // Descriptions d'activité traduites dynamiquement
        'activity_upload_document' => 'Upload du document: :name',
        'activity_delete_document' => 'Suppression du document: :name',
        'activity_update_document' => 'Modification du document',
        'activity_update_document_name' => 'Modification du document: :name',
        'activity_create_zone' => 'Création de zone sur le document #:id',
        'activity_update_zone' => 'Modification de zone',
        'activity_delete_zone' => 'Suppression de zone',
        'activity_create_link' => 'Création de liaison',
        'activity_delete_link' => 'Suppression de liaison',
        'activity_create_annotation' => 'Ajout d\'annotation',
        'activity_resolve_annotation' => 'Résolution d\'annotation',
        'activity_delete_annotation' => 'Suppression d\'annotation',
        'activity_login_success' => 'Connexion réussie',
        'activity_logout' => 'Déconnexion',
        'activity_create_user' => 'Création d\'utilisateur: :name',
        'activity_update_user' => 'Modification d\'utilisateur',
        'activity_deactivate_user' => 'Désactivation d\'utilisateur',
        'activity_create_team' => 'Création d\'équipe: :name',
        'activity_update_team' => 'Modification d\'équipe',
        'activity_delete_team' => 'Suppression d\'équipe',
        'activity_mention_sent' => 'Mention envoyée',
        
        // Actions d'activité (clés de traduction pour les descriptions)
        'activity_login' => 'Connexion',
        'activity_logout' => 'Déconnexion',
        'activity_upload' => 'Upload de document',
        'activity_delete' => 'Suppression',
        'activity_create_link' => 'Création de liaison',
        'activity_delete_link' => 'Suppression de liaison',
        'activity_create_zone' => 'Création de zone',
        'activity_delete_zone' => 'Suppression de zone',
        'activity_create_annotation' => 'Ajout d\'annotation',
        'activity_delete_annotation' => 'Suppression d\'annotation',
        'activity_resolve_annotation' => 'Résolution d\'annotation',
        'activity_update_profile' => 'Modification du profil',
        'activity_create_user' => 'Création d\'utilisateur',
        'activity_update_user' => 'Modification d\'utilisateur',
        'activity_delete_user' => 'Suppression d\'utilisateur',
        'activity_create_team' => 'Création d\'équipe',
        'activity_update_team' => 'Modification d\'équipe',
        'activity_delete_team' => 'Suppression d\'équipe',
        'activity_create_role' => 'Création de rôle',
        'activity_update_role' => 'Modification de rôle',
        'activity_delete_role' => 'Suppression de rôle',
        'activity_view_document' => 'Consultation de document',
        'activity_download_document' => 'Téléchargement de document',
        'activity_share_document' => 'Partage de document',
        'activity_mention_user' => 'Mention d\'utilisateur',
        
        // Descriptions détaillées avec paramètres
        'activity_login_desc' => ':user s\'est connecté',
        'activity_logout_desc' => ':user s\'est déconnecté',
        'activity_upload_desc' => ':user a uploadé le document ":document"',
        'activity_delete_desc' => ':user a supprimé ":item"',
        'activity_create_link_desc' => ':user a créé une liaison vers ":target"',
        'activity_delete_link_desc' => ':user a supprimé une liaison',
        'activity_create_zone_desc' => ':user a créé la zone ":zone" sur ":document"',
        'activity_create_annotation_desc' => ':user a ajouté une annotation sur ":document"',
        'activity_resolve_annotation_desc' => ':user a résolu une annotation',
        'activity_create_user_desc' => ':user a créé l\'utilisateur ":target_user"',
        'activity_update_user_desc' => ':user a modifié l\'utilisateur ":target_user"',
        'activity_create_team_desc' => ':user a créé l\'équipe ":team"',
        'activity_view_document_desc' => ':user a consulté le document ":document"',
        'activity_download_document_desc' => ':user a téléchargé le document ":document"',
        'activity_mention_user_desc' => ':user a mentionné :target_user sur une zone',
        
        // Entités
        'entity_document' => 'Document',
        'entity_user' => 'Utilisateur',
        'entity_team' => 'Équipe',
        'entity_role' => 'Rôle',
        'entity_zone' => 'Zone',
        'entity_link' => 'Liaison',
        'entity_annotation' => 'Annotation',
        
        // ==========================================
        // CHAT
        // ==========================================
        'chat' => 'Chat',
        'chat_title' => 'Chat d\'équipe',
        'chat_open' => 'Ouvrir le chat',
        'chat_close' => 'Fermer le chat',
        'chat_general' => 'Général',
        'chat_placeholder' => 'Écrivez un message...',
        'chat_send' => 'Envoyer',
        'chat_online' => ':count en ligne',
        'chat_no_messages' => 'Aucun message. Soyez le premier à écrire !',
        'chat_loading' => 'Chargement des messages...',
        'chat_reply_to' => 'Réponse à',
        'chat_reply' => 'Répondre',
        'chat_delete' => 'Supprimer',
        'chat_cancel_reply' => 'Annuler la réponse',
        'chat_delete_confirm' => 'Supprimer ce message ?',
        'chat_message_deleted' => 'Message supprimé',
        'chat_error_send' => 'Erreur lors de l\'envoi',
        'chat_error_load' => 'Erreur lors du chargement',
        'chat_error_delete' => 'Erreur lors de la suppression',
        'chat_channel_general' => '💬 Général',
        'chat_channel_team' => '👥 Mon équipe',
        'chat_channel_document' => '📄 Document',
        'chat_you' => 'Vous',
        'chat_typing' => ':name est en train d\'écrire...',
        'chat_new_messages' => ':count nouveau(x) message(s)',
        
        // ==========================================
        // TEMPS
        // ==========================================
        'this_month' => 'ce mois',
        'today' => 'Aujourd\'hui',
        'yesterday' => 'Hier',
        'time_now' => 'À l\'instant',
        'time_minutes' => 'Il y a :count min',
        'time_hours' => 'Il y a :counth',
        'time_days' => 'Il y a :count jour(s)',
        'time_months' => 'Il y a :count mois',
        'time_years' => 'Il y a :count an(s)',
        'time_ago' => 'il y a',
        
        // ==========================================
        // RÉINITIALISATION COMPLÈTE
        // ==========================================
        'reset_all_btn' => 'Tout supprimer',
        'reset_session_info' => 'Fin de séance de travail ?',
        'reset_all_title' => 'Réinitialisation complète',
        'reset_warning_title' => 'Action irréversible',
        'reset_warning_irreversible' => 'Cette action est définitive et ne peut pas être annulée. Toutes les données seront perdues.',
        'reset_what_deleted' => 'Les éléments suivants seront supprimés :',
        'reset_all_documents' => 'Tous les fichiers PDF et leurs métadonnées',
        'reset_all_zones' => 'Toutes les zones définies sur les documents',
        'reset_all_annotations' => 'Toutes les annotations et commentaires',
        'reset_all_links' => 'Toutes les liaisons entre documents',
        'reset_all_activity' => 'L\'historique complet des activités',
        'reset_all_chat' => 'Tous les messages du chat collaboratif',
        'reset_when_use' => 'Quand utiliser cette fonction ?',
        'reset_when_use_desc' => 'Cette fonction est prévue pour réinitialiser l\'espace de travail lorsqu\'une séance de travail sur les documents est terminée et que vous souhaitez repartir à zéro.',
        'reset_all_users_warning' => 'Attention : Tous les utilisateurs concernés',
        'reset_all_users_desc' => 'Cette suppression affecte TOUS les utilisateurs de la plateforme, pas seulement votre compte.',
        'reset_confirm_label' => 'Pour confirmer, tapez :',
        'reset_confirm_placeholder' => 'Tapez le code de confirmation',
        'reset_confirm_btn' => 'Supprimer définitivement',
        'reset_invalid_code' => 'Code de confirmation invalide. Veuillez saisir exactement ERASE-ALL',
        'reset_final_confirm' => 'DERNIÈRE CONFIRMATION : Êtes-vous absolument certain de vouloir supprimer TOUTES les données ? Cette action est IRRÉVERSIBLE.',
        'reset_success' => 'Réinitialisation terminée. Toutes les données ont été supprimées.',
        'reset_error' => 'Une erreur est survenue lors de la réinitialisation.',
        'activity_reset_all' => 'Réinitialisation complète du système',
        
        // ==========================================
        // MESSAGES FLASH
        // ==========================================
        'success' => 'Succès',
        'error' => 'Erreur',
        'warning' => 'Attention',
        'info' => 'Information',
        'session_expired' => 'Session expirée.',
        'changes_saved' => 'Modifications enregistrées.',
        'operation_success' => 'Opération réussie.',
        'operation_failed' => 'Opération échouée.',
        
        // ==========================================
        // PROFIL
        // ==========================================
        'profile' => 'Profil',
        'profile_title' => 'Mon profil',
        'profile_subtitle' => 'Gérez vos informations personnelles et vos paramètres',
        'profile_edit' => 'Modifier mon profil',
        'profile_password' => 'Changer le mot de passe',
        'current_password' => 'Mot de passe actuel',
        'new_password' => 'Nouveau mot de passe',
        'profile_updated' => 'Profil mis à jour.',
        'password_updated' => 'Mot de passe mis à jour.',
        'password_incorrect' => 'Le mot de passe actuel est incorrect.',
        'personal_info' => 'Informations personnelles',
        'security' => 'Sécurité',
        'member_since' => 'Membre depuis',
        'last_login' => 'Dernière connexion',
        'username_readonly' => 'Le nom d\'utilisateur ne peut pas être modifié',
        'notif_new_documents' => 'Nouveaux documents',
        'notif_new_documents_desc' => 'Recevoir une notification quand un document est ajouté',
        'notif_annotations_desc' => 'Recevoir une notification pour les nouvelles annotations',
        'notif_links_desc' => 'Recevoir une notification quand une liaison est créée vers vos documents',
        
        // ==========================================
        // NOTIFICATIONS
        // ==========================================
        'notifications' => 'Notifications',
        'mark_all_read' => 'Tout marquer comme lu',
        'no_notifications' => 'Aucune notification',
        'notification_mention' => ':user vous a mentionné sur une zone',
        'notification_annotation' => ':user a commenté un document',
        'notification_link' => ':user a créé une liaison',
        
        // Titres de notifications (pour traduction dynamique)
        'notif_title_new_link' => 'Nouvelle liaison',
        'notif_title_new_document' => 'Nouveau document',
        'notif_title_new_annotation' => 'Nouvelle annotation',
        'notif_title_mention' => 'Mention',
        'notif_title_document_updated' => 'Document mis à jour',
        'notif_title_system_reset' => 'Réinitialisation système',
        
        // Descriptions de notifications (patterns pour traduction)
        'notif_desc_created_link' => 'a créé une liaison entre documents',
        'notif_desc_added_document' => 'a ajouté un nouveau document',
        'notif_desc_added_annotation' => 'a ajouté une annotation',
        'notif_desc_mentioned_you' => 'vous a mentionné',
        'notif_desc_updated_document' => 'a mis à jour un document',
        
        // ==========================================
        // SYNCHRONISATION
        // ==========================================
        'sync_updated' => 'Document mis à jour par un collaborateur',
        'viewers_count' => ':count personne(s) sur ce document',
        'online' => 'en ligne',
        'you_suffix' => ' (vous)',
        'just_now' => 'À l\'instant',
        'user' => 'Utilisateur',
        
        // ==========================================
        // STOCKAGE
        // ==========================================
        'storage_used' => 'Stockage utilisé',
        
        // ==========================================
        // UPLOAD
        // ==========================================
        'upload' => 'Upload',
        'drag_drop' => 'Glissez-déposez ou cliquez pour sélectionner',
        'max_size' => 'Taille max : :size',
        'allowed_formats' => 'Formats autorisés : :formats',
        
        // ==========================================
        // RECHERCHE
        // ==========================================
        'search_results' => 'Résultats de recherche',
        'search_no_results' => 'Aucun résultat trouvé',
        'search_for' => 'Recherche de ":query"',
        'results_count' => 'résultat(s) pour',
        'search_placeholder' => 'Rechercher des documents, du contenu...',
        'search_button' => 'Rechercher',
        'search_in_ocr' => 'Rechercher dans le contenu OCR',
        'no_results' => 'Aucun résultat',
        'no_results_for' => 'Aucun document ne correspond à votre recherche',
        'search_tips_title' => 'Conseils de recherche :',
        'search_tip_1' => 'Vérifiez l\'orthographe des termes',
        'search_tip_2' => 'Essayez des mots-clés plus généraux',
        'search_tip_3' => 'Activez la recherche dans le contenu OCR pour rechercher dans le texte des documents',
        'ref' => 'Réf',
        'page' => 'Page',
        'of' => 'sur',
        
        // ==========================================
        // ERREURS
        // ==========================================
        'error_404' => 'Page non trouvée',
        'error_403' => 'Accès refusé',
        'error_500' => 'Erreur serveur',
        'go_home' => 'Retour à l\'accueil',
        
        // ==========================================
        // FORMULAIRES
        // ==========================================
        'select_option' => 'Sélectionnez une option',
        'select_team' => 'Sélectionnez une équipe',
        'select_role' => 'Sélectionnez un rôle',
        'select_type' => 'Sélectionnez un type',
        
        // ==========================================
        // DASHBOARD - CLÉS MANQUANTES
        // ==========================================
        'recent_documents' => 'Documents récents',
        'recent_links' => 'Liaisons récentes',
        'mapping_active' => 'Mapping actif',
        'unresolved' => 'non résolu(s)',
        'no_links' => 'Aucune liaison',
        'no_team' => 'Aucune équipe',
        'members' => 'membre(s)',
        'links' => 'Liaisons',
        
        // ==========================================
        // ACTIVITY PAGE - FILTRES
        // ==========================================
        'activity_log_subtitle' => 'Historique des actions sur la plateforme',
        'all_actions' => 'Toutes les actions',
        'all_users' => 'Tous les utilisateurs',
        'filter' => 'Filtrer',
        'action_create' => 'Création',
        'action_update' => 'Modification',
        'action_delete' => 'Suppression',
        'action_view' => 'Consultation',
        'action_upload' => 'Import',
        'action_download' => 'Téléchargement',
        'action_login' => 'Connexion',
        'action_logout' => 'Déconnexion',
    ],
    
    // ==========================================
    // ENGLISH TRANSLATIONS
    // ==========================================
    'en' => [
        // ==========================================
        // GENERAL
        // ==========================================
        'app_name' => 'DocuFlow',
        'app_description' => 'Collaborative document management',
        'welcome' => 'Welcome',
        'home' => 'Home',
        'dashboard' => 'Dashboard',
        'search' => 'Search...',
        'search_documents' => 'Search documents...',
        'loading' => 'Loading...',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'create' => 'Create',
        'add' => 'Add',
        'view' => 'View',
        'view_all' => 'View all',
        'back' => 'Back',
        'close' => 'Close',
        'confirm' => 'Confirm',
        'yes' => 'Yes',
        'no' => 'No',
        'or' => 'or',
        'and' => 'and',
        'actions' => 'Actions',
        'settings' => 'Settings',
        'language' => 'Language',
        'french' => 'French',
        'english' => 'English',
        'other' => 'Other',
        'system' => 'System',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'all' => 'All',
        'none' => 'None',
        'required' => 'Required',
        'optional' => 'Optional',
        
        // ==========================================
        // NAVIGATION
        // ==========================================
        'nav_documents' => 'Documents',
        'nav_users' => 'Users',
        'nav_teams' => 'Teams',
        'nav_roles' => 'Roles',
        'nav_profile' => 'My profile',
        'nav_logout' => 'Logout',
        'nav_administration' => 'Administration',
        'nav_activity' => 'Activity',
        
        // ==========================================
        // AUTHENTICATION
        // ==========================================
        'login' => 'Login',
        'login_title' => 'Login',
        'login_subtitle' => 'Sign in to access your documents',
        'login_button' => 'Sign in',
        'register' => 'Register',
        'register_title' => 'Create an account',
        'register_subtitle' => 'Join DocuFlow to manage your documents',
        'register_button' => 'Create account',
        'email' => 'Email address',
        'password' => 'Password',
        'password_confirm' => 'Confirm password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'no_account' => 'No account yet?',
        'have_account' => 'Already have an account?',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'logout_success' => 'You have been logged out.',
        'register_success' => 'Account created successfully! You can now login.',
        'login_error' => 'Invalid email or password.',
        'invalid_credentials' => 'Invalid email or password.',
        'account_deactivated' => 'Account deactivated. Contact an administrator.',
        
        // ==========================================
        // DOCUMENTS
        // ==========================================
        'documents' => 'Documents',
        'document' => 'Document',
        'documents_title' => 'My documents',
        'documents_count' => ':count document(s)',
        'upload_document' => 'Upload a document',
        'new_document' => 'New document',
        'upload_file' => 'Upload file',
        'document_title' => 'Document title',
        'document_name' => 'Document name',
        'document_description' => 'Description',
        'document_file' => 'PDF file',
        'document_category' => 'Category',
        'document_type' => 'Type',
        'document_date' => 'Date',
        'document_uploaded_by' => 'Uploaded by',
        'document_uploaded_at' => 'Uploaded on',
        'document_size' => 'Size',
        'document_pages' => 'Pages',
        'document_actions' => 'Actions',
        'document_preview' => 'Preview',
        'document_download' => 'Download',
        'document_delete' => 'Delete',
        'document_share' => 'Share',
        'no_documents' => 'No documents',
        'no_documents_desc' => 'Upload your first document to get started.',
        'drop_files' => 'Drag and drop your files here',
        'browse_files' => 'Browse',
        'delete_document_confirm' => 'Delete this document?',
        'document_deleted' => 'Document deleted.',
        'document_created' => 'Document created successfully.',
        'document_uploaded' => 'Document uploaded successfully.',
        'document_processing' => 'Processing...',
        'upload_first_document' => 'Upload your first document to get started.',
        'all_types' => 'All types',
        'all_teams' => 'All teams',
        'date_format_placeholder' => 'mm/dd/yyyy',
        
        // Document creation page
        'back_to_documents' => 'Back to documents',
        'drop_pdf_here' => 'Drop your PDF file here',
        'or_click_to_browse' => 'or click to browse',
        'pdf_only' => 'PDF only',
        'max_size' => 'Max',
        'informations' => 'Information',
        'document_title_placeholder' => 'Ex: Supplier Invoice March 2024',
        'document_description_placeholder' => 'Optional document description...',
        'reference_number' => 'Reference number',
        'reference_placeholder' => 'Ex: INV-2024-001',
        'amount' => 'Amount',
        'currency' => 'Currency',
        'attribution' => 'Attribution',
        'team_visibility_hint' => 'The document will be visible to everyone, but can be associated with a team',
        'pdf_only_alert' => 'Only PDF files are allowed.',
        'file_too_large' => 'The file is too large.',
        
        // Document edit page
        'edit_document' => 'Edit document',
        'back_to_document' => 'Back to document',
        'uploaded_on' => 'Uploaded on',
        'view_pdf' => 'View PDF',
        'danger_zone' => 'Danger zone',
        'delete_document_warning' => 'Deleting a document is irreversible. All associated zones, links and annotations will also be deleted.',
        'delete_document_confirm_full' => 'Are you sure you want to delete this document? This action cannot be undone.',
        'delete_this_document' => 'Delete this document',
        
        // Multiple upload
        'single_document' => 'Single document',
        'multiple_documents' => 'Multiple documents',
        'drop_pdfs_here' => 'Drop your PDF files here',
        'or_click_to_browse_multiple' => 'or click to select multiple files',
        'per_file' => 'per file',
        'files_selected' => 'file(s) selected',
        'clear_all' => 'Clear all',
        'add_more_files' => 'Add more files',
        'common_options' => 'Common options',
        'keep_default' => 'Keep default value',
        'apply_to_all_files' => 'Applied to all files',
        'use_filename_as_title' => 'Use filename as title',
        'upload_documents' => 'Upload documents',
        'uploading_documents' => 'Uploading...',
        'preparing' => 'Preparing',
        'uploading' => 'Uploading',
        'upload_complete' => 'Upload complete!',
        'files_uploaded_successfully' => 'file(s) uploaded successfully',
        'upload_success' => 'Document uploaded successfully',
        'upload_error' => 'Upload error',
        
        // Document types
        'type_invoice' => 'Invoice',
        'type_quote' => 'Quote',
        'type_contract' => 'Contract',
        'type_report' => 'Report',
        'type_other' => 'Other',
        
        // ==========================================
        // ZONES & ANNOTATIONS
        // ==========================================
        'zones' => 'Zones',
        'zone' => 'Zone',
        'zones_count' => ':count zone(s)',
        'new_zone' => 'New zone',
        'create_zone' => 'Create a zone',
        'zone_name' => 'Zone name',
        'zone_value' => 'Extracted value',
        'zone_type' => 'Zone type',
        'zone_color' => 'Color',
        'zone_tooltip' => 'Tooltip',
        'zone_page' => 'Page',
        'annotations' => 'Annotations',
        'annotation' => 'Annotation',
        'new_annotation' => 'New annotation',
        'add_annotation' => 'Add an annotation',
        'annotation_text' => 'Annotation text',
        'annotation_placeholder' => 'Write your annotation...',
        'no_zones' => 'No zones defined',
        'no_annotations' => 'No annotations',
        'zone_saved' => 'Zone saved.',
        'zone_deleted' => 'Zone deleted.',
        'delete_zone_confirm' => 'Delete this zone?',
        'zones_list' => 'Zones list',
        
        // Zone types
        'zone_text' => 'Text',
        'zone_number' => 'Number',
        'zone_date' => 'Date',
        'zone_amount' => 'Amount',
        'zone_checkbox' => 'Checkbox',
        
        // ==========================================
        // LINKS
        // ==========================================
        'links' => 'Links',
        'link' => 'Link',
        'links_count' => ':count link(s)',
        'new_link' => 'New link',
        'create_link' => 'Create a link',
        'link_documents' => 'Link documents',
        'link_source' => 'Source zone',
        'link_target' => 'Target zone',
        'link_type' => 'Link type',
        'link_description' => 'Description',
        'linked_documents' => 'Linked documents',
        'no_links' => 'No links',
        'link_created' => 'Link created.',
        'link_deleted' => 'Link deleted.',
        'points_to' => 'Points to',
        'pointed_by' => 'Pointed by',
        
        // ==========================================
        // USERS
        // ==========================================
        'users' => 'Users',
        'user' => 'User',
        'users_title' => 'User management',
        'users_count' => ':count user(s)',
        'new_user' => 'New user',
        'edit_user' => 'Edit user',
        'user_role' => 'Role',
        'user_team' => 'Team',
        'user_status' => 'Status',
        'user_active' => 'Active',
        'user_inactive' => 'Inactive',
        'user_last_login' => 'Last login',
        'user_created_at' => 'Created on',
        'delete_user_confirm' => 'Deactivate this user?',
        'user_deleted' => 'User deactivated.',
        'user_created' => 'User created successfully.',
        'user_updated' => 'User updated.',
        'never_connected' => 'Never connected',
        
        // User form
        'back_to_users' => 'Back to users',
        'personal_info' => 'Personal information',
        'security' => 'Security',
        'role_and_team' => 'Role and team',
        'username' => 'Username',
        'username_hint' => 'Letters, numbers and underscores only',
        'password_leave_empty' => 'Leave password empty to keep current',
        'password_min_chars' => 'Minimum 8 characters',
        'confirm_password' => 'Confirm password',
        'role_member' => 'Member',
        'role_admin' => 'Administrator',
        'admin_access_hint' => 'Administrators have access to user and team management',
        'user_active_hint' => 'Inactive users cannot log in',
        'create_user' => 'Create user',
        'passwords_not_match' => 'Passwords do not match',
        
        // ==========================================
        // TEAMS
        // ==========================================
        'teams' => 'Teams',
        'team' => 'Team',
        'teams_title' => 'Team management',
        'teams_count' => ':count team(s)',
        'new_team' => 'New team',
        'add_team' => 'Add a team',
        'edit_team' => 'Edit team',
        'team_name' => 'Team name',
        'team_description' => 'Description',
        'team_color' => 'Color',
        'team_members' => 'Members',
        'team_members_label' => 'MEMBER(S)',
        'team_documents_label' => 'DOCUMENT(S)',
        'members' => 'member(s)',
        'members_label' => 'MEMBERS',
        'no_team' => 'No team',
        'no_members_in_team' => 'No members in this team',
        'create_first_team' => 'Create your first team to get started.',
        'delete_team_confirm' => 'Delete this team?',
        'team_created' => 'Team created.',
        'team_updated' => 'Team updated.',
        'team_deleted' => 'Team deleted.',
        
        // ==========================================
        // ROLES
        // ==========================================
        'roles' => 'Roles',
        'role' => 'Role',
        'roles_title' => 'Role management',
        'roles_count' => ':count role(s) configured',
        'new_role' => 'New role',
        'edit_role' => 'Edit role',
        'role_name' => 'Technical identifier',
        'role_display_name' => 'Display name',
        'role_description' => 'Description',
        'role_color' => 'Color',
        'role_permissions' => 'Permissions',
        'role_system' => 'System role',
        'role_users_count' => ':count user(s)',
        'role_users_label' => 'USER(S)',
        'select_all' => 'Select all',
        'deselect_all' => 'Deselect all',
        'delete_role_confirm' => 'Delete this role? Users will be switched to Member.',
        'permissions' => 'Permissions',
        
        // Default role names
        'role_admin' => 'Administrator',
        'role_admin_desc' => 'Full access to all features',
        'role_member' => 'Member',
        'role_member_desc' => 'Standard access to documents',
        'role_viewer' => 'Viewer',
        'role_viewer_desc' => 'View only',
        'role_manager' => 'Manager',
        'role_manager_desc' => 'Manage documents and team',
        
        // Permission categories
        'perm_documents' => 'Documents',
        'perm_zones' => 'Zones & Annotations',
        'perm_links' => 'Links',
        'perm_users' => 'Users',
        'perm_teams' => 'Teams',
        'perm_roles' => 'Roles & Permissions',
        'perm_admin' => 'Administration',
        
        // ==========================================
        // ACTIVITY - RECENT ACTIVITY (NEW)
        // ==========================================
        'activity' => 'Activity',
        'recent_activity' => 'Recent activity',
        'no_recent_activity' => 'No recent activity',
        'activity_log' => 'Activity log',
        'activity_view_all' => 'View all activity',
        
        // Activity descriptions - dynamically translated
        'activity_upload_document' => 'Document upload: :name',
        'activity_delete_document' => 'Document deleted: :name',
        'activity_update_document' => 'Document updated',
        'activity_update_document_name' => 'Document updated: :name',
        'activity_create_zone' => 'Zone creation on document #:id',
        'activity_update_zone' => 'Zone updated',
        'activity_delete_zone' => 'Zone deleted',
        'activity_create_link' => 'Link created',
        'activity_delete_link' => 'Link deleted',
        'activity_create_annotation' => 'Annotation added',
        'activity_resolve_annotation' => 'Annotation resolved',
        'activity_delete_annotation' => 'Annotation deleted',
        'activity_login_success' => 'Login successful',
        'activity_logout' => 'Logout',
        'activity_create_user' => 'User created: :name',
        'activity_update_user' => 'User updated',
        'activity_deactivate_user' => 'User deactivated',
        'activity_create_team' => 'Team created: :name',
        'activity_update_team' => 'Team updated',
        'activity_delete_team' => 'Team deleted',
        'activity_mention_sent' => 'Mention sent',
        
        // Activity actions (translation keys for descriptions)
        'activity_login' => 'Login',
        'activity_upload' => 'Document upload',
        'activity_delete' => 'Deletion',
        'activity_delete_team' => 'Team deleted',
        'activity_create_role' => 'Role created',
        'activity_update_role' => 'Role updated',
        'activity_delete_role' => 'Role deleted',
        'activity_view_document' => 'Document viewed',
        'activity_download_document' => 'Document downloaded',
        'activity_share_document' => 'Document shared',
        'activity_mention_user' => 'User mentioned',
        
        // Detailed descriptions with parameters
        'activity_login_desc' => ':user logged in',
        'activity_logout_desc' => ':user logged out',
        'activity_upload_desc' => ':user uploaded document ":document"',
        'activity_delete_desc' => ':user deleted ":item"',
        'activity_create_link_desc' => ':user created a link to ":target"',
        'activity_delete_link_desc' => ':user deleted a link',
        'activity_create_zone_desc' => ':user created zone ":zone" on ":document"',
        'activity_create_annotation_desc' => ':user added an annotation on ":document"',
        'activity_resolve_annotation_desc' => ':user resolved an annotation',
        'activity_create_user_desc' => ':user created user ":target_user"',
        'activity_update_user_desc' => ':user updated user ":target_user"',
        'activity_create_team_desc' => ':user created team ":team"',
        'activity_view_document_desc' => ':user viewed document ":document"',
        'activity_download_document_desc' => ':user downloaded document ":document"',
        'activity_mention_user_desc' => ':user mentioned :target_user on a zone',
        
        // Entities
        'entity_document' => 'Document',
        'entity_user' => 'User',
        'entity_team' => 'Team',
        'entity_role' => 'Role',
        'entity_zone' => 'Zone',
        'entity_link' => 'Link',
        'entity_annotation' => 'Annotation',
        
        // ==========================================
        // CHAT
        // ==========================================
        'chat' => 'Chat',
        'chat_title' => 'Team Chat',
        'chat_open' => 'Open chat',
        'chat_close' => 'Close chat',
        'chat_general' => 'General',
        'chat_placeholder' => 'Write a message...',
        'chat_send' => 'Send',
        'chat_online' => ':count online',
        'chat_no_messages' => 'No messages. Be the first to write!',
        'chat_loading' => 'Loading messages...',
        'chat_reply_to' => 'Reply to',
        'chat_reply' => 'Reply',
        'chat_delete' => 'Delete',
        'chat_cancel_reply' => 'Cancel reply',
        'chat_delete_confirm' => 'Delete this message?',
        'chat_message_deleted' => 'Message deleted',
        'chat_error_send' => 'Error sending message',
        'chat_error_load' => 'Error loading messages',
        'chat_error_delete' => 'Error deleting message',
        'chat_channel_general' => '💬 General',
        'chat_channel_team' => '👥 My team',
        'chat_channel_document' => '📄 Document',
        'chat_you' => 'You',
        'chat_typing' => ':name is typing...',
        'chat_new_messages' => ':count new message(s)',
        
        // ==========================================
        // TIME
        // ==========================================
        'this_month' => 'this month',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'time_now' => 'Just now',
        'time_minutes' => ':count min ago',
        'time_hours' => ':counth ago',
        'time_days' => ':count day(s) ago',
        'time_months' => ':count month(s) ago',
        'time_years' => ':count year(s) ago',
        'time_ago' => 'ago',
        
        // ==========================================
        // FULL RESET
        // ==========================================
        'reset_all_btn' => 'Delete all',
        'reset_session_info' => 'End of work session?',
        'reset_all_title' => 'Full reset',
        'reset_warning_title' => 'Irreversible action',
        'reset_warning_irreversible' => 'This action is permanent and cannot be undone. All data will be lost.',
        'reset_what_deleted' => 'The following items will be deleted:',
        'reset_all_documents' => 'All PDF files and their metadata',
        'reset_all_zones' => 'All zones defined on documents',
        'reset_all_annotations' => 'All annotations and comments',
        'reset_all_links' => 'All links between documents',
        'reset_all_activity' => 'The complete activity history',
        'reset_all_chat' => 'All collaborative chat messages',
        'reset_when_use' => 'When to use this feature?',
        'reset_when_use_desc' => 'This feature is intended to reset the workspace when a document work session is finished and you want to start fresh.',
        'reset_all_users_warning' => 'Warning: All users affected',
        'reset_all_users_desc' => 'This deletion affects ALL users of the platform, not just your account.',
        'reset_confirm_label' => 'To confirm, type:',
        'reset_confirm_placeholder' => 'Type the confirmation code',
        'reset_confirm_btn' => 'Delete permanently',
        'reset_invalid_code' => 'Invalid confirmation code. Please type exactly ERASE-ALL',
        'reset_final_confirm' => 'FINAL CONFIRMATION: Are you absolutely sure you want to delete ALL data? This action is IRREVERSIBLE.',
        'reset_success' => 'Reset complete. All data has been deleted.',
        'reset_error' => 'An error occurred during reset.',
        'activity_reset_all' => 'Full system reset',
        
        // ==========================================
        // FLASH MESSAGES
        // ==========================================
        'success' => 'Success',
        'error' => 'Error',
        'warning' => 'Warning',
        'info' => 'Information',
        'session_expired' => 'Session expired.',
        'changes_saved' => 'Changes saved.',
        'operation_success' => 'Operation successful.',
        'operation_failed' => 'Operation failed.',
        
        // ==========================================
        // PROFILE
        // ==========================================
        'profile' => 'Profile',
        'profile_title' => 'My profile',
        'profile_subtitle' => 'Manage your personal information and settings',
        'profile_edit' => 'Edit my profile',
        'profile_password' => 'Change password',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'profile_updated' => 'Profile updated.',
        'password_updated' => 'Password updated.',
        'password_incorrect' => 'Current password is incorrect.',
        'personal_info' => 'Personal information',
        'security' => 'Security',
        'member_since' => 'Member since',
        'last_login' => 'Last login',
        'username_readonly' => 'Username cannot be changed',
        'notif_new_documents' => 'New documents',
        'notif_new_documents_desc' => 'Get notified when a document is added',
        'notif_annotations_desc' => 'Get notified for new annotations',
        'notif_links_desc' => 'Get notified when a link is created to your documents',
        
        // ==========================================
        // NOTIFICATIONS
        // ==========================================
        'notifications' => 'Notifications',
        'mark_all_read' => 'Mark all as read',
        'no_notifications' => 'No notifications',
        'notification_mention' => ':user mentioned you on a zone',
        'notification_annotation' => ':user commented on a document',
        'notification_link' => ':user created a link',
        
        // Notification titles (for dynamic translation)
        'notif_title_new_link' => 'New link',
        'notif_title_new_document' => 'New document',
        'notif_title_new_annotation' => 'New annotation',
        'notif_title_mention' => 'Mention',
        'notif_title_document_updated' => 'Document updated',
        'notif_title_system_reset' => 'System reset',
        
        // Notification descriptions (patterns for translation)
        'notif_desc_created_link' => 'created a link between documents',
        'notif_desc_added_document' => 'added a new document',
        'notif_desc_added_annotation' => 'added an annotation',
        'notif_desc_mentioned_you' => 'mentioned you',
        'notif_desc_updated_document' => 'updated a document',
        
        // ==========================================
        // SYNC
        // ==========================================
        'sync_updated' => 'Document updated by a collaborator',
        'viewers_count' => ':count person(s) on this document',
        'online' => 'online',
        'you_suffix' => ' (you)',
        'just_now' => 'Just now',
        'user' => 'User',
        
        // ==========================================
        // STORAGE
        // ==========================================
        'storage_used' => 'Storage used',
        
        // ==========================================
        // UPLOAD
        // ==========================================
        'upload' => 'Upload',
        'drag_drop' => 'Drag and drop or click to select',
        'max_size' => 'Max size: :size',
        'allowed_formats' => 'Allowed formats: :formats',
        
        // ==========================================
        // SEARCH
        // ==========================================
        'search_results' => 'Search results',
        'search_no_results' => 'No results found',
        'search_for' => 'Search for ":query"',
        'results_count' => 'result(s) for',
        'search_placeholder' => 'Search documents, content...',
        'search_button' => 'Search',
        'search_in_ocr' => 'Search in OCR content',
        'no_results' => 'No results',
        'no_results_for' => 'No document matches your search',
        'search_tips_title' => 'Search tips:',
        'search_tip_1' => 'Check the spelling of the terms',
        'search_tip_2' => 'Try more general keywords',
        'search_tip_3' => 'Enable OCR content search to search within document text',
        'ref' => 'Ref',
        'page' => 'Page',
        'of' => 'of',
        
        // ==========================================
        // ERRORS
        // ==========================================
        'error_404' => 'Page not found',
        'error_403' => 'Access denied',
        'error_500' => 'Server error',
        'go_home' => 'Go home',
        
        // ==========================================
        // FORMS
        // ==========================================
        'select_option' => 'Select an option',
        'select_team' => 'Select a team',
        'select_role' => 'Select a role',
        'select_type' => 'Select a type',
        
        // ==========================================
        // DASHBOARD - MISSING KEYS
        // ==========================================
        'recent_documents' => 'Recent documents',
        'recent_links' => 'Recent links',
        'mapping_active' => 'Active mapping',
        'unresolved' => 'unresolved',
        'no_links' => 'No links',
        'no_team' => 'No team',
        'members' => 'member(s)',
        'links' => 'Links',
        
        // ==========================================
        // ACTIVITY PAGE - FILTERS
        // ==========================================
        'activity_log_subtitle' => 'Platform action history',
        'all_actions' => 'All actions',
        'all_users' => 'All users',
        'filter' => 'Filter',
        'action_create' => 'Creation',
        'action_update' => 'Update',
        'action_delete' => 'Deletion',
        'action_view' => 'View',
        'action_upload' => 'Upload',
        'action_download' => 'Download',
        'action_login' => 'Login',
        'action_logout' => 'Logout',
    ],
];
