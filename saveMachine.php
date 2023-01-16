<?php
    //Adds a new machine to the database and saves it to the text file
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");

    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $shoot = new Shoot();
    
    $machines = Machine::loadData();
    $idx = filter_input(INPUT_POST,"idx");
    $idx = (int)$idx;
    
    $machines[$idx] = new Machine();
    $machines[$idx]->getInput();
    
    $val = $machines[$idx]->checkDuplicateNames($machines,$idx);
    if ($val === false) {
        Machine::saveData($machines);
        Shoot::updateStations($dir,$machines);  //Change station properties for all events
    }
    
    print json_encode($val);
   
    //print json_encode($machines[$idx]->toArray($idx));
    //print DIVIDER;
    //$machines[$idx]->displayHTMLbutton($idx,"clr" . (($idx%2)+1));

    
?>