<?php

include_once('../assets/database/connection.php');

$type = $_GET['type'];
$text = $_GET['text'];
$date = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO wot_feedback (`id`, `type`, `text`, `date`) VALUES (NULL, ?, ?, ?)");
$stmt->bind_param('sss', $type, $text, $date);
$stmt->execute();
$conn->close();

echo json_encode('Success');