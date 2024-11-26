# Projet Advent Calendar - Symfony 7
 
Ce projet a été réalisé dans un cadre personnel et famillial. A usage ludique essentiellement.
Il s'agit d'un calendrier de l'avent permettant aux participants de s'inscrire et participer à des petits défis mis en place chaque jour du mois de décembre. 


## Table des matières

- [Stack](#stack)

- [Installation](#installation)

- [License](#license)

  
 ## Stack
 * \>= PHP 8.2 
 * \>= Symfony 7.1
 * Composer
 * Docker
 * MySQL

## Installation

 1. Clonez le dépôt du projet :

```bash
git clone https://github.com/mroulland/advent-calendar.git
cd advent-calendar 
```
2. Installez les dépendances avec Composer :

```bash
composer install
```
3. Configurez .env (ou .env.local) pour connecter la base de données

4. Importez la base de données

```bash
php bin/console doctrine:migrations:migrate
```
5. Démarrage du serveur Symfony.
```
symfony server:start
```
6. Ouvrir le navigateur sur proposée par le serveur : http(s)://localhost:8000 (généralement).


## Licence
Ce projet est sous licence MIT.
