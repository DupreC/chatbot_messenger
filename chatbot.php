<?php
/**
 * Vérification du Token du de FaceBook developers
 */
$access_token = "EAAImAP6YXzsBADwnZA1IPrk9vmC3ZCJy4EZAmGw0KmmxxRKzZBklg5QZBEUZCNhXgDUeLFeAuN27UBIpWxzfKA7yv39uTMsB8GFkHmfMJ0WjLdFErL2hDIioEKGUZBpwfP0GSZAONBzRkXj5YSV1ffnTHBzOenQGdN0llNjGoX0Mjet7shWniyZCU5zmTRnZAOuQQZD";
$verify_token = "fb_time_bot";
$hub_verify_token = null;

if(isset($_REQUEST['hub_challenge'])) {
 $challenge = $_REQUEST['hub_challenge'];
 $hub_verify_token = $_REQUEST['hub_verify_token'];
}

if ($hub_verify_token === $verify_token) {
 echo $challenge;
}
/**
 * Initialisation des données à récupérer
 */
$input = json_decode(file_get_contents('php://input'), true);

 $sender = $input['entry'][0]['messaging'][0]['sender']['id']; //sender facebook id
 $message = $input['entry'][0]['messaging'][0]['message']['text']; //text that user 
$message_to_reply = '';
/**
 * On inclue les fonctions liées à l'api de foot
 */
include 'api_foot.php';
$result='';

//On test la chaine de caractère "resultat:" pour savoir si elle existe

if(preg_match('[resultat:]', str_replace(' ', '', strtolower($message)))) {

//On inclue la fonctions pour tester une chaine entre 2 balises
	include 'test_chaine.php'; 
	$result = RechercheScoreTeam($equipe1, $equipe2);

}
 //On test la chaine de caractère "classement" pour savoir si elle existe
else if(preg_match('[classement]', strtolower($message))) {
 	$result = ClassementEquipe();
 }
//On test la chaine de caractère "infos" ou "commande" pour savoir si elle existe
else if(preg_match('[info|commande]', strtolower($message))) {
 	$result = "Pour avoir les résultats entre deux équipe, tapez : \nResultat : nom equipe1/nom equipe2 \nPour avoir le classement des équipes, tapez : classement";
 }
  ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
 if($result != '') {
 $message_to_reply = $result;
 }
//si aucune correspondance existe, on demande lui fait remarquer
 else{
 $message_to_reply = "Huh? la syntaxe doit être differente. \nPour avoir la liste des commandes, tapez : \ninfos commande";
}

//API Url
$url = 'https://graph.facebook.com/v3.2/me/messages?access_token='.$access_token;

//Initiate cURL.
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//The JSON data.
$jsonData = [
    'recipient' => [ 'id' => $sender ],
    'message' => [ 'text' => $message_to_reply ]
];
//Encode the array into JSON.
$jsonDataEncoded = $jsonData;

//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);

//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonDataEncoded));

//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

//Execute the request
if(!empty($input['entry'][0]['messaging'][0]['message'])){
 $result = curl_exec($ch);
}

curl_close($ch);