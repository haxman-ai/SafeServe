# SafeServ

Application **Symfony** de gestion HACCP pour la restauration : planification des menus de la semaine et relevés de températures des plats, avec suivi de conformité.

## Sommaire

- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Utilisation](#utilisation)
- [Rôles et accès](#rôles-et-accès)
- [Architecture](#architecture)
- [Commandes utiles](#commandes-utiles)

## Stack technique

- **Symfony 7.4** (PHP 8.2+)
- **MySQL 8** via Docker
- **Nginx** (serveur web)
- **PHPMyAdmin** (administration base de données)
- **Doctrine ORM** / Migrations
- Thème **Bootstrap Bootswatch Slate** (dark)

## Prérequis

- Docker et Docker Compose

## Installation

```bash
git clone https://github.com/haxman-ai/SafeServe.git
cd SafeServe

docker compose up -d --build

docker exec safeserv_php composer install
docker exec safeserv_php php bin/console doctrine:migrations:migrate --no-interaction
```

Configurer la connexion à la base de données dans `app/.env.local` si besoin :

```
DATABASE_URL="mysql://user:pwd@mysql:3306/safeserv?serverVersion=8.0&charset=utf8mb4"
```

## Utilisation

| Service    | URL                            |
|------------|---------------------------------|
| Application | http://localhost:8080          |
| PHPMyAdmin  | http://localhost:8081          |

## Rôles et accès

- **ROLE_ADMIN** — gestion des menus de la semaine (`/menu`) et suivi des conformités
- **ROLE_USER** — saisie des relevés de température (`/temp`)

## Architecture

```
app/src/
  Controller/   HomeController, MenuController, RegistrationController, SecurityController, TempController
  Entity/       User, Plat, Menu, Temp
  Repository/   un repository par entité
  Form/         MenuType, TempType
app/templates/
  base.html.twig
  _shared/_nav.html.twig   ← navbar commune
  home/, menu/, temp/, registration/, security/
```

### Entités principales

- **User** — email, password, nom, roles (`ROLE_USER` / `ROLE_ADMIN`)
- **Plat** — nom, type (entrée / plat / dessert)
- **Menu** — date de service, plats associés (ManyToMany)
- **Temp** — température relevée, date/heure du relevé, plat concerné, utilisateur ayant fait le relevé

## Commandes utiles

Toutes les commandes `php bin/console` s'exécutent dans le conteneur PHP :

```bash
docker exec safeserv_php php bin/console <commande>
docker exec -it safeserv_php bash   # entrer dans le conteneur
```

Commandes fréquentes :

```bash
docker exec safeserv_php php bin/console make:entity <Nom>
docker exec safeserv_php php bin/console make:migration
docker exec safeserv_php php bin/console doctrine:migrations:migrate --no-interaction
docker exec safeserv_php php bin/console make:form <NomType>
docker exec safeserv_php php bin/console cache:clear
```
