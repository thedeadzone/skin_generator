<?php

//Removes all generator modpacks
$files = glob('../modpacks/{,.}*', GLOB_BRACE);
foreach($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

$filesArray = glob('../createModpack/*');

$dontDelete = ['../createModpack/getProgress.php', '../createModpack/getTanks.php', '../createModpack/getUser.php',
    '../createModpack/getValues.php', '../createModpack/saveFeedback.php'];

// Only delete the non crucial files
foreach($filesArray as $file) {
    if (is_file($file)) {
        if(!in_array($file, $dontDelete)) {
            unlink($file);
        }
    }
}