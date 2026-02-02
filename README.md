# Test technique - Développeur Fullstack Symfony / Twig

## Présentation du projet

Ce test technique a pour objectif d'évaluer vos compétences en développement fullstack avec Symfony et Twig

Vous disposerez d'un environnement de développement complet comprenant :
- Un backend Symfony 7.4 avec une interface d'administration EasyAdmin
- Un frontend Vue.js dans le dossier front
- Un environnement Docker avec PHP, MariaDB et PHPMyAdmin

### Architecture technique

**Backend (Symfony 7.4)**
- EasyAdmin pour l'interface d'administration
- Fixtures pour générer des données de test

**Base de données**
- MariaDB
- 2 entités principales : `User` et `Event`
- Les événements sont liés aux utilisateurs via la propriété `$creator`

**Frontend**
- Application Twig
- Affichage de la liste des événements

### URLs d'accès

- **Application frontend** : `http://docker.local`
- **Interface EasyAdmin** : `http://docker.local/admin`
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

# Créer la base de données et charger les fixtures
docker-compose exec php bin/console doctrine:fixtures:load
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

Modifier la route existante dans le controller `src/Controller/Front/EventController.php` qui renvoie la liste des événements pour :
- Ne retourner que les événements du mois en cours
- Ne retourner que les événements dont le `creator` correspond à l'utilisateur ayant pour e-mail `admin@effetb.com`

Modifier l'affichage de la liste des événements pour ajouter une pastille de couleur :
- Afficher une pastille colorée à côté de chaque événement
- La couleur de la pastille doit correspondre à la propriété `color` de l'événement

Ajouter un bouton "Voir" ou "Détails" sur chaque événement :
- Au clic, aller sur la route suivante (voir 1.3)

**Livrables** :
- Route GET optimisée avec les filtres demandés
- Documentation de l'API mise à jour (annotations)

#### 1.3 - Route GET /events/{id}

Créer une nouvelle route dans le même controller pour afficher les détails d'un événement unique dans un formulaire Symfony :
- Méthode : GET
- Paramètre : ID de l'événement
- Sécurité : retourner une erreur 403 si l'événement n'appartient pas à l'utilisateur ayant pour e-mail `admin@effetb.com`
- Retourner une erreur 404 si l'événement n'existe pas

Dans la page, permettre la modification de l'événement :
- Champs modifiables : titre, description, couleur
- Afficher un sélecteur pour la couleur avec uniquement les 3 valeurs autorisées
- Bouton "Enregistrer" qui envoie les modifications via la route POST
- Validation côté client pour s'assurer que seules les couleurs autorisées sont envoyées

#### 1.4 - Route POST /events/{id}

Créer une route pour mettre à jour un événement existant en lui passant le formulaire développer précédemment :
- Méthode : POST
- Paramètre : ID de l'événement
- Body : le formulaire
- Validation : s'assurer que la couleur fait partie des valeurs autorisées

**Livrables** :
- Route POST créée avec validation et sécurité
- Gestion des erreurs appropriée (403, 404, 400)
- Documentation de l'API mise à jour

---

## Critères d'évaluation

- **Code propre et structuré** : respect des conventions Symfony
- **Sécurité** : gestion correcte de des autorisations
- **Validation** : validation des données côté backend et frontend
- **Gestion des erreurs** : messages d'erreur appropriés et UX fluide
- **Tests** : bonus si vous ajoutez des tests unitaires ou fonctionnels
- **Documentation** : code commenté si nécessaire

---

## Conseils

- Utilisez les commandes Symfony dans le conteneur Docker :
  ```bash
  docker-compose exec php bin/console make:migration
  docker-compose exec php bin/console doctrine:migrations:migrate
  docker-compose exec php bin/console doctrine:fixtures:load
  ```
  
- N'hésitez pas à utiliser les outils de debug de Symfony (Profiler, var_dump, etc.)


---

## Rendu

Commitez et pushez votre travail dans une merge request sur le dépôt Git fourni. Assurez-vous que :
- Les migrations sont incluses dans le dépôt
- Les fixtures sont à jour
- Le code frontend est prêt à être testé
- Les dépendances sont listées dans `composer.json` et `package.json`

Bon courage ! 🚀
