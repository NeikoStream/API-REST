<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
include('myFunction.php');
include('jwt_utils.php');
 

 /// Paramétrage de l'entête HTTP (pour la réponse au Client)
 header("Content-Type:application/json");


 /// Identification du type de méthode HTTP envoyée par le client
 $http_method = $_SERVER['REQUEST_METHOD'];
 switch ($http_method){
    /// Cas de la méthode GET
    case "GET" :
        //verifier que l'utilisateur possède un JETON JWT
        if(!is_null(get_bearer_token())){
            //verifier la validiter du jeton
            if(is_jwt_valid(get_bearer_token())){
                $idUser = getPayloadUser(get_bearer_token());
                //Si publisher
                if(getRole($idUser) == 2){
                    //getMyArticles
                    if(isset($_GET["methode"])){
                        if($_GET["methode"] == 'myArticles'){
                            $articles=getMyArticles($idUser);
                            //validité de getMyArticles
                            if ($articles==0){
                                deliver_response(400, "Erreur base de données execution", NULL);
                            }else{
                                deliver_response(200, "Get de mes articles réussit !", $articles);
                            }
                        } //GetAllArticles avec nblike / nb dislike
                        else {
                            deliver_response(400, "Mauvaise méthode renseigné", NULL);
                        }
                    }else {
                        //getAllArticles
                        $articles=getPuArticles();
                        if ($articles==0){
                                deliver_response(400, "Erreur base de données execution", NULL);
                        }else{
                                deliver_response(200, "Get All Articles Publisher", $articles);
                            }
                    }
                    

                }//Si Moderateur 
                elseif (getRole($idUser) == 1) {
                    //GetAllArticles avec toutes les infos
                    $articles=getMoArticles();
                        if ($articles==0){
                                deliver_response(400, "Erreur base de données execution", NULL);
                        }else{
                                deliver_response(200, "Get All Articles Moderateur", $articles);
                            }
                }//Si autres 
                else{
                    deliver_response(401, "Probleme de role (ROLE INEXISTANT)", getRole($idUser)[0]);
                }
            } else {
                deliver_response(401, "CLE JWT NON VALIDE", NULL);
            }
        } else 
        //Sinon faire l'action non authentifier
        {
            //GetAllArticles (Sans détail)
            $articles=getDeArticles();
            if ($articles==0){
                deliver_response(400, "Erreur base de données execution", NULL);
            }else{
                deliver_response(200, "Get Default Reussit", $articles);
            }            
        }
        break;
    /// Cas de la méthode POST
    case "POST" :
        //verifier que l'utilisateur possède un JETON JWT
        if(!is_null(get_bearer_token())){
            //verifier la validiter du jeton
            if(is_jwt_valid(get_bearer_token())){
                $idUser = getPayloadUser(get_bearer_token());
                $postedData = file_get_contents('php://input');
                $body = json_decode($postedData,true);
                //Verif si Publisher
                if(getRole($idUser) == 2){
                    
                    //si il y a un contenu
                    if(isset($body['contenu'])){
                        $post = postPuArticle($body['contenu'],$idUser);
                        if($post==0){
                            deliver_response(400, "Erreur base de données execution", NULL);
                        } else {
                            deliver_response(201, "Articles créer avec succès", $body['contenu'] );
                        }
                        
                    } //si il y a un id et un etatlike
                    elseif(isset($body['id']) && isset($body['EtatLike'])) {
                        
                        $like =  postLikeEtatArticles($body['id'],$idUser,$body['EtatLike']);
                        if($like==0){
                            deliver_response(404, "Erreur d'execution (Article n'existe pas)", NULL);
                        } else {
                            deliver_response(201, "Like/Dislike bien ajouter !", NULL);
                        }     
                    } else{
                        deliver_response(400 , "Parametre non valide !", NULL);
                    }
                }else{
                        deliver_response(401, "Erreur mauvais rôle !", NULL);
                }
                
                
            } else {
                deliver_response(401, "Clé JWT non valide", NULL);
            }
        } else 
        //Sinon erreur Clé non entrer
        {
            //Pas de clé JWT rentré 
            deliver_response(403, "Erreur aucune clé Bearer entrer pas de POST possible", NULL);
        }
        break;
    /// Cas de la méthode PATCH
    case "PATCH" :
        //verifier que l'utilisateur possède un JETON JWT
        if(!is_null(get_bearer_token())){
            //verifier la validiter du jeton
            if(is_jwt_valid(get_bearer_token())){
                $idUser = getPayloadUser(get_bearer_token());
                $postedData = file_get_contents('php://input');
                $body = json_decode($postedData,true);
                if(getRole($idUser) == 2){
                    if(isset($body["id"]) && isset($body["contenu"])){
                        if(getIdUser($body["id"]) == $idUser){
                            $edit = patchPuArticles($body["contenu"], $body["id"]); 
                            if ($edit==0){
                                    deliver_response(400, "Erreur base de données execution", NULL);
                                }else{
                                    deliver_response(201, "Articles modifier avec succés !", $body["contenu"]);
                            }
                        } else {
                            deliver_response(404, "Article n'appartient pas a l'utilisateur ou Article n'exite pas", NULL);
                        }
                    } else{
                        deliver_response(400, "Mauvais parametre (id / contenu) !", NULL);
                    }
                } else {
                    deliver_response(401, "Erreur mauvais rôle !", NULL);
                }
            } else {
                deliver_response(401, "JWT non valide !", NULL);
            }
        } else 
        //Sinon erreur Clé non entrer
        {
            //GetAllArticles (Sans détail)
            deliver_response(403, "Erreur aucune clé Bearer entrer pas de POST possible", NULL);
        }
        break;
    /// Cas de la méthode DELETE
    case "DELETE" :
        //verifier si le jeton a été renseigner
        if(!is_null(get_bearer_token())){
            //verifier la validiter du jeton
            if(is_jwt_valid(get_bearer_token())){
                $idUser = getPayloadUser(get_bearer_token());
                //Si User
                if(getRole($idUser) == 2){
                    if(isset($_GET["id"])){
                        if(getIdUser($_GET["id"]) == $idUser){
                            $delete = deleteArticle($_GET["id"]);
                            if ($delete==0){
                                    deliver_response(400, "Erreur base de données execution", NULL);
                                }else{
                                    deliver_response(200, "Articles bien supprimer !", NULL);
                            }
                        }else{
                            deliver_response(404, "Erreur : Articles n'appartient pas a user ou n'existe pas !", NULL);
                        }
                        
                    }else {
                        deliver_response(400, "Mauvais parametre pour Delete !", NULL);
                    }
                    //Si Moderateur
                } elseif(getRole($idUser) == 1){
                    if(isset($_GET["id"])){
                        $delete = deleteArticle($_GET["id"]);
                        if ($delete==0){
                                deliver_response(400, "Erreur base de données execution", NULL);
                            }else{
                                deliver_response(200, "Articles bien supprimé !", NULL);
                            }
                    }else {
                        deliver_response(400, "Erreur : Renseigner un idArticle !", NULL);
                    }
                } else {
                    deliver_response(401, "Erreur mauvais rôle !", NULL);
                }
                
            } else {
                deliver_response(401, "JWT Token non valide !", NULL);
            }
        } else 
        //Sinon erreur Clé non entrer
        {
            deliver_response(403, "Erreur aucune clé Bearer entrer pas de POST possible", NULL);
        }
        break;

    default :
    /// Renvoie les articles sans détail (GET)
        deliver_response(200, "Get Default", getDeArticles());
        break;
}

/// Envoi de la réponse au Client
function deliver_response($status, $status_message, $data){
    /// Paramétrage de l'entête HTTP, suite
    header("HTTP/1.1 $status $status_message");
    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    echo $json_response;
}
?>