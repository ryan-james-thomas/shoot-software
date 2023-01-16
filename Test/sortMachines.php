<?php
    //Sorts machines first by owner in the order LWD R&G, Golden R&G, Tom Ferguson, and then Other
    //Then sorts by name
    //Re-writes database in that order and sends new HTML to client
    require_once("MachineObject.php");
    require_once("FunctionList.php");
    
    
    $filename = filter_input(INPUT_POST,"filename");
    $machines = readMachineList($filename);
    
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
    
    $i=0;
    $fid = fopen($filename,"w");
    foreach ($machinesLWD as $item) {
        fwrite($fid,$item->displayText());
        $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
        $i++;         
    }
    foreach ($machinesGOL as $item) {
        fwrite($fid,$item->displayText());
        $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
        $i++;         
    }
    foreach ($machinesTAF as $item) {
        fwrite($fid,$item->displayText());
        $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
        $i++;         
    }
    foreach ($machinesOther as $item) {
        fwrite($fid,$item->displayText());
        $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
        $i++;         
    }
    fclose($fid);



?>