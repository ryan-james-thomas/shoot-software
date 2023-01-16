<?php
    //Sorts machines first by owner in the order LWD R&G, Golden R&G, Tom Ferguson, and then Other
    //Then sorts by name
    //Re-writes database in that order and sends new HTML to client
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");

    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    
    $cLWD = 0;
    $cGOL = 0;
    $cTAF = 0;
    $cOther = 0;
    $machinesLWD = array();
    $machinesGOL = array();
    $machinesTAF = array();
    $machinesOther = array();
    $allowedOwners = $machines[0]->getAllowedOwners();
    for ($i=0;$i<sizeof($machines);$i++) {
        if ($machines[$i]->getOwner() == $allowedOwners["LWD"]) {
            $machinesLWD[$cLWD] = new Machine();
            $machinesLWD[$cLWD] = $machines[$i];
            $cLWD++;
        } else if ($machines[$i]->getOwner() == $allowedOwners["GOL"]) {
            $machinesGOL[$cGOL] = new Machine();
            $machinesGOL[$cGOL] = $machines[$i];
            $cGOL++;
        } else if ($machines[$i]->getOwner() == $allowedOwners["TAF"]) {
            $machinesTAF[$cTAF] = new Machine();
            $machinesTAF[$cTAF] = $machines[$i];
            $cTAF++;
        } else {
            $machinesOther[$cOther] = new Machine();
            $machinesOther[$cOther] = $machines[$i];
            $cCount++;
        }
    }
    
    usort($machinesLWD,"cmp");
    usort($machinesGOL,"cmp");
    usort($machinesTAF,"cmp");
    usort($machinesOther,"cmp");
    
    $machines = array_merge($machinesLWD,$machinesGOL,$machinesTAF,$machinesOther);
    print json_encode(Machine::machineArray($machines));
    print DIVIDER;
    
    Machine::saveData($machines);
    $i=0;
    foreach ($machines as $item) {
        $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
        $i++;
    }
    


?>