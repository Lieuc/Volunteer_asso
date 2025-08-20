# 🚀 Plateforme Associations & Missions

Ce projet est une application Symfony permettant la gestion d'associations, la création de missions, et la vérification automatique du numéro RNA grâce à une API externe.

---

## 📦 Prérequis

Avant de commencer, assure-toi d'avoir installé :

- [PHP 8.2+](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Symfony CLI](https://symfony.com/download)
- [Node.js & NPM](https://nodejs.org/) pour gérer les assets frontend

---

## 🔧 Installation du projet principal

### 1. **Clone le dépôt**
```bash
git clone (https://github.com/Lieuc/Volunteer_asso)
cd ton-projet
```

### 2. **Installe les dépendances PHP**
```bash
composer install
```

### 3. **Configure l'environnement**
```bash
Le fichier .env se situe déja dans le git pour pouvoir tester le projet
```

### 4. **Base de données**
```bash
La bdd d'exemple et déjà présente et configuré et contient des données d'exemple
```

### 5. **Installer dépendance tailwindcss**
```bash
npm i
```

### 6. **Lancer le serveur Symfony**
```bash
symfony server:start
```

### 7. **Refresh tailwind (dans un autres teminal)**
```bash
npm run watch
```

Le projet est maintenant disponible sur [http://127.0.0.1:8000](http://127.0.0.1:8000).

**Config lien bdd**
```bash
Dans picture-api/src/main/java/com/post/Database.java, remplacer le chemin de la bdd avec celui sur vôtre machine.
```

---

## 🌍 Dépendances externes

Le projet repose sur deux APIs (jakarta & .net). Elles doivent être installées et démarrées en parallèle.

### 1️⃣ API-RNA-Check

Cette API permet de vérifier la validité d'un numéro RNA et renvoie les informations d'une association.

**Installation :**
```bash
git clone https://github.com/Lieuc/API-RNA-Check
cd API-RNA-Check
```

Par défaut, elle tourne sur [http://localhost:5088](http://localhost:5088).

### 2️⃣ post-api

Cette API gère la récupération des posts des utilisateurs.

**Installation :**
```bash
git clone https://github.com/Lieuc/post-api
cd post-api
```

---

## ⚙️ Configuration du projet Symfony

Dans `src/Controller/AssociationController.php`, la vérification RNA est faite via l'API :

```php
private string $postApi = 'http://localhost:5088/api/Rna/check';
```

⚠️ **Vérifier que API-RNA-Check tourne bien sur le port 5088.**

---

## 🚀 Lancer le projet complet

1. Démarre **API-RNA-Check**
2. Démarre **post-api**
3. Démarre ton **projet Symfony**
4. Ouvre [http://127.0.0.1:8000](http://127.0.0.1:8000)


