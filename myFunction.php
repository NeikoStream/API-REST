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

    //tous les articles + nblike + nb dislike
    function getMoArticles(){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT idArticle, COUNT(case when etatLike=1 then 1 else 0 end) AS nbLike, COUNT(case when etatLike=0 then 1 else 0 end) AS nbDislike, contenu, datePublication, login FROM articles, utilisateur, liker WHERE articles.idUser=utilisateur.idUser and liker.idArticle=articles.idArticle group by contenu, datePublication, login');
        if ($requete -> execute()){
            $articles = $requete->fetchALL();
            return $articles;
        }else{
            return 0;
        }
    }

    //tous les likes/dislikes par articles (peut etre mettre un id en param)
    function getLikeArticles(){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT idArticle, idUser, etatLike FROM liker ORDER BY idArticle, etatLike');
        if ($requete -> execute()){
            $like = $requete->fetchALL();
            return $like;
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
        $requete = $linkpdo->prepare('SELECT idArticle, COUNT(case when etatLike=1 then 1 else 0 end) AS nbLike, COUNT(case when etatLike=0 then 1 else 0 end) AS nbDislike, contenu, datePublication, login FROM articles, utilisateur, liker WHERE articles.idUser=utilisateur.idUser and liker.idArticle=articles.idArticle group by contenu, datePublication, login');
        if ($requete -> execute()){
            $articles = $requete->fetchALL();  
            return $articles;
        }else{
            return 0;
        }
    }

    //renvoie les articles d'un utilisateur
    function getMyArticles($id){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('SELECT idArticle, COUNT(case when etatLike=1 then 1 else 0 end) AS nbLike, COUNT(case when etatLike=0 then 1 else 0 end) AS nbDislike, contenu, datePublication, login FROM articles, utilisateur, liker WHERE articles.idUser=utilisateur.idUser and liker.idArticle=articles.idArticle and articles.idUser=:id group by contenu, datePublication, login');
        if ($requete -> execute(array('id' => $id))){
            $articles = $requete->fetchALL();  
            return $articles;
        }else{
            return 0;
        }
    }

    //modifie un article
    function patchPuArticles($contenu,$idArticle){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('UPDATE articles SET datePublication= ???, contenu=:contenu, WHERE idArticle=:idArticle');
        if ($requete -> execute(array('contenu' => $contenu, 'idArticle' => $idArticle))){
            return 1;
        }else{
            return 0;
        }
    }
   
    //LIKE/DISLIKE
    function postLikeArticles($idArticle,$idPublisher){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('INSERT INTO liker (idArticle, idUser, etatLike) VALUES (:idArticle, :idUser, 1)');
        if ($requete -> execute(array('idArticle' => $idArticle, 'idUser' => $idPublisher))){
            return 1;
        }else{
            return 0;
        }
    }

    function postDisLikeArticles($idArticle,$idPublisher){
        $linkpdo = connexionDB();
        $requete = $linkpdo->prepare('INSERT INTO liker (idArticle, idUser, etatLike) VALUES (:idArticle, :idUser, 0)');
        if ($requete -> execute(array('idArticle' => $idArticle, 'idUser' => $idPublisher))){
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
            $articles = $requete->fetchALL();  
            return $articles;
        }else{
            return 0;
        }
    }
?>