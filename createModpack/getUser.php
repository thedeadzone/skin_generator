<?php

include_once('../assets/database/connection.php');

ini_set('max_execution_time', 600); // 600 = 10 min

session_start();
$_SESSION['progress'] = 0;
session_write_close();

sleep(1);

$username = $_GET['username'];
$hd = $_GET['hd'];
$server = $_GET['server'];

$url = "https://api.worldoftanks.". $server ."/wot/account/list/?application_id=bd84a9e4307514da892224b760b4db57&search=" . $username;

$response = file_get_contents($url);
$json_decoded = json_decode($response);
$finaldata = $json_decoded->data;

$account_id = $finaldata{0}->account_id;

if ($account_id == null) {
    echo 'account_error'; exit;
}

session_start();
$_SESSION['progress'] = 20;
session_write_close();

$tank_url = "https://api.worldoftanks.". $server ."/wot/tanks/stats/?application_id=bd84a9e4307514da892224b760b4db57&account_id=" . $account_id;
$response_tanks = file_get_contents($tank_url);
$json_decoded_tanks = json_decode($response_tanks);

$tanks = $json_decoded_tanks->data->$account_id;

$query3 = "SELECT * FROM tanks";
$result2= $conn->query($query3);

$tanksArray = [];
$tankArrayId = [];
// Retrieves all tanks in the game
foreach($result2 as $result) {
    $tanksArray[] = $result;
    $tankArrayId[] = $result['tank_id'];
}

$playedTanks = [];

// Loops trough tanks
foreach($tanks as $key=>$tank) {
    $right_stat = $tank->all;

    // Only selects tanks that are also played before by the user
    if (in_array($tank->tank_id, $tankArrayId)) {
        $playedTanks[] = $tank;
    }
}

// Gets WN8 expected values
$query4 = "SELECT * FROM wn8";
$result3 = $conn->query($query4);

$wn8Array = [];

foreach($result3 as $result) {
    $wn8Array[] = $result;
}

$tankColorArray = [];
$userTankAndWn8 = [];
// Calculates wn8 per tank and therefore the color required
foreach($playedTanks as $playedTank) {
    $neededTank = ['error', 'error', 'error'];

    foreach($tanksArray as $tank) {
        if ($playedTank->tank_id . '' == $tank['tank_id']) {
            $neededTank = $tank;
        }
    }

    $finalWn8Tank = '';

    foreach($wn8Array as $wn8Tank) {
        if ($playedTank->tank_id . '' == $wn8Tank['tank_id']) {
            $finalWn8Tank = $wn8Tank;
        }
    }

    //Calculates wn8
    $rDAMAGE = ($playedTank->all->damage_dealt / $playedTank->all->battles) / $finalWn8Tank['expDamage'];
    $rSPOT = ($playedTank->all->spotted / $playedTank->all->battles) / $finalWn8Tank['expSpot'];
    $rFRAG = ($playedTank->all->frags / $playedTank->all->battles) / $finalWn8Tank['expFrag'];
    $rDEF = ($playedTank->all->dropped_capture_points / $playedTank->all->battles) / $finalWn8Tank['expDef'];
    $rWIN = ($playedTank->all->wins / $playedTank->all->battles * 100) / $finalWn8Tank['expWinRate'];

    $rWINc = max(0, ($rWIN - 0.71) / (1 - 0.71) );
    $rDAMAGEc = max(0, ($rDAMAGE - 0.22) / (1 - 0.22) );
    $rFRAGc = max(0, min($rDAMAGEc + 0.2, ($rFRAG - 0.12) / (1 - 0.12)));
    $rSPOTc = max(0, min($rDAMAGEc + 0.1, ($rSPOT - 0.38) / (1 - 0.38)));
    $rDEFc = max(0, min($rDAMAGEc + 0.1, ($rDEF - 0.10) / (1 - 0.10)));

    $wn8 = 980*$rDAMAGEc + 210*$rDAMAGEc*$rFRAGc + 155*$rFRAGc*$rSPOTc + 75*$rDEFc*$rFRAGc + 145*MIN(1.8,$rWINc);

    $color = '';

    switch($wn8) {
        case $wn8<=599:
            $color = 'red';
            break;
        case $wn8<=899:
            $color = 'orange';
            break;
        case $wn8<=1199:
            $color = 'yellow';
            break;
        case $wn8<=1799:
            $color = 'green';
            break;
        case $wn8<=2299:
            $color = 'blue';
            break;
        case $wn8>=2300:
            $color = 'purple';
            break;
    }
    $tankColorArray[] = [$neededTank['tag'], $color, $neededTank['nation']];
    $userTankAndWn8[] = [$playedTank, $wn8, $neededTank];
}

