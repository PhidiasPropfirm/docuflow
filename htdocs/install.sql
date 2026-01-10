-- =====================================================
-- DocuFlow - Script d'installation de la base de données
-- Portail collaboratif de gestion documentaire
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Table des équipes
CREATE TABLE IF NOT EXISTS `teams` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `color` VARCHAR(7) DEFAULT '#3B82F6',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `team_id` INT UNSIGNED,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'member') DEFAULT 'member',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `last_login` TIMESTAMP NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des documents
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `team_id` INT UNSIGNED,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) DEFAULT 'application/pdf',
    `page_count` INT UNSIGNED DEFAULT 0,
    `document_type` ENUM('report', 'invoice', 'receipt', 'contract', 'other') DEFAULT 'other',
    `reference_number` VARCHAR(100),
    `document_date` DATE,
    `total_amount` DECIMAL(15,2),
    `currency` VARCHAR(3) DEFAULT 'EUR',
    `is_ocr_processed` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE SET NULL,
    INDEX `idx_document_type` (`document_type`),
    INDEX `idx_reference` (`reference_number`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table du contenu texte extrait (pour recherche full-text)
CREATE TABLE IF NOT EXISTS `document_content` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT UNSIGNED NOT NULL,
    `page_number` INT UNSIGNED NOT NULL,
    `content` LONGTEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
    FULLTEXT INDEX `idx_fulltext_content` (`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des zones sélectionnées dans les documents
CREATE TABLE IF NOT EXISTS `document_zones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT UNSIGNED NOT NULL,
    `page_number` INT UNSIGNED NOT NULL,
    `x` FLOAT NOT NULL,
    `y` FLOAT NOT NULL,
    `width` FLOAT NOT NULL,
    `height` FLOAT NOT NULL,
    `label` VARCHAR(255),
    `extracted_text` TEXT,
    `zone_type` ENUM('line', 'amount', 'reference', 'custom') DEFAULT 'custom',
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des liaisons entre documents (mapping)
CREATE TABLE IF NOT EXISTS `document_links` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `source_zone_id` INT UNSIGNED NOT NULL,
    `target_document_id` INT UNSIGNED NOT NULL,
    `target_zone_id` INT UNSIGNED,
    `link_type` ENUM('reference', 'justification', 'annexe', 'related') DEFAULT 'reference',
    `description` TEXT,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`source_zone_id`) REFERENCES `document_zones`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`target_document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`target_zone_id`) REFERENCES `document_zones`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_source_zone` (`source_zone_id`),
    INDEX `idx_target_doc` (`target_document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des annotations
CREATE TABLE IF NOT EXISTS `annotations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT UNSIGNED NOT NULL,
    `zone_id` INT UNSIGNED,
    `user_id` INT UNSIGNED NOT NULL,
    `content` TEXT NOT NULL,
    `annotation_type` ENUM('comment', 'note', 'warning', 'question') DEFAULT 'comment',
    `color` VARCHAR(7) DEFAULT '#FFEB3B',
    `is_resolved` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`zone_id`) REFERENCES `document_zones`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table du journal d'activité
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED,
    `description` TEXT,
    `metadata` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_action` (`action`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des notifications
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'document', 'link', 'annotation') DEFAULT 'info',
    `link` VARCHAR(500),
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_read` (`user_id`, `is_read`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des sessions de connexion (pour sécurité)
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `session_token` VARCHAR(255) NOT NULL UNIQUE,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(500),
    `expires_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_token` (`session_token`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Données initiales
-- =====================================================

-- Création des 3 équipes
INSERT INTO `teams` (`name`, `description`, `color`) VALUES
('Comptabilité', 'Équipe de gestion comptable et financière', '#10B981'),
('Administration', 'Équipe administrative et RH', '#8B5CF6'),
('Direction', 'Direction générale et management', '#F59E0B');

-- Création du compte administrateur par défaut
-- Mot de passe: Admin123! (à changer immédiatement)
INSERT INTO `users` (`team_id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`) VALUES
(3, 'admin', 'admin@docuflow.local', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X.VRnAE3s7.XWtqWS', 'Administrateur', 'Système', 'admin');

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- Message de confirmation
-- =====================================================
-- Base de données installée avec succès !
-- 
-- Compte administrateur :
-- Email: admin@docuflow.local
-- Mot de passe: Admin123!
-- 
-- IMPORTANT: Changez ce mot de passe immédiatement !
-- =====================================================
