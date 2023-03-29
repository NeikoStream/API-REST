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
                deliver_response(201, "Clé JWT valide", NULL);
                $idUser = getPayloadUser(get_bearer_token());
                //Si publisher
                if(getRole($idUser) == 1){
                    //getMyArticles
                    if(isset($_GET["methode"])){
                        if($_GET["methode"] == 'myArticles'){
                            deliver_response(201, "Articles de l'utilisateur", getMyArticles($idUser));
                        } //GetAllArticles avec nblike / nb dislike
                        else {
                            deliver_response(201, "Mauvaise méthode renseigné", NULL);
                        }
                    }else {
                            deliver_response(201, "Articles All sans le détail des likes",getPuArticles());
                    }
                    

                }//Si Moderateur 
                elseif (getRole($idUser) == 2) {
                    //GetAllArticles avec toutes les infos
                }//Si autres 
                else{
                    deliver_response(201, "Probleme de role (ROLE INEXISTANT)", getRole($idUser)[0]);
                }
            } else {
                deliver_response(201, "CLE JWT NON VALIDE", NULL);
            }
        } else 
        //Sinon faire l'action non authentifier
        {
            //GetAllArticles (Sans détail)
            deliver_response(201, "Get Default Reussit", getDeArticles());
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
                if(isset($body['contenu'])){
                    postPuArticle($body['contenu'],$idUser)
                    deliver_response(201, "Articles créer avec succès", $body['user'] );
                }else{
                    deliver_response(201, "Erreur, pas de contenu renseigner", NULL);
                }
                
            } else {
                deliver_response(201, "Clé JWT non valide", NULL);
            }
        } else 
        //Sinon erreur Clé non entrer
        {
            //GetAllArticles (Sans détail)
            deliver_response(201, "Erreur aucune clé Bearer entrer pas de POST possible", NULL);
        }
        break;
    /// Cas de la méthode PATCH
    case "PATCH" :
        //verifier que l'utilisateur possède un JETON JWT
        if(!is_null(get_bearer_token())){
            //verifier la validiter du jeton
            if(is_jwt_valid(get_bearer_token())){
                
                deliver_response(201, "Ca passe", NULL);
            } else {
                deliver_response(201, "Ca casse", NULL);
            }
        } else 
        //Sinon erreur Clé non entrer
        {
            //GetAllArticles (Sans détail)
            deliver_response(201, "Erreur aucune clé Bearer entrer pas de POST possible", NULL);
        }
        break;

    default :
    /// Renvoie les articles sans détail (GET)
        deliver_response(201, "Get Default Reussit 2", getDeArticles());
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