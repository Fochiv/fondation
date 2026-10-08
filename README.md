# Aliko Dangote Foundation — plateforme de soumission de projets

Site PHP 8.1+ et MySQL pour présenter un programme d’accompagnement, recevoir des candidatures, suivre leur statut et administrer les dossiers. L’interface publique est bilingue français/anglais ; le français est la langue par défaut.

## Lancer l’aperçu Replit

Le workflow du projet démarre le serveur PHP sur le port `5000`. Les pages publiques peuvent être consultées sans base de données. Pour enregistrer des candidatures, afficher le suivi ou utiliser l’espace administrateur, connectez une base MySQL externe et configurez les variables privées suivantes dans Replit Secrets :

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_PORT` (facultatif, valeur par défaut : `3306`)
- `SESSION_SECRET` (déjà fourni dans l’environnement Replit)
- `ADMIN_SETUP_KEY` (à définir temporairement avec une valeur longue avant la création du premier administrateur)

Ne copiez pas de mot de passe de base de données ou de clé secrète dans le code source. Les informations de connexion ne sont pas incluses dans ce dépôt.

## Installation sur Hostinger

1. Créez une base MySQL et un utilisateur dans le panneau Hostinger.
2. Importez `database/schema.sql` dans cette base via phpMyAdmin.
3. Copiez `config/local.example.php` sous `config/local.php`, puis saisissez localement les paramètres MySQL et deux valeurs secrètes aléatoires et longues. `config/local.php` est ignoré par Git et protégé par `config/.htaccess`.
4. Utilisez PHP 8.1 ou une version ultérieure, avec les extensions PDO et PDO MySQL activées.
5. Déployez les fichiers à la racine du site. Le serveur Apache sert `index.php` comme page d’accueil.
6. Définissez `SESSION_SECRET` et `ADMIN_SETUP_KEY` dans la configuration privée. Ouvrez une fois `/admin/setup.php` pour créer un administrateur avec une nouvelle adresse e-mail et un mot de passe d’au moins 12 caractères.
7. Connectez-vous à `/admin/login.php`, puis retirez `ADMIN_SETUP_KEY` de la configuration privée. L’installation refuse de créer un deuxième administrateur.
8. Depuis **Administration → Paramètres**, renseignez les coordonnées officielles, le WhatsApp avec indicatif de pays et les liens sociaux. Les liens WhatsApp sont lus depuis ce paramètre unique.

Le mot de passe d’administration est créé à l’installation et stocké avec `password_hash()`. Aucun mot de passe n’est préconfiguré dans le code.

## Fonctionnalités

- Pages d’accueil, de présentation, de programme, de critères, de témoignages, FAQ, contact et confidentialité.
- Candidature en cinq étapes avec validation côté navigateur et côté PHP ; aucun champ n’est envoyé avant la validation finale.
- Référence unique `DG-AAAA-000001`, affichage de confirmation, suivi par référence et e-mail/téléphone, sans exposer les notes privées.
- Notifications d’assistance WhatsApp configurées dans les paramètres de l’administration.
- Administration protégée : tableau de bord, filtres, tri, pagination, export CSV, fiche détaillée, statuts, notes privées, historique, témoignages, messages et coordonnées.
- Requêtes PDO préparées, échappement HTML, jetons CSRF, sessions HttpOnly/SameSite, délai d’expiration et limitation des tentatives de connexion.
- Consentements explicites, collecte réduite des données personnelles et page de confidentialité.

## Statistiques et informations de programme

Les chiffres de l’accueil (projets, secteurs et pays) sont des données d’illustration explicitement signalées comme telles. Remplacez-les par des valeurs validées avant publication. Les critères, montants éventuels et autres conditions officielles doivent également être confirmés par le programme avant la mise en production.

Les formulaires dépendent de MySQL et ne sauvegardent aucune candidature tant que la connexion à la base et `database/schema.sql` ne sont pas installés. Une copie de sauvegarde régulière de la base est nécessaire. Le texte de confidentialité fourni dans ce prototype doit être validé selon les obligations applicables avant son usage public.
