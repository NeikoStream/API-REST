<?php 
include('jwt_utils.php');
include('myFunction.php');

 /// Paramétrage de l'entête HTTP (pour la réponse au Client)
 header("Content-Type:application/json");


 /// Identification du type de méthode HTTP envoyée par le client
 $http_method = $_SERVER['REQUEST_METHOD'];
 switch ($http_method){
    /// Cas de la méthode GET
    /// Cas de la méthode POST
    case "POST" :
        /// Récupération des données envoyées par le Client
        
        $postedData = (array) json_decode(file_get_contents('php://input'),TRUE);

        if(isset($postedData['user']) && isset($postedData['mdp'])){
            $recupuser = getUser($postedData['user']); 
            $payload = array('user'=>$recupuser[0]['idUser'], 'exp'=>(time()+60));
            $header = array('alg'=>'HS256','typ'=>'JWT');
            
            print_r($recupuser[0]['login']);
            if($recupuser[0]['login'] != null && password_verify($postedData['mdp'], $recupuser[0]['password'])){
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

        if(!is_null(get_bearer_token())){
            if(is_jwt_valid(get_bearer_token())){
                deliver_response(200, "Clé JWT valide", get_bearer_token());
            }else{
                deliver_response(201, "Clé JWT non valide", get_bearer_token());
            }
        }

        
        

        $postedData = file_get_contents('php://input');

        /// Traitement

        break;
    default :
    /// Erreur si 0 info

        deliver_response(202, "Manque user / mdp ou token pour verifier", NULL);
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