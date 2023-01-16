<?php
    //Reads machine database and sends JSON to client
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    print json_encode(Machine::machineArray($machines));
    
    
?>