<?php
    
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toEvent();
    
    $event = new ShootEvent();
    $event->getInput();
    $name = $event->getName();
    
    print json_encode($event->toArray());
    print DIVIDER;
    
    $fid = fopen(EVENT_FILE,"w");
    if ($fid!==false) {
        fwrite($fid,$event->displayText());
        fclose($fid);
        print "Data saved!";
    } else {
        print "Error in opening file.";
    }
    
    try {
        $dir->toShoot();
        rename($dir->getEventDir(),$dir->getShootDir() . "\\$name");
    } catch (E_WARNING $e) {
        print "Event name already exists!";
        //print $e . "<br/>\n";
    }
    
    Shoot::updateStations($dir);
    







?>