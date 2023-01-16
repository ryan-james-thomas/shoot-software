<?php

    $directory = filter_input(INPUT_POST,"userDirectory");
    $directory = './' . $directory;

    $dp = opendir($directory);
    $currentFile = "";
    while ($currentFile !== false) {
        $currentFile = readdir($dp);
        if (strpos($currentFile,"MachineDatabase.txt") !== false) {
            $machineDB = $currentFile;
            break;
        }
    }
    
    print $machineDB;
    
?>