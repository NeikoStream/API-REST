<?php
    function connexionDB(){  
        $db_username = 'root';
        $db_password = '';
        $db_name = 'api_rest';
        $db_host = '127.0.0.1:3306';

        try {
            $linkpdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=UTF8", $db_username, $db_password);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage()); 
        }
        return $linkpdo;
    }

    function genPasswordHash($mdpClair){
        return password_hash($mdpClair, PASSWORD_DEFAULT, ["cost" => 12]);
    }

    function newUser($user,$mdp){
        $linkpdo = connexionDB();
        if(getUser($user)['user'][0] == NULL){
            $new = $linkpdo->prepare('INSERT INTO user(user,passwordKey) VALUES (:user,:pwdHash)');
            return($new->execute(array('user' => $user, 'pwdHash' => genPasswordHash($mdp))));
        }
    }

    function getUser($user){
        $linkpdo = connexionDB();
        $getUser  = $linkpdo->prepare('SELECT * FROM user WHERE user = :user');
        $getUser->execute(array('user' => $user));
        $user = $getUser->fetchALL();
        return $user;
    }

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
