<?php

if( !@$_SERVER["HTTP_X_REQUESTED_WITH"] ){
    header('HTTP/1.1 403 Forbidden');
    exit;
}

include_once('../assets/database/connection.php');

$url = "https://api.worldoftanks.eu/wot/encyclopedia/vehicles/?application_id=bd84a9e4307514da892224b760b4db57";

$response = file_get_contents($url);
$json_decoded = json_decode($response);
$tanks = $json_decoded->data;

//$account_id = $finaldata{0}->account_id;

$resultArray = [];

foreach($tanks as $tank) {
//    var_dump($tank->tank_id);
    $resultArray[] = "('NULL','". $tank->tank_id ."', '". $tank->type ."', '". $tank->short_name ."', '". $tank->name ."', '". $tank->nation ."', '". $tank->tier ."', '". $tank->is_premium ."', '". $tank->images->small_icon ."', '". $tank->images->contour_icon ."', '". $tank->images->big_icon ."', '". $tank->tag ."')";
}

//var_dump($resultArray); exit;

$query = "INSERT INTO tanks (`id`, `tank_id`, `type`, `short_name`, `name`, `nation`, `tier`, `is_premium`, `small_icon`, `contour_icon`, `big_icon`, `tag`) VALUES ";

$query .= implode(",",$resultArray);

var_dump($query);

$result = $conn->query($query);

$conn->close();


//var_dump($json_decoded_tanks->data->account_id);
//exit;
//
//$json = json_encode();
//echo($json);
