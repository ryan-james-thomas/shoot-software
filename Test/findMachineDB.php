<?php

    $directory = filter_input(INPUT_POST,"userDirectory");
    $directory = './' . $directory;

    $dp = opendir($directory);
    $currentFile = "";
    while ($currentFile !== false) {
        $currentFile = readdir($dp);
        $filesArray[] = $currentFile;
        if (strpos($currentFile,"_MachineDB.txt") !== false) {
            $machineDB[] = substr($currentFile,0,strlen($currentFile)-14);
        }
    }
    
    foreach ($machineDB as $item) {
        print "<p>$item</p>\n";
    }
    
?>