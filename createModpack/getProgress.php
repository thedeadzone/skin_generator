<?php
ob_start();
session_start();
session_write_close();
$output = array();
$output['progress'] = $_SESSION['progress'];
echo json_encode($output);