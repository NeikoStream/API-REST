<?php 
include('jwt_utils.php');


 /// Paramétrage de l'entête HTTP (pour la réponse au Client)
 header("Content-Type:application/json");


 /// Identification du type de méthode HTTP envoyée par le client
 $http_method = $_SERVER['REQUEST_METHOD'];
 switch ($http_method){
    /// Cas de la méthode GET
    /// Cas de la méthode POST
    case "POST" :
        /// Récupération des données envoyées par le Client
        $header = array('alg'=>'HS256','typ'=>'JWT');

        $mdp = "admin";
        $user = "admin";
        
        if(isset($_GET['user']) && isset($_GET['mdp'])){
        $payload = array('user'=>$_GET['user'], 'exp'=>(time()+60));
        
            if($user == $_GET['user'] && $mdp == $_GET['mdp']){
                $jwt = generate_jwt($header, $payload);
                if(is_jwt_valid($jwt)){
                    deliver_response(200, "Clé JWT créer avec succés", $jwt);
                }else{
                    deliver_response(201, "Clé JWT échoué", NULL);
                }
            }else{
                deliver_response(202, "Erreur mauvaise combinaison USER/MDP", NULL);
            }
        }

        if(isset($_GET['token'])){
            if(is_jwt_valid($_GET['token'])){
                deliver_response(200, "Clé JWT valide", $_GET['token']);
            }else{
                deliver_response(201, "Clé JWT non valide", NULL);
            }
        }

        
        

        $postedData = file_get_contents('php://input');

        /// Traitement

        break;
    default :
    /// Récupération de l'identifiant de la ressource envoyé par le Client

        deliver_response(202, "Manque user / mdp", NULL);
        /// Envoi de la réponse au Client
        
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