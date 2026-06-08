# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**SafeServ** — Application Symfony de gestion HACCP pour la restauration. Permet de gérer les menus de la semaine et les relevés de températures des plats.

Projet de titre professionnel développeur — l'utilisateur doit rester autonome. En mode mentor, ne pas donner les réponses directement.

## Stack

- **Symfony** (PHP) dans `app/`
- **MySQL 8** via Docker (`safeserv_mysql`)
- **Nginx** sur le port `8080`
- **PHPMyAdmin** sur le port `8081`
- Thème Bootstrap **Bootswatch Slate** (dark)

## Commandes essentielles

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

## Architecture

```
app/src/
  Controller/   HomeController, MenuController, RegistrationController, SecurityController, TempController
  Entity/       User, Plat, Menu, Temp
  Repository/   un repository par entité
  Form/         MenuType (formulaire menu)
app/templates/
  base.html.twig
  _shared/_nav.html.twig   ← navbar commune
  home/, menu/, temp/, registration/, security/
```

## Entités et relations

- **User** — email, password, nom, roles (ROLE_USER / ROLE_ADMIN)
- **Plat** — nom, type (entree / plat / dessert)
- **Menu** — served_at (DateTimeImmutable), ManyToMany → Plat
- **Temp** — temperature (float), releveAT (DateTimeImmutable), ManyToOne → Plat, ManyToOne → User

## Rôles et accès

- `ROLE_ADMIN` — accès au menu (`/menu`)
- `ROLE_USER` — accès aux relevés de température (`/temp`)
- Contrôle via `$this->denyAccessUnlessGranted('ROLE_X')` en début de méthode

## Pattern contrôleur

```php
#[Route('/route', name: 'app_nom')]
public function index(XxxRepository $repo): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');
    return $this->render('template.html.twig', ['data' => $repo->findAll()]);
}
```

## Pattern template Twig

```twig
{% for item in items %}
    <tr>...</tr>
{% else %}
    <tr><td colspan="N">Aucun élément.</td></tr>
{% endfor %}
```

## Base de données

- Host (depuis conteneur) : `mysql:3306`
- Base : `safeserv`, User : `user`, Password : `pwd`
- Config dans `app/.env.local` (DATABASE_URL)
