<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $shoot = new Shoot();
    $shoot->loadData();
    $events = $shoot->loadEvents($dir);
    //$s = $events[0]->getStations();
    //print implode(", ",$s[1]->getTargetColours()) . "<br/>\n";
    
    
    print json_encode($shoot->toArray());
    print DIVIDER;
    
    $numTargets = $shoot->setTypeCount();
    $numBoxes = $shoot->getNumBoxes();
    
    displayTargetTable($numTargets,$numBoxes);
    
    print "---EVENT SUMMARY BELOW---\n";
    
    $shoot->dispEventHTML();

    




?>