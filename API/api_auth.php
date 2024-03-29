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

        //Methode création de Token JWT
        if(isset($postedData['user']) && isset($postedData['mdp'])){
            $timeJWT = 3600*24*30;
            $recupuser = getUser($postedData['user']); 
            $payload = array('user'=>$recupuser[0]['idUser'], 'exp'=>(time()+$timeJWT));
            $header = array('alg'=>'HS256','typ'=>'JWT');
            
            //Verification de l'utilisateur
            if($recupuser[0]['login'] != null && password_verify($postedData['mdp'], $recupuser[0]['password'])){
                //génération Clé + Vérif
                $jwt = generate_jwt($header, $payload);
                if(is_jwt_valid($jwt)){
                    deliver_response(201, "Clé JWT créer avec succés", $jwt);
                }else{
                    deliver_response(400, "Clé JWT échoué", NULL);
                }
            }else{
                deliver_response(401, "Erreur mauvaise combinaison USER/MDP", NULL);
            }
        }

        //Methode de verification de Token
        if(!is_null(get_bearer_token())){
            if(is_jwt_valid(get_bearer_token())){
                deliver_response(200, "Clé JWT valide", get_bearer_token());
            }else{
                deliver_response(400, "Clé JWT non valide", get_bearer_token());
            }
        }


        break;
    default :
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