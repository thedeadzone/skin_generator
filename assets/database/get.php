<?php
include_once('connection.php');

switch($_GET['what']) {
    case 'patients':
        $query = "SELECT * FROM patients";
        break;

    case 'medicines':
        $query = "SELECT * FROM medicines";
        break;

    default:
        $query = "SELECT * FROM patients";
}

$result = $conn->query($query);
$conn->close();


$results = array();
foreach($result as $item) {
    $results[] = $item;
}
//echo $results;
//var_dump($results);

//var_dump($results);


//var_dump($results);
$json = json_encode($results);
echo($json);

//var_dump($json);
