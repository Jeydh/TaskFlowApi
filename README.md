# TaskFlow API

API backend de gestion de tâches (Task Management) développée en **PHP / Symfony** avec **API Platform**, **Doctrine**, **PostgreSQL** et **JWT**.

> Projet personnel orienté portfolio pour démontrer des compétences backend PHP modernes : authentification, endpoints custom, logique métier, API REST, sécurité, DTO/validation, et outillage de développement (WSL + Docker + Postman).

---

## Stack technique

- **PHP 8.x**
- **Symfony 7**
- **API Platform**
- **Doctrine ORM**
- **PostgreSQL**
- **JWT Authentication** (LexikJWTAuthenticationBundle)
- **Docker / Docker Compose** (via WSL sous Windows)
- **Postman** (tests manuels des endpoints)

---

## Objectif du projet

Construire une API de gestion de tâches réutilisable avec un futur front (web/mobile), en mettant en avant :

- une base CRUD propre
- de la sécurité (JWT)
- des endpoints custom
- de la logique métier (processor API Platform)
- des validations côté backend
- une structure de projet réaliste

---

## Fonctionnalités déjà implémentées

### Authentification & utilisateurs
- [x] Authentification **JWT** (`/api/login`)
- [x] Génération des clés JWT (`config/jwt`)
- [x] Route de test utilisateur connecté : **`/api/me`**
- [x] Endpoint custom d’inscription : **`/api/register`**
- [x] Vérification d’unicité de l’email
- [x] Validation de données via **DTO** (ex: longueur de mot de passe)

### Tâches (Task)
- [x] Entité `Task` (Doctrine)
- [x] Ressource exposée via **API Platform** (`/api/tasks`)
- [x] CRUD de base généré par API Platform
- [x] Champs métier principaux :
  - `title`
  - `description`
  - `status`
  - `priority`
  - `dueDate`
  - `createdAt`
  - `updatedAt`
  - `createdBy`
  - `assignedTo`

### Logique métier custom (API Platform Processor)
- [x] `createdAt` / `updatedAt` alimentés automatiquement à la création
- [x] `createdBy` affecté automatiquement à l’utilisateur authentifié
- [x] Champ `createdBy` protégé (non pilotable directement par le body client)
- [x] Champs “computed” / sérialisation testés dans les réponses API

### Sécurité / accès
- [x] Route `/api/login` accessible publiquement
- [x] Routes `/api/*` protégées par authentification (JWT)
- [x] Tests via Postman avec récupération automatique du token JWT

---

## Endpoints disponibles (actuels)

### Public
- `POST /api/register` — création d’un compte utilisateur
- `POST /api/login` — authentification JWT

### Protégés (JWT requis)
- `GET /api/me` — informations du user connecté
- `GET /api/tasks` — liste des tâches
- `POST /api/tasks` — création d’une tâche
- `GET /api/tasks/{id}` — détail d’une tâche
- `PATCH /api/tasks/{id}` — modification partielle
- `DELETE /api/tasks/{id}` — suppression

> Les routes `tasks` sont actuellement exposées par API Platform.

---