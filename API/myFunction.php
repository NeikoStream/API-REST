<?php
    function connexionDB(){  
        $db_username = 'root';
        $db_password = '';
        $db_name = 'forum';
        $db_host = '127.0.0.1:3306';

        try {
            $linkpdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=UTF8", $db_username, $db_password);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage()); 
        }
        return $linkpdo;
    }

    ### API_AUTHENTIFICATION
    function genPasswordHash($mdpClair){
        return password_hash($mdpClair, PASSWORD_DEFAULT, ["cost" => 12]);
    }

    function newUser($user,$mdp,$role){
        $linkpdo = connexionDB();
        $new = $linkpdo->prepare('INSERT INTO utilisateur(login, password, idRole) VALUES (:user,:pwdHash,:role)');
        return($new->execute(array('user' => $user, 'pwdHash' => genPasswordHash($mdp), 'role' => $role)));
    }

    function getUser($user){
        $linkpdo = connexionDB();
        $getUser  = $linkpdo->prepare('SELECT * FROM utilisateur WHERE login = :user');
        $getUser->execute(array('user' => $user));
        $user = $getUser->fetchALL();
        return $user;
    }

    function getRole($idUser){
        $linkpdo = connexionDB();
        $getUser  = $linkpdo->prepare('SELECT idRole FROM utilisateur WHERE idUser = :user');
        $getUser->execute(array('user' => $idUser));
        $user = $getUser->fetchALL();
        return $user[0][0];
    }

    ### API_REST
    /*Moderateur*/

    //tous les articles + nblike + nb dislike + likes et dislike
    function getMoArticles(){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT articles.idArticle, contenu, datePublication, u.login, SUM(liker.etatLike = 1) as nbLikes, SUM(liker.etatLike = 0) as nbDislikes, GROUP_CONCAT(CASE liker.etatLike WHEN 1 THEN utilisateur.login END) as listeLikes, GROUP_CONCAT(CASE liker.etatLike WHEN 0 THEN utilisateur.login END) as listeDislikes FROM articles LEFT JOIN liker ON articles.idArticle = liker.idArticle LEFT JOIN utilisateur ON liker.idUser = utilisateur.idUser LEFT JOIN utilisateur u ON articles.idUser = u.idUser GROUP BY articles.idArticle, contenu, datePublication, u.login');
        if ($requete -> execute()){
            $articles = $requete->fetchALL(PDO::FETCH_CLASS);
            return $articles;
        }else{
            return 0;
        }
    }

    //NE SERS PLUS A RIEN
    //tous les likes/dislikes par articles (peut etre mettre un id en param)
    function getLikeArticles(){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT idArticle, idUser, etatLike FROM liker ORDER BY idArticle, etatLike');
        if ($requete -> execute()){
            $like = $requete->fetchALL(PDO::FETCH_CLASS);
            return $like;
        }else{
            return 0;
        }
    }

    //fonction permettant de récupérer l'id de l'auteur de l'article
    function getIdUser($idArticle){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT idUser FROM articles WHERE idArticle=:id');
        if ($requete -> execute(array('id' => $idArticle))){
            $user = $requete->fetchALL();
            if($requete->rowCount() > 0){
                return $user[0]['idUser'];
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    //delete un article
    function deleteArticle($idArticle){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('DELETE FROM liker WHERE idArticle=:id');
        if ($requete -> execute(array('id' => $idArticle))){
            $requete = $linkpdo->prepare('DELETE FROM articles WHERE idArticle=:id');
            if ($requete -> execute(array('id' => $idArticle))){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    /*Publisher*/

    //post un article
    function postPuArticle($contenu, $idPublisher){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('INSERT INTO articles(datePublication, contenu, idUser) VALUES (Now(), :contenu, :iduser)');
        if ($requete -> execute(array('contenu' => $contenu, 'iduser' => $idPublisher))){
            return 1;
        }else{
            return 0;
        }
    }

    //peut etre faire juste un getArticles en commun vue qu'il renvoie la meme chose
    function getPuArticles(){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT articles.idArticle, contenu, datePublication, u.login, SUM(liker.etatLike = 1) as nbLikes, SUM(liker.etatLike = 0) as nbDislikes FROM articles LEFT JOIN liker ON articles.idArticle = liker.idArticle LEFT JOIN utilisateur u ON articles.idUser = u.idUser GROUP BY articles.idArticle, contenu, datePublication, u.login');

        if ($requete -> execute()){
            $articles = $requete->fetchALL(PDO::FETCH_CLASS);  
            return $articles;
            
        }else{
            return 0;
        }
    }

    //renvoie les articles d'un utilisateur
    function getMyArticles($idPublisher){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT articles.idArticle, contenu, datePublication, u.login, SUM(liker.etatLike = 1) as nbLikes, SUM(liker.etatLike = 0) as nbDislikes FROM articles LEFT JOIN liker ON articles.idArticle = liker.idArticle LEFT JOIN utilisateur u ON articles.idUser = u.idUser WHERE articles.idUser=:id GROUP BY articles.idArticle, contenu, datePublication, u.login;');
        if ($requete -> execute(array('id' => $idPublisher))){
            $articles = $requete->fetchALL(PDO::FETCH_CLASS);  
            return $articles;
        }else{
            return 0;
        }
    }

    //modifie un article
    function patchPuArticles($contenu, $idArticle){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('UPDATE articles SET contenu=:contenu WHERE idArticle=:idArticle');
        if ($requete -> execute(array('contenu' => $contenu, 'idArticle' => $idArticle))){
            return 1;
        }else{
            return 0;
        }
    }
   
    //LIKE/DISLIKE
    function postLikeEtatArticles($idArticle,$idPublisher,$etat){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('INSERT INTO liker (idArticle, idUser, etatLike) VALUES (:idArticle, :idUser, :etatLike) ON DUPLICATE KEY UPDATE etatLike=:etatLike');
        if ($requete -> execute(array('idArticle' => $idArticle, 'idUser' => $idPublisher,'etatLike' => $etat))){
            return 1;
        }else{
            return 0;
        }
    }


    /*Default*/
    //get les articles sans info juste le contenue
    function getDeArticles(){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT contenu, datePublication, login FROM articles, utilisateur WHERE articles.idUser=utilisateur.idUser');
        if ($requete -> execute()){
            $articles = $requete->fetchALL(PDO::FETCH_CLASS); 
            return $articles;
        }else{
            return 0;
        }
    }
?>