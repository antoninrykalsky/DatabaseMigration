# DatabaseMigration

Simple tool to have create and execute database migration automatically. Tool is easilly usable with Nette projects.

## api
call **php vendor/bin/databaseMigration[.bat|.sh]** with parameters from the root of webapplication

**create [hint] [SQL|php]**
- create file in migration folder so you can just fill it
- hint is just small part of filename so it is more readable after some time

**check**
- check complete migration folder for correct encoding / utf8 without bom

**migrate**
- execute migration of each of migration files, if it was not executed before

## api
- prefered instalation is via composer (by _composer require SoftwareStudio\DatabaseMigration 1.0.*_)
- but you will need to add link to this repository to your composer.json till I create packagist profile
```
"repositories": [
		{ "type": "vcs", "url": "https://github.com/antoninrykalsky/DatabaseMigration" }
],
```