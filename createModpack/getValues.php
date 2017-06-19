<?php

if( !@$_SERVER["HTTP_X_REQUESTED_WITH"] ){
    header('HTTP/1.1 403 Forbidden');
    exit;
}

include_once('../assets/database/connection.php');

$url = "http://www.wnefficiency.net/exp/expected_tank_values_30.json";

$response = file_get_contents($url);
$json_decoded = json_decode($response);
$ratings = $json_decoded->data;

//$account_id = $finaldata{0}->account_id;

$resultArray = [];

foreach($ratings as $rating) {
//    var_dump($tank->tank_id);
    $resultArray[] = "('NULL','". $rating->IDNum ."','". $rating->expFrag ."', '". $rating->expDamage ."', '". $rating->expSpot ."', '". $rating->expDef ."', '". $rating->expWinRate ."')";
}

//var_dump($resultArray); exit;

$query = "INSERT INTO wn8 (`id`, `tank_id`, `expFrag`, `expDamage`, `expSpot`, `expDef`, `expWinRate`) VALUES ";

$query .= implode(",",$resultArray);

//var_dump($query);

$result = $conn->query($query);

$conn->close();


//var_dump($json_decoded_tanks->data->account_id);
//exit;
//
//$json = json_encode();
//echo($json);