$zip = new ZipArchive;
$zipName = 'CustomStatModpack-TDZ-DEV.zip';

// Creates required dir
if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {
    // Finds files based on color, nation and tankname. Then names them the right way ($file[2])
    // Files are then added in the right vehicles/nation/tank dir

    $amountOfTanks = count($tankColorArray);

    foreach ($tankColorArray as $key => $tank) {
        if($key != 0) {
            $zip->open($zipName);
        }

        $A8th = round($amountOfTanks / 8);

        switch ($key) {
            case $A8th:
                session_start();
                $_SESSION['progress'] = 30;
                session_write_close();
                break;

            case ($A8th*2):
                session_start();
                $_SESSION['progress'] = 40;
                session_write_close();
                break;

            case ($A8th*3):
                session_start();
                $_SESSION['progress'] = 50;
                session_write_close();
                break;

            case ($A8th*4):
                session_start();
                $_SESSION['progress'] = 60;
                session_write_close();
                break;

            case ($A8th*5):
                session_start();
                $_SESSION['progress'] = 70;
                session_write_close();
                break;
            case ($A8th*6):
                session_start();
                $_SESSION['progress'] = 80;
                session_write_close();
                break;
            case ($A8th*7):
                session_start();
                $_SESSION['progress'] = 90;
                session_write_close();
                break;
            case ($A8th*8):
                session_start();
                $_SESSION['progress'] = 100;
                session_write_close();
                break;
        }

        $nation = '';

        // Switch that sets the right country as its wrong in tank data.
        switch($tank[2]) {
            case 'usa':
                $nation = 'american';
                break;
            case 'ussr':
                $nation = 'russian';
                break;
            case 'france':
                $nation = 'french';
                break;
            case 'china':
                $nation = 'chinese';
                break;
            case 'germany':
                $nation = 'german';
                break;
            case 'uk':
                $nation = 'british';
                break;
            case 'sweden':
                $nation = 'sweden';
                break;
            case 'japan':
                $nation = 'japan';
                break;
            case 'czech':
                $nation = 'czech';
                break;
        }

        // Picks HD or non HD files from the right folder and adds them to the Zip
        if ($hd == 'true') {
            // With HD you also use the non HD files for longer distances?
            try {
                $file = scandir('../assets/files/hd/'. $tank[1] .'/vehicles/' . $nation . '/' . $tank[0] . '/');
            } catch (Exception $e) {
//                var_dump($e->getMessage()); exit;
            }
            if (count($file) == 3) {
                $zip->addFile('../assets/files/hd/'. $tank[1] .'/vehicles/' . $nation . '/' . $tank[0] . '/' . $file[2], 'vehicles/' .$nation . '/' . $tank[0] . '/' .$file[2]);
            } else {
//                var_dump("Error at : ". '../assets/files/hd/'. $tank[1] .'/vehicles/' . $nation . '/' . $tank[0] . '/');
            }
        }

        try {
            $file = scandir('../assets/files/not-hd/'. $tank[1] .'/vehicles/' . $nation . '/' . $tank[0] . '/');
        } catch (Exception $e) {
//            var_dump($e->getMessage()); exit;
        }
        if (count($file) == 3) {
            $zip->addFile('../assets/files/not-hd/'. $tank[1] .'/vehicles/' . $nation . '/' . $tank[0] . '/' . $file[2], 'vehicles/' .$nation . '/' . $tank[0] . '/' .$file[2]);
        } else {
//            var_dump("Error at : ". '../assets/files/not-hd/'. $tank[1] .'/vehicles/' . $nation . '/' . $tank[0] . '/');
        }
        $zip->close();
    }

    $zip->open($zipName);
    $zip->addFile('../permission/create-new-modpack.url', 'create-new-modpack-TDZ.url');
    $zip->close();
}

$date = date("Y-m-d H:i:s");
$map_id = uniqid();
mkdir('../modpacks/' . $map_id, 0700);

copy($zipName, '../modpacks/' . $map_id . '/' .$zipName );
copy('../permission/.htaccess', '../modpacks/' . $map_id . '/.htaccess');
$download_url = "../modpacks/" . $map_id . '/' .$zipName;

$query = "INSERT INTO modpack (`id`, `map_id`, `username`, `date`) VALUES (NULL, '".$map_id."', '".$username."', '".$date."')";
$result = $conn->query($query);
$conn->close();

unlink($zipName);

echo json_encode([$download_url, $userTankAndWn8]);