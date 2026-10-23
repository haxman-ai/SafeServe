# Reprise du projet SafeServ

Dernière mise à jour : 2026-10-16

## État Git

- Branche : `dev`
- Dernier commit poussé sur `origin/dev` : `be6d4ac` — `Fix menu deletion week and paginate temperature records`
- Le commit corrige l’édition des menus sans certains plats, passe les listes de relevés à une pagination Doctrine et conserve la semaine affichée après suppression d’un menu.
- Bornes de semaine : le lundi commence désormais à 00:00.

## Modifications locales à examiner

Au moment de la reprise, deux fichiers Twig étaient modifiés sans être inclus dans le commit :

- `app/templates/_shared/_nav.html.twig` (libellé « Menu-du-jour »)
- `app/templates/temp/menu-semaine.html.twig` (titre « Menu du jour »)

Le fichier `docker/AGENTS.md` était également non suivi. Ne pas l’ajouter au commit sans vérifier avec l’utilisateur.

## Prochain contrôle

L’utilisateur avait signalé que supprimer un menu semblait supprimer tous les menus et que les dates ne correspondaient pas. Les changements ci-dessus traitent les causes visibles dans le code (redirection vers la semaine courante et borne du lundi à l’heure courante), mais le comportement n’a pas été vérifié dans l’application. Reproduire si possible et confirmer que seul le menu ciblé disparaît.

SafeServ est configuré pour être servi sur `http://localhost:8080`. Lors de la dernière tentative, l’environnement Codex ne pouvait pas accéder au socket Docker (`permission denied`), donc l’état des conteneurs n’a pas pu être vérifié.
