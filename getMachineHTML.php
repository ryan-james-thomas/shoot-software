<?php
    //Sends machine HTML display to client
    require_once("MachineObject.php");
    require_once("FunctionList.php");
    require_once("ShootingObjects.php");

    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $machineUse = Shoot::loadMachineUtilisation();
    //$machineUse = null;
    
    $idx = filter_input(INPUT_POST,"machineIdx");
    $stnIdx = filter_input(INPUT_POST,"stnIdx");
    $columnIdx = filter_input(INPUT_POST,"columnIdx");
    if ($idx==-1) {
        print "";
    } else {
        $machines[$idx]->displayHTML(false,"","",array($stnIdx,$columnIdx),$machineUse);
    }

?>