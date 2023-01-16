<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    
    $shoot = new Shoot();
    $shoot->setName(filter_input(INPUT_POST,"name"));
    $shoot->setDates(filter_input(INPUT_POST,"start"),filter_input(INPUT_POST,"end"));
    $shoot->setNotes(filter_input(INPUT_POST,"notes"));

    $shoot->loadEvents($dir);
    $shoot->saveMachineUtilisation();
    print json_encode($shoot->toArray());
    print DIVIDER;
    
    $fid = fopen(SHOOT_FILE,"w");
    if ($fid!==false) {
        fwrite($fid,$shoot->displayText());
        fclose($fid);
        print "Data saved!";
    } else {
        print "Error in opening file.";
    }
    
    try {
        $dir->toUser();
        rename($dir->getShootDir(),$dir->getUserDir() . "\\" . $shoot->getName());
    } catch (E_WARNING $e) {
        print "Event name already exists!";
        //print $e . "<br/>\n";
    }





?>