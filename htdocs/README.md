# DocuFlow - Portail Collaboratif de Gestion Documentaire

## 📋 Description

DocuFlow est une application web complète de gestion documentaire collaborative conçue pour les équipes comptables. Elle permet de stocker, organiser, annoter et lier des documents PDF avec une interface moderne et intuitive.

## ✨ Fonctionnalités

### Gestion des Documents
- **Upload de PDF** : Import de documents avec validation (taille, type MIME)
- **Métadonnées** : Type, référence, montant, date, équipe associée
- **Viewer PDF intégré** : Visualisation avec zoom, navigation par page
- **Téléchargement** : Accès direct aux fichiers originaux

### Mapping Visuel & Liaisons
- **Sélection de zones** : Dessinez des rectangles sur le PDF pour isoler des informations
- **OCR automatique** : Extraction du texte des zones sélectionnées (Tesseract.js)
- **Liaisons bidirectionnelles** : Connectez des zones entre différents documents
- **Types de liaisons** : Référence, paiement, annexe, justificatif, duplicata

### Annotations & Collaboration
- **4 types d'annotations** : Commentaire, Note, Avertissement, Question
- **Association aux zones** : Annotations liées à des parties spécifiques du document
- **Système de résolution** : Marquez les annotations comme résolues
- **Historique complet** : Traçabilité de toutes les actions

### Recherche
- **Recherche par métadonnées** : Titre, référence, type, équipe
- **Recherche full-text** : Dans le contenu OCR extrait
- **Filtres avancés** : Par date, type, équipe

### Notifications
- **Temps réel** : Polling toutes les 30 secondes
- **Types** : Nouveau document, annotation, liaison
- **Badge compteur** : Notifications non lues

### Administration
- **Gestion des utilisateurs** : Création, modification, suppression
- **Rôles** : Administrateur, Membre
- **Équipes** : Organisation avec couleurs distinctives

## 🛠️ Stack Technique

- **Backend** : PHP 7.4+ (Architecture MVC)
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Bibliothèques** :
  - PDF.js (Mozilla) - Visualisation PDF
  - Fabric.js - Canvas interactif pour les zones
  - Tesseract.js - OCR côté client

## 📁 Structure du Projet

```
docuflow/
├── public/                     # Dossier accessible publiquement
│   ├── index.php              # Point d'entrée (routeur)
│   ├── .htaccess              # Réécriture URL Apache
│   ├── css/
│   │   └── style.css          # Styles CSS
│   ├── js/
│   │   └── app.js             # JavaScript principal
│   └── uploads/               # Documents uploadés
├── src/
│   ├── Config/
│   │   └── config.php         # Configuration globale
│   ├── Router.php             # Système de routing
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── DocumentController.php
│   │   └── UserController.php
│   ├── Models/
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Team.php
│   │   ├── Document.php
│   │   ├── DocumentZone.php
│   │   ├── DocumentLink.php
│   │   ├── Annotation.php
│   │   ├── Notification.php
│   │   └── ActivityLog.php
│   └── Views/
│       ├── layouts/
│       │   └── main.php       # Layout principal
│       └── pages/
│           ├── login.php
│           ├── dashboard.php
│           ├── profile.php
│           ├── teams.php
│           ├── activity.php
│           ├── search.php
│           ├── documents/
│           │   ├── index.php
│           │   ├── create.php
│           │   ├── show.php
│           │   └── edit.php
│           └── users/
│               ├── index.php
│               └── form.php
├── storage/
│   └── logs/                  # Logs d'application
├── install.sql                # Schéma de base de données
└── README.md                  # Cette documentation
```

## 🚀 Installation

### Prérequis
- PHP 7.4+ avec extensions : PDO, pdo_mysql, mbstring, fileinfo
- MySQL 5.7+ ou MariaDB 10+
- Serveur web Apache avec mod_rewrite

### Étapes

1. **Cloner/Uploader les fichiers** sur votre hébergement

2. **Créer la base de données**
   ```sql
   CREATE DATABASE docuflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importer le schéma**
   ```bash
   mysql -u user -p docuflow < install.sql
   ```

4. **Configurer la connexion**
   Éditer `src/Config/config.php` :
   ```php
   define('DB_HOST', 'votre_host');
   define('DB_NAME', 'docuflow');
   define('DB_USER', 'votre_user');
   define('DB_PASS', 'votre_password');
   define('APP_URL', 'https://votre-domaine.com');
   ```

5. **Configurer les permissions**
   ```bash
   chmod 755 public/uploads/
   chmod 755 storage/logs/
   ```

6. **Configurer Apache**
   Pointer le DocumentRoot vers le dossier `public/`

### Configuration InfinityFree

Pour héberger sur InfinityFree :
1. Créer un compte et un site sur infinityfree.com
2. Accéder au gestionnaire de fichiers
3. Uploader tous les fichiers dans `htdocs/`
4. Créer la base de données MySQL via le panel
5. Mettre à jour `config.php` avec les informations MySQL InfinityFree
6. Accéder à votre domaine

## 👤 Compte Administrateur par Défaut

```
Email : admin@docuflow.local
Mot de passe : Admin123!
```

⚠️ **IMPORTANT** : Changez immédiatement ce mot de passe après la première connexion !

## 📖 Guide d'Utilisation

### Connexion
Accédez à l'URL de votre installation et connectez-vous avec vos identifiants.

### Ajouter un Document
1. Cliquez sur "Upload" ou allez dans "Documents" > "Nouveau document"
2. Glissez-déposez ou sélectionnez un fichier PDF
3. Remplissez les métadonnées (titre, type, référence...)
4. Cliquez sur "Uploader"

### Créer des Zones et Liaisons
1. Ouvrez un document
2. Cliquez sur l'outil "Zone" dans la barre d'outils
3. Dessinez un rectangle sur la zone souhaitée
4. Donnez un label à la zone
5. Pour créer une liaison, cliquez sur l'icône liaison de la zone
6. Sélectionnez le document cible

### Lancer l'OCR
1. Ouvrez un document
2. Cliquez sur "OCR" dans la barre d'outils
3. Attendez la fin du traitement
4. Le document est maintenant recherchable

### Ajouter une Annotation
1. Ouvrez un document
2. Allez dans l'onglet "Notes"
3. Cliquez sur "Ajouter"
4. Sélectionnez le type et rédigez votre commentaire

## 🔐 Sécurité

- Protection CSRF sur tous les formulaires
- Mots de passe hashés avec bcrypt (cost 12)
- Sessions sécurisées (httponly, SameSite)
- Validation stricte des uploads
- Prepared statements PDO (anti SQL injection)
- Échappement HTML systématique (anti XSS)

## 🗄️ Base de Données

### Tables Principales

| Table | Description |
|-------|-------------|
| users | Utilisateurs du système |
| teams | Équipes |
| documents | Documents PDF |
| document_content | Contenu OCR extrait |
| document_zones | Zones sélectionnées |
| document_links | Liaisons entre documents |
| annotations | Annotations et commentaires |
| activity_log | Journal d'activité |
| notifications | Notifications in-app |
| user_sessions | Sessions utilisateur |

## 🔧 Maintenance

### Logs
Les erreurs sont enregistrées dans `storage/logs/`

### Nettoyage
```sql
-- Supprimer les anciennes notifications (> 30 jours)
DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Supprimer les anciennes sessions
DELETE FROM user_sessions WHERE expires_at < NOW();
```

## 📝 Licence

Ce projet est fourni tel quel, sans garantie. Libre d'utilisation pour des projets personnels et commerciaux.

## 🤝 Support

Pour toute question ou problème, consultez les logs d'erreur et vérifiez la configuration de votre serveur.

---

Développé avec ❤️ pour la gestion documentaire collaborative.
