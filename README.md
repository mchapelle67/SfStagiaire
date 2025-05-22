👩‍💻 SfStagiaire – Gestion de Stagiaires en Centre de Formation

📌 Description

SfStagiaire est une application Symfony permettant la gestion de stagiaires dans un centre de formation. Le projet a été réalisé dans un cadre d'apprentissage du framework Symfony, avec une approche complète.

Ce projet intègre les bonnes pratiques de l’architecture MVC, le CRUD complet des stagiaires et des entreprises, ainsi qu’un système d’authentification.

🧠 Étapes du projet

🎨 Maquettage : Réalisation des wireframes sur Figma à partir du modèle Trullo.

⚙️ Création du projet Symfony via Composer.

🏗️ Architecture MVC mise en place.

📄 Affichage des données : listes, détails dynamiques via id.

🛠️ Fonctionnalités CRUD : ajout, modification, suppression.

🔐 Authentification : connexion / déconnexion via système de sécurité Symfony.


🔧 Fonctionnalités principales

✅ Affichage de la liste des stagiaires et entreprises

✅ Vue détaillée via identifiant (id)

✅ Ajout de stagiaire / entreprise via formulaire Symfony

✅ Modification et suppression

✅ Connexion / déconnexion avec rôles utilisateurs

✅ Sécurisation des accès (formulaires réservés aux utilisateurs connectés)
🛠️ Stack technique

Symfony 7

PHP 8.1

Doctrine ORM

Twig

MySQL

Figma (maquettage)

Git & GitHub


$
🚀 Installation

Cloner le dépôt :

git clone https://github.com/mchapelle67/SfStagiaire.git

cd SfStagiaire

Installer les dépendances :

composer install

Configurer la base de données dans .env :

DATABASE_URL="mysql:///%kernel.project_dir%/var/data.db"

Créer la base et exécuter les migrations :

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

Lancer le serveur Symfony :

symfony server:start

🔐 Authentification

Page de connexion : /login

Déconnexion : /logout

Accès aux fonctionnalités uniquement pour les utilisateurs authentifiés

Projet réalisé dans le cadre de ma formation en développement web.

