# 🚀 Plateforme Associations & Missions

Ce projet est une application Symfony permettant la gestion d’associations, la création de missions, et la vérification automatique du numéro RNA grâce à une API externe.

---

## 📦 Prérequis

Avant de commencer, assure-toi d’avoir installé :

- [PHP 8.2+](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Symfony CLI](https://symfony.com/download)
- [MySQL ou PostgreSQL](https://www.mysql.com/) (selon ta configuration)
- [Node.js & NPM](https://nodejs.org/) pour gérer les assets frontend

---

## 🔧 Installation du projet principal

1. **Clone le dépôt**
   ```bash
   git clone <url-de-ton-projet>
   cd ton-projet
Installe les dépendances PHP

bash
Copier
Modifier
composer install
Configure l’environnement
Copie le fichier .env :

bash
Copier
Modifier
cp .env .env.local
Puis configure ta base de données dans .env.local :

env
Copier
Modifier
DATABASE_URL="mysql://user:password@127.0.0.1:3306/associations_db"
Migrations & Base de données

bash
Copier
Modifier
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
Lancer le serveur Symfony

bash
Copier
Modifier
symfony serve -d
Le projet est maintenant disponible sur http://127.0.0.1:8000.

🌍 Dépendances externes
Le projet repose sur deux APIs complémentaires. Elles doivent être installées et démarrées en parallèle.

1️⃣ API-RNA-Check
Cette API permet de vérifier la validité d’un numéro RNA et renvoie les informations d’une association.

Installation :

bash
Copier
Modifier
git clone https://github.com/Lieuc/API-RNA-Check
cd API-RNA-Check
npm install
npm start
Par défaut, elle tourne sur http://localhost:5088.

2️⃣ post-api
Cette API gère la partie messagerie et publication associée au projet.

Installation :

bash
Copier
Modifier
git clone https://github.com/Lieuc/post-api
cd post-api
npm install
npm run dev
⚙️ Configuration du projet Symfony
Dans src/Controller/AssociationController.php, la vérification RNA est faite via l’API :

php
Copier
Modifier
private string $postApi = 'http://localhost:5088/api/Rna/check';
⚠️ Assure-toi que API-RNA-Check tourne bien sur le port 5088.

🚀 Lancer le projet complet
Démarre API-RNA-Check

Démarre post-api

Démarre ton projet Symfony

Ouvre http://127.0.0.1:8000
