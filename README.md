# Leaguer project

## Description:

The Leaguer project simulates football games based on Premier League rules. It includes 20 teams from the EPL. The project helps users to generate matches by week or for a whole season, replay matches, and select teams from a list. Results of each game can be edited, and stored to a database. You can review / edit / replay matches as well. The project calculates estimated result based on the season of 2023-2024 .The project is built with Symfony 7 and includes integration tests.

## Features:

-   Generate matches by week or for a whole season.
-   Replay matches.
-   Predefined 20 teams from EPL.
-   Calculating of estimated resuld based on real data.
-   Edit the results of each game.
-   Store match results in a database.
-   Review or modify match results or simulate an entire season at any time.
-   Integration tests.
-   Setup with Docker.

### Requirements:

-   **PHP 8.1 or higher**
-   **Composer**
-   **Required PHP extensions**
-  **Node.js (14 or later)**
-  **npm**

Refer to the Symfony requirements [documentation](https://symfony.com/doc/current/setup.html) for more details. 


## Setup Leaguer with Docker

This guide will help you set up and run the Symfony application using Docker and Docker Compose.

### Requirements:

Make sure you have the following software installed on your machine:
- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting Started

### 1. Clone the repository

```
git clone https://github.com/seregaayzik/leaguer
cd leaguer
```

### 2. Setup project
```
docker compose up -d
docker-compose exec php /bin/bash
npm install && npm run build -- --env=prod #compile project assets
composer install --no-interaction --optimize-autoloader
```

### 3. Install and configure DB
```
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 4. Run the project .

The project is available by [http://localhost:8000](http://localhost:8000). 

## Testing:
You need to deploy and configure test DB

```
php bin/console --env=test doctrine:migrations:migrate
php bin/console --env=test doctrine:fixtures:load
```
Then you can run tests
```
php bin/phpunit
```