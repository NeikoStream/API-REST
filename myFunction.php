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
        return NULL;
    }
    //tous les likes/dislikes par articles (peut etre mettre un id en param)
    function getLikeArticles(){
        return NULL;
    }

    //delete un article
    function deleteArticle($idArticle){
        return NULL;
    }


    /*Publisher*/

    //post un article
    function postPuArticle($contenu, $idPublisher){
        return NULL;
    }
    //peut etre faire juste un getArticles en commun vue qu'il renvoie la meme chose
    function getPuArticles(){
        return NULL;
    }
    //renvoie les articles d'un utilisateur
    function getMyArticles($idPublisher){
        return NULL;
    }
    //modifie un article
    function patchPuArticles($contenu,$idArticle){
        return NULL;
    }
    //supprimer ses articles (la meme que get)
    function deleteMyArticles($idArticle){
        return NULL;
    }
    //LIKE/DISLIKE
    function postLikeArticles($idArticle,$idPublisher){
        return NULL;
    }
    function postDisLikeArticles($idArticle,$idPublisher){
        return NULL;
    }

    /*Default*/
    //get les articles sans info juste le contenue
    function getDeArticles(){
        return 'Ceci est un get default d articles';
    }



    #Ancienne API CHUCK
    function getById($id){
        $linkpdo = connexionDB();
        $recupid = $linkpdo->prepare('SELECT * FROM chuckn_facts WHERE id = :id');
        $recupid->execute(array('id' => $id));
        $chuck = $recupid->fetchALL();
        return $chuck;
    }

    function getAll(){
        $linkpdo = connexionDB();

        $recupall = $linkpdo->prepare('SELECT * FROM chuckn_facts');
        if($recupall->execute()){
            $chuck = $recupall->fetchALL();
            return $chuck;

        } else {
            return "ça marche po";
        }  
    }

    function edit($id, $phrase){
        $linkpdo = connexionDB();
        $edit = $linkpdo->prepare('UPDATE chuckn_facts SET phrase = :phrase WHERE id=:id');
        return($edit->execute(array('phrase' => $phrase, 'id' => $id)));
    }

    function addPhrase($phrase){
        $linkpdo = connexionDB();
        $edit = $linkpdo->prepare('insert chuckn_facts SET phrase = :phrase WHERE id=:id');

    }
?>
