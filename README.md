# Test technique - Développeur Fullstack Symfony / Twig

## Présentation du projet

Ce test technique a pour objectif d'évaluer vos compétences en développement fullstack avec Symfony et Vue.js.

Vous disposerez d'un environnement de développement complet comprenant :
- Un backend Symfony 7.4 avec une API REST
- Un frontend Vue.js dans le dossier front
- Un environnement Docker avec PHP, MariaDB et PHPMyAdmin

### Architecture technique

**Backend (Symfony 7.4)**
- FosRestBundle pour l'API REST
- NelmioApiDocBundle pour la documentation automatique
- LexikJWTAuthenticationBundle pour l'authentification JWT
- EasyAdmin pour l'interface d'administration
- Fixtures pour générer des données de test

**Base de données**
- MariaDB
- 2 entités principales : `User` et `Event`
- Les événements sont liés aux utilisateurs via la propriété `$creator`

**Frontend (Vue.js)**
- Application Vue.js basique connectée à l'API
- Affichage de la liste des événements

### URLs d'accès

- **Application frontend** : `http://docker.local`
- **Interface EasyAdmin** : `http://docker.local/admin`
- **Documentation API** : `http://docker.local/api/doc`
- **PHPMyAdmin** : `https://docker.local:8080`

---

## Configuration de l'environnement

### Prérequis
- Docker et Docker Compose installés
- PHPStorm (recommandé)
- Créer une branche à votre nom pour pouvoir faire une merge request

### Démarrage du projet

```bash
# Démarrer les conteneurs Docker
docker-compose up -d

# Installer les vendors
docker exec -it php composer install

# Installer yarn encore
docker exec -it php yarn install

# Build les assets
docker exec -it php yarn encore dev

# Construire la base de données
docker exec -it php bin/console doctrine:migrations:migrate

# Charge les fixtures
docker exec -it php bin/console doctrine:fixtures:load

# Générer les clefs JWT
php bin/console lexik:jwt:generate-keypair

# Démarrer le front (depuis le terminal phpstorm)a
cd front
yarn serve
````

Ou utilisation du plugin Docker dans PHPStorm.

### Accéder au terminal Docker avec PHPStorm

#### Méthode 1 : Via le terminal intégré

1. Ouvrir le terminal PHPStorm (`Alt + F12` ou via le menu `View > Tool Windows > Terminal`)
2. Exécuter la commande suivante :

```bash
docker-compose exec php bash
```
3. Vous êtes maintenant dans le conteneur PHP

#### Méthode 2 : Via le plugin Docker

1. Ouvrir la vue Docker (`View > Tool Windows > Services` ou `Alt + 8`)
2. Dans l'arborescence, dérouler `Docker > Containers`
3. Trouver le conteneur PHP (généralement nommé `[projet]_php_1`)
4. Clic droit sur le conteneur > `Create terminal > As Container User`
7. Un terminal s'ouvre directement dans le conteneur dans le dossier source (/var/www/html)


---

## Consignes de l'exercice

### Partie 1 : Backend Symfony

#### 1.1 - Modification de l'entité Event

Ajouter une propriété `color` à l'entité `Event` avec les contraintes suivantes :
- Type : chaîne de caractères (ou enum si vous préférez)
- Valeurs possibles : `rouge`, `vert`, `bleu`
- Obligatoire
- Ajouter la validation appropriée

**Livrables** :
- Entité `Event` modifiée
- Migration Doctrine créée et exécutée
- Fixtures mises à jour pour attribuer aléatoirement une couleur à chaque événement

#### 1.2 - Optimisation de la route GET /events

Modifier la route existante qui renvoie la liste des événements pour :
- Ne retourner que les événements du mois en cours
- Ne retourner que les événements dont le `creator` correspond à l'utilisateur connecté (via le JWT)

**Livrables** :
- Route GET optimisée avec les filtres demandés
- Documentation de l'API mise à jour (annotations)

#### 1.3 - Route GET /events/{id}

Créer une nouvelle route pour récupérer les détails d'un événement unique :
- Méthode : GET
- Paramètre : ID de l'événement
- Sécurité : retourner une erreur 403 si l'événement n'appartient pas à l'utilisateur connecté
- Retourner une erreur 404 si l'événement n'existe pas

**Livrables** :
- Route GET créée avec la sécurité implémentée
- Documentation de l'API mise à jour

#### 1.4 - Route POST /events/{id}

Créer une route pour mettre à jour un événement existant :
- Méthode : POST (ou PUT/PATCH selon votre préférence)
- Paramètre : ID de l'événement
- Body : JSON avec les champs modifiables (titre, description, couleur)
- Sécurité : vérifier que l'utilisateur connecté est bien le créateur de l'événement
- Validation : s'assurer que la couleur fait partie des valeurs autorisées

**Livrables** :
- Route POST créée avec validation et sécurité
- Gestion des erreurs appropriée (403, 404, 400)
- Documentation de l'API mise à jour

---

### Partie 2 : Frontend Vue.js

#### 2.1 - Affichage des couleurs

Modifier l'affichage de la liste des événements pour ajouter une pastille de couleur :
- Afficher une pastille colorée à côté de chaque événement
- La couleur de la pastille doit correspondre à la propriété `color` de l'événement

**Livrables** :
- Composant Vue.js modifié avec affichage des pastilles

#### 2.2 - Visualisation d'un événement

Ajouter un bouton "Voir" ou "Détails" sur chaque événement :
- Au clic, récupérer les détails de l'événement via la route GET créée précédemment
- Afficher les informations dans une nouvelle page

**Livrables** :
- Bouton ajouté sur chaque événement
- Page créée pour afficher les détails
- Appel à l'API GET /events/{id}

#### 2.3 - Modification d'un événement

Dans la page, permettre la modification de l'événement :
- Champs modifiables : titre, description, couleur
- Afficher un sélecteur pour la couleur avec uniquement les 3 valeurs autorisées
- Bouton "Enregistrer" qui envoie les modifications via la route POST
- Validation côté client pour s'assurer que seules les couleurs autorisées sont envoyées

**Livrables** :

- Formulaire de modification dans une nouvelle page
- Validation des données
- Appel à l'API POST /events/{id}
- Gestion des erreurs (affichage de messages appropriés)
- Rafraîchissement de la liste après modification

---

## Critères d'évaluation

- **Code propre et structuré** : respect des conventions Symfony et Vue.js
- **Sécurité** : gestion correcte de l'authentification et des autorisations
- **Validation** : validation des données côté backend et frontend
- **Gestion des erreurs** : messages d'erreur appropriés et UX fluide
- **Tests** : bonus si vous ajoutez des tests unitaires ou fonctionnels
- **Documentation** : code commenté si nécessaire, annotations API à jour

---

## Conseils

- Utilisez les commandes Symfony dans le conteneur Docker :
  ```bash
  docker-compose exec php bin/console make:migration
  docker-compose exec php bin/console doctrine:migrations:migrate
  docker-compose exec php bin/console doctrine:fixtures:load
  ```
- Consultez la documentation de l'API sur `/api/doc` pour tester vos endpoints
- N'hésitez pas à utiliser les outils de debug de Symfony (Profiler, var_dump, etc.)
- Testez vos endpoints avec un client REST (Postman, Insomnia, ou directement via Nelmio)

---

## Durée estimée

2 à 3 heures selon votre niveau d'expérience.

---

## Rendu

Commitez et pushez votre travail dans une merge request sur le dépôt Git fourni. Assurez-vous que :
- Les migrations sont incluses dans le dépôt
- Les fixtures sont à jour
- Le code frontend est prêt à être testé
- Les dépendances sont listées dans `composer.json` et `package.json`

Bon courage ! 🚀
