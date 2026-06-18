# Technical test - Fullstack Symfony / Twig Developer

## Project overview

This technical test aims to assess your fullstack development skills with Symfony and Vue.js.

You will have access to a complete development environment including:
- A Symfony 7.4 backend with a REST API
- A Vue.js frontend in the `front` folder
- A Docker environment with PHP, MariaDB and PHPMyAdmin

### Technical architecture

**Backend (Symfony 7.4)**
- FosRestBundle for the REST API
- NelmioApiDocBundle for automatic documentation
- LexikJWTAuthenticationBundle for JWT authentication
- EasyAdmin for the administration interface
- Fixtures to generate test data

**Database**
- MariaDB
- 2 main entities: `User` and `Event`
- Events are linked to users via the `$creator` property

**Frontend (Vue.js)**
- Basic Vue.js application connected to the API
- Display of the event list

### Access URLs

- **Frontend application**: `http://docker.local`
- **EasyAdmin interface**: `http://docker.local/admin`
- **API documentation**: `http://docker.local/api/doc`
- **PHPMyAdmin**: `https://docker.local:8080`

---

## Environment setup

### Prerequisites
- Docker and Docker Compose installed
- PHPStorm (recommended)
- Create a branch with your name to be able to open a merge request

### Starting the project

```bash
# Start the Docker containers
docker-compose up -d

# Install the vendors
docker exec -it php composer install

# Install yarn encore
docker exec -it php yarn install

# Build the assets
docker exec -it php yarn encore dev

# Build the database
docker exec -it php bin/console doctrine:migrations:migrate

# Load the fixtures
docker exec -it php bin/console doctrine:fixtures:load

# Generate the JWT keys
php bin/console lexik:jwt:generate-keypair

# Start the frontend (from the PHPStorm terminal)
cd front
yarn serve
````

Or use the Docker plugin in PHPStorm.

### Accessing the Docker terminal with PHPStorm

#### Method 1: Via the integrated terminal

1. Open the PHPStorm terminal (`Alt + F12` or via the menu `View > Tool Windows > Terminal`)
2. Run the following command:

```bash
docker-compose exec php bash
```
3. You are now inside the PHP container

#### Method 2: Via the Docker plugin

1. Open the Docker view (`View > Tool Windows > Services` or `Alt + 8`)
2. In the tree, expand `Docker > Containers`
3. Find the PHP container (usually named `[project]_php_1`)
4. Right-click the container > `Create terminal > As Container User`
7. A terminal opens directly in the container in the source folder (/var/www/html)


---

## Exercise instructions

### Part 1: Symfony backend

#### 1.1 - Modifying the Event entity

Add a `color` property to the `Event` entity with the following constraints:
- Type: string (or enum if you prefer)
- Possible values: `rouge`, `vert`, `bleu`
- Required
- Add the appropriate validation

**Deliverables**:
- Modified `Event` entity
- Doctrine migration created and executed
- Fixtures updated to randomly assign a color to each event

#### 1.2 - Optimizing the GET /events route

Modify the existing route that returns the event list to:
- Return only the events of the current month
- Return only the events whose `creator` matches the logged-in user (via the JWT)

**Deliverables**:
- Optimized GET route with the requested filters
- Updated API documentation (annotations)

#### 1.3 - GET /events/{id} route

Create a new route to retrieve the details of a single event:
- Method: GET
- Parameter: event ID
- Security: return a 403 error if the event does not belong to the logged-in user
- Return a 404 error if the event does not exist

**Deliverables**:
- GET route created with the security implemented
- Updated API documentation

#### 1.4 - POST /events/{id} route

Create a route to update an existing event:
- Method: POST (or PUT/PATCH depending on your preference)
- Parameter: event ID
- Body: JSON with the editable fields (title, description, color)
- Security: verify that the logged-in user is indeed the creator of the event
- Validation: ensure the color is among the allowed values

**Deliverables**:
- POST route created with validation and security
- Appropriate error handling (403, 404, 400)
- Updated API documentation

---

### Part 2: Vue.js frontend

#### 2.1 - Displaying colors

Modify the event list display to add a color dot:
- Display a colored dot next to each event
- The color of the dot must match the `color` property of the event

**Deliverables**:
- Modified Vue.js component displaying the dots

#### 2.2 - Viewing an event

Add a "View" or "Details" button on each event:
- On click, retrieve the event details via the GET route created previously
- Display the information on a new page

**Deliverables**:
- Button added on each event
- Page created to display the details
- Call to the GET /events/{id} API

#### 2.3 - Editing an event

On the page, allow editing of the event:
- Editable fields: title, description, color
- Display a selector for the color with only the 3 allowed values
- "Save" button that sends the changes via the POST route
- Client-side validation to ensure only allowed colors are sent

**Deliverables**:

- Edit form on a new page
- Data validation
- Call to the POST /events/{id} API
- Error handling (display of appropriate messages)
- Refresh of the list after editing

---

## Evaluation criteria

- **Clean and structured code**: adherence to Symfony and Vue.js conventions
- **Security**: correct handling of authentication and authorization
- **Validation**: data validation on both backend and frontend
- **Error handling**: appropriate error messages and smooth UX
- **Tests**: bonus if you add unit or functional tests
- **Documentation**: code commented if necessary, API annotations up to date

---

## Tips

- Use the Symfony commands inside the Docker container:
  ```bash
  docker-compose exec php bin/console make:migration
  docker-compose exec php bin/console doctrine:migrations:migrate
  docker-compose exec php bin/console doctrine:fixtures:load
  ```
- Check the API documentation at `/api/doc` to test your endpoints
- Feel free to use Symfony's debugging tools (Profiler, var_dump, etc.)
- Test your endpoints with a REST client (Postman, Insomnia, or directly via Nelmio)

---

## Estimated duration

2 to 3 hours depending on your experience level.

---

## Submission

Commit and push your work in a merge request on the provided Git repository. Make sure that:
- The migrations are included in the repository
- The fixtures are up to date
- The frontend code is ready to be tested
- The dependencies are listed in `composer.json` and `package.json`

Good luck! 🚀
