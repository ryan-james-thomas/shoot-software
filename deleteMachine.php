<?php
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");

    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $idx = filter_input(INPUT_POST,"idx");
    if ($idx == -1) {
        $machines = array();
    } else {
        $machines = Machine::deleteMachine($machines,$idx);
    }
     
    print json_encode(Machine::machineArray($machines));
    print DIVIDER;
    
    Machine::saveData($machines);
    
    for ($i=0;$i<sizeof($machines);$i++) {
        $machines[$i]->displayHTMLbutton($i,"clr" . (($i%2)+1));
    }
    
    //Change station properties for all events
    Shoot::updateStations($dir,$machines); 

?>