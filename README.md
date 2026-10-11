# Aliko Dangote Foundation — plateforme de soumission de projets

## Le projet est-il entièrement en SQL ?

Non. Le site n’est pas écrit en SQL :

- **PHP** génère les pages, traite les formulaires, vérifie l’accès administrateur et communique avec la base.
- **HTML, CSS et JavaScript** construisent l’interface et ses interactions.
- **MySQL** conserve les candidatures, leur historique, les comptes d’administration, les témoignages publiés, les messages et les paramètres du site.
- `database/schema.sql` contient uniquement la structure SQL nécessaire à la base. Ce fichier ne contient ni le site ni les fichiers téléversés par les candidats.

## Prérequis pour Hostinger

- Un hébergement Hostinger avec un domaine ou sous-domaine dirigé vers le dossier du site.
- PHP 8.1 ou plus récent, avec **PDO**, **PDO MySQL** et **mbstring** activés.
- Une base MySQL et un utilisateur MySQL créés depuis hPanel.
- L’archive ou les fichiers du projet à téléverser.

## Installation sur Hostinger

### 1. Créer la base de données

1. Dans hPanel, ouvrez **Bases de données → Gestion MySQL** (le nom du menu peut varier).
2. Créez une base de données et un utilisateur MySQL.
3. Attribuez à cet utilisateur tous les privilèges sur cette base.
4. Notez le nom de l’hôte MySQL indiqué par Hostinger, le nom de base et le nom d’utilisateur. Hostinger ajoute souvent un préfixe : reprenez les valeurs exactes affichées dans hPanel.
5. Ouvrez phpMyAdmin depuis hPanel, sélectionnez la base nouvellement créée, puis importez `database/schema.sql`.
6. Vérifiez que les tables sont présentes, notamment `admins`, `applications`, `application_history`, `testimonials`, `settings` et `contact_messages`.

### 2. Téléverser les fichiers du site

1. Dans hPanel, ouvrez **Fichiers → Gestionnaire de fichiers**.
2. Téléversez les fichiers du site dans le dossier web du domaine — généralement `public_html` — ou dans le dossier assigné au domaine.
3. Conservez l’arborescence et téléversez notamment `admin/`, `assets/`, `config/`, `database/`, `includes/`, `index.php`, `logo_fondation.png`, `images2.jpg` et le fichier `.htaccess` à la racine.
4. N’envoyez pas de fichier `.env` ni de copie de sauvegarde contenant des mots de passe. Les fichiers `attached_assets/`, `.git/` et `.replit` ne sont pas nécessaires au site en production.

### 3. Renseigner la configuration privée

1. Dans `config/`, copiez `local.example.php` sous le nom `local.php`.
2. Modifiez **uniquement** `config/local.php` sur le serveur. Le fichier doit retourner un tableau PHP et n’est pas destiné à être téléversé publiquement avec des valeurs réelles.
3. Renseignez les valeurs fournies par Hostinger pour `DB_HOST`, `DB_PORT` (généralement `3306`), `DB_NAME`, `DB_USER` et `DB_PASSWORD`.
4. Définissez `SESSION_SECRET` et `ADMIN_SETUP_KEY` comme deux valeurs aléatoires, distinctes, longues et difficiles à deviner. Un gestionnaire de mots de passe peut les générer.
5. Pour créer le premier compte avec des identifiants préparés à l’avance, renseignez aussi `ADMIN_EMAIL` et `ADMIN_INITIAL_PASSWORD`. Ces champs sont lus côté serveur, pas affichés dans la page. Le mot de passe doit compter au moins 12 caractères.

Exemple de configuration — **ne remplacez pas les champs vides par des identifiants dans ce README** :

```php
return [
    'DB_HOST' => 'hôte MySQL indiqué par Hostinger',
    'DB_PORT' => '3306',
    'DB_NAME' => 'nom exact de la base',
    'DB_USER' => 'utilisateur MySQL',
    'DB_PASSWORD' => 'mot de passe MySQL',
    'SESSION_SECRET' => 'valeur aléatoire privée',
    'ADMIN_SETUP_KEY' => 'clé temporaire aléatoire privée',
    'ADMIN_EMAIL' => 'adresse du premier administrateur',
    'ADMIN_INITIAL_PASSWORD' => 'mot de passe temporaire du premier administrateur',
];
```

`config/local.php` est ignoré par Git et protégé par `config/.htaccess`. Ne partagez pas ce fichier et ne copiez pas ses valeurs dans une conversation ou dans une page web.

### 4. Créer le compte administrateur

1. Visitez `https://votre-domaine/admin/setup.php`.
2. Entrez la valeur privée de `ADMIN_SETUP_KEY`. Si `ADMIN_EMAIL` et `ADMIN_INITIAL_PASSWORD` sont tous deux configurés, l’application utilisera ces identifiants côté serveur pour créer le premier compte. Sinon, elle demandera l’e-mail et un mot de passe à définir sur le formulaire.
3. Après confirmation de création, connectez-vous sur `https://votre-domaine/admin/login.php`.
4. **Retirez ensuite `ADMIN_SETUP_KEY` et `ADMIN_INITIAL_PASSWORD` de `config/local.php`**. L’administrateur demeure enregistré dans MySQL sous forme de hash de mot de passe. Conservez les identifiants de connexion dans un gestionnaire sécurisé.
5. `admin/setup.php` ne permet pas de créer un second administrateur. Gardez une sauvegarde sécurisée de la base et prévoyez une procédure de récupération.

`ADMIN_INITIAL_PASSWORD` ne sert qu’à la création du premier compte. Le modifier ou le supprimer après l’installation ne change pas le mot de passe déjà enregistré dans MySQL. Cette version ne comporte pas encore d’écran de modification du mot de passe ou de l’adresse e-mail administrateur.

### 5. Vérifier la mise en ligne

- Confirmez que la page d’accueil, les images et les feuilles de style se chargent avec HTTPS.
- Soumettez un dossier de test et vérifiez son numéro de référence.
- Connectez-vous à l’administration, mettez à jour le statut du dossier et vérifiez son historique.
- Testez le suivi du dossier avec sa référence et l’e-mail ou le téléphone utilisés lors de la soumission.
- Testez le formulaire de contact et l’export CSV depuis l’administration.
- Depuis **Administration → Paramètres**, ajoutez les coordonnées officielles, WhatsApp et les liens sociaux.
- Supprimez les candidatures de test selon votre procédure de conservation des données.

## Aperçu Replit

Le workflow de développement démarre PHP sur le port `5000`. Les pages publiques peuvent s’afficher sans base de données ; l’enregistrement des candidatures et les fonctions d’administration requièrent une base MySQL configurée.

Pour l’aperçu Replit, renseignez `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` et, si nécessaire, `DB_PORT` dans **Tools → Secrets**. L’application utilise une base MySQL externe : elle ne remplace pas votre base par PostgreSQL.

## Contenu du site

- Pages d’accueil, À propos, programme, critères, témoignages, FAQ, contact, politique de confidentialité et conditions générales.
- Formulaire de candidature en cinq étapes, validation côté navigateur et côté PHP, et numéro de référence unique.
- Suivi par référence et e-mail/téléphone, sans exposer les notes internes.
- Administration protégée : tableau de bord, recherche, filtres, export CSV, statuts, notes privées, historique, témoignages, messages et paramètres.
- Requêtes PDO préparées, échappement HTML, protection CSRF, sessions sécurisées et limitation des tentatives de connexion.

## Avant publication

Les chiffres affichés sur l’accueil sont des valeurs d’illustration ; ils doivent être remplacés par des données officielles validées avant publication. Ils ne sont pas signalés comme chiffres de démonstration sur la page d’accueil. Confirmez également les critères et conditions du programme avant d’ouvrir les candidatures. Faites vérifier la politique de confidentialité et les conditions générales en fonction des obligations juridiques applicables.

Les formulaires n’enregistrent aucune candidature si MySQL n’est pas configuré ou si `database/schema.sql` n’a pas été importé. Planifiez des sauvegardes régulières de la base et des fichiers du site.
