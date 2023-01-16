<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dirCopy = new DirList();
    $dir->getInput();
    $dir->toShoot();
    
    $name = filter_input(INPUT_POST,"name");
    $event = new ShootEvent();
    
    if (!filter_has_var(INPUT_POST,"copyName")) {
        try {
            $dir->setEventDir($name);
            mkdir($dir->getEventDir());
            $dir->toEvent();
            $fid = fopen(STATION_FILE,"w");
            fclose($fid);
            $event->setName($name);
            $event->saveData($dir);
        } catch (E_WARNING $e) {
            print "Error creating event.<br/>\n";
        }
    } else {        
        $dirCopy = clone $dir;
        $dirCopy->setEventDir(filter_input(INPUT_POST,"copyName"));
        try {
            $dir->setEventDir($name);
            mkdir($dir->getEventDir());
            copy($dirCopy->getEventDir() . "\\" . STATION_FILE,$dir->getEventDir() . "\\"  . STATION_FILE);
            copy($dirCopy->getEventDir() . "\\" . EVENT_FILE,$dir->getEventDir() . "\\"  . EVENT_FILE);
            $dir->toEvent();
            $event->loadData();
            $event->setName($name);
            $event->saveData($dir);
        } catch (E_WARNING $e) {
            print "Error creating event.<br/>\n";
        }
    }





?>