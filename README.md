Billetterie Musée du Louvre
======

Projet 4 du parcours Chef de Projet Multimedia spécialité développement d'OpenClassrooms.

## Installation de l'application
Le guide ci-dessous part du principe du serveur web déjà configuré.

Pré-requis : MySql et PHP 7.1.*

### Installation des sources et de la base de données
Se positionner dans le répertoire choisi pour installer l'application.

1. Dans une console, saisir : 
`git clone https://github.com/chatlumo/p4-Louvre.git`

2. Puis :
`cd p4-Louvre`

3. Puis :
`composer install`

4. Saisir les paramètres demandés par la console

5. **Si la base de donnée n'a pas été créée en amont, saisir :**
`bin/console doctrine:database:create`

6. Puis :
`bin/console doctrine:schema:update --force`

7. Faire pointer le "document root" du serveur web vers le dossier "web" (p4-Louvre/web).

L'application est prête à être utilisée !

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/9d5dd725c80a4873a066d4d6ff0ed95a)](https://www.codacy.com/app/chatlumo/p4-Louvre?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=chatlumo/p4-Louvre&amp;utm_campaign=Badge_Grade)