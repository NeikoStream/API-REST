# API-REST
Projet Universitaire API REST avec Jeton JWT


# Introduction
Cette API REST a pour but de fournir un ensemble de fonctionnalités permettant aux utilisateurs de créer, lire, mettre à jour et supprimer des articles, ainsi que de liker ou disliker des publications. Pour pouvoir accéder à ces fonctionnalités, les utilisateurs doivent s'authentifier via un jeton JWT valide.

# Prérequis
Avant d'utiliser cette API REST, vous devez vous assurer que vous disposez des éléments suivants :

Un compte utilisateur pour obtenir un jeton JWT.
Un environnement de développement, tel que Postman ou un navigateur web, pour tester les fonctionnalités de l'API REST.
Installation
Clonez le dépôt git sur votre machine locale.
Assurez-vous d'avoir les dépendances nécessaires pour exécuter l'API REST.
Lancez l'API REST sur votre environnement de développement.
Utilisation
Voici les fonctionnalités disponibles via cette API REST :

Créer un article en indiquant le contenu dans le corps de la requête.
Récupérer tous les articles avec les détails suivants : idArticle, contenu, datePublication, l'utilisateur qui l'a publié, nombre de likes, nombre de dislikes, liste des utilisateurs qui ont liké, liste des utilisateurs qui ont disliké.
Récupérer tous les articles de l'utilisateur authentifié avec les détails suivants : idArticle, contenu, datePublication, nombre de likes, nombre de dislikes, liste des utilisateurs qui ont liké, liste des utilisateurs qui ont disliké.
Modifier un de ses articles en indiquant dans le corps de la requête le contenu ainsi que l'identifiant de l'article à modifier (idArticle).
Supprimer un de ses articles en indiquant son identifiant en paramètre.
Ajouter un like ou un dislike à une publication en indiquant l'état du like dans le corps de la requête (1 pour like, 2 pour dislike).
Récupérer un jeton JWT valide en fournissant des identifiants de connexion (user, mdp) afin de s'authentifier sur l'API REST.
Exemples
# Voici quelques exemples de requêtes que vous pouvez effectuer via cette API REST :

GET /articles : récupère tous les articles avec leurs détails.
GET /articles?methode=myArticles : récupère tous les articles de l'utilisateur authentifié avec leurs détails.
POST /articles : crée un nouvel article en indiquant le contenu dans le corps de la requête.
PUT /articles/{idArticle} : modifie l'article correspondant à l'identifiant indiqué en paramètre.
DELETE /articles/{idArticle} : supprime l'article correspondant à l'identifiant indiqué en paramètre.
POST /auth : récupère un jeton JWT valide en fournissant des identifiants de connexion (user, mdp).
Auteurs
Ce projet a été réalisé par [Nom de l'auteur 1] et [Nom de l'auteur 2]. Vous pouvez les contacter à [adresse e-mail de l'auteur 1] et [adresse e-mail de l'auteur 2].
