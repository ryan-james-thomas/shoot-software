<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $shoot = new Shoot();
    $shoot->loadData();
    $shoot->loadEvents($dir);
    $shoot->setTypeCount();
    $machineUse = Shoot::loadMachineUtilisation();
    $machines = $shoot->getMachineTargets();
    $stations = $shoot->getAllStations();
    
    print "<table class=\"Full\">\n";
    foreach ((array)$stations as $station) {
        if ($station->getUse()===false) {
            continue;
        }
        $name = $station->getName();
        $eventNames = $station->getEventNames();
        $machineNames = $station->getEventMachines();
        
print<<<HERE
<tr style="background-color: #FFFFFF;border-top: 3px black solid">
    <th>Station $name</th>
    <th>Machine</th>
    <th>Events</th>
    <th>Notes</th>
    <th>Target type</th>
    <th>Total boxes</th>
    <th>Boxes with filled carousel</th>
    <th>Move notes</th>
</tr>
HERE;
       
        $machineNames = $station->getUniqueMachines();
        foreach ($machineNames as $mName) {
            $machine = findName($mName,$machines,0);
            //$mName = $machine->getName();
            $mSize = $machine->getSize();
            $mType = $machine->getType();
            if ($mType == "regular") {
                $mColour = $machine->getTargetColour();
            } else {
                $mColour = "";
            }
            $mNotes = $machine->getNotes();
            //$mTargets = ceil($machine->getTotalTargets());
            $eventData = $machine->getEventData();
            $numTargets = 0;
            $moveNotes = array();
            for ($i=0;$i<sizeof($eventData);$i++) {
                if ($eventData[$i]["sName"] == $station->getName()) {
                    $numTargets += $eventData[$i]["numTargets"];
                } else {
                    $moveNotes[] = $eventData[$i]["sName"];
                }
            }
            if (sizeof($moveNotes) == 0) {
                $moveNotes = "";
                $moveColour = "class=\"NotMoved\"";
            } else {
                $moveNotes = implode(", ",array_unique($moveNotes));
                $moveNotes = "Also used on " . $moveNotes;
                $moveColour = "class=\"Moved\"";
            }
            $totalBoxes = round($numTargets/getTargetsPerBox($mType),1);
            $remBoxes = round(($numTargets - $mSize)/getTargetsPerBox($mType),1);
            $remBoxes = ($remBoxes>=0)?$remBoxes:0;
            $a = getAllInArray($mName,$machineUse,"name");
            $a = getAllInArray($name,$a,"station");
            $b = array();
            for($i=0;$i<sizeof($a);$i++) {
                $b[] = $a[$i]["event"];
            }
            $str = implode(", ",$b);
print <<<HERE
<tr $moveColour>
    <td></td>
    <td>$mName</td>
    <td>$str</td>
    <td>$mNotes</td>
    <td>$mType $mColour</td>
    <td>$totalBoxes</td>
    <td>$remBoxes</td>
    <td>$moveNotes</td>
</tr>

HERE;

        }
print<<<HERE
<tr>
<td colspan="8" style="padding: 0em">
<table style="margin: 0em;border: none;background-color: #DDDDDD">
    <tr>
        <th>Item</th>
        <th>Done?</th>
        <th>Item</th>
        <th>Done?</th>
        <th>Item</th>
        <th>Done?</th>
        <th>Item</th>
        <th>Done?</th>
        <th>Item</th>
        <th>Done?</th>
        <th>Item</th>
        <th>Done?</th>
    </tr>
    <tr>
        <td>Targets set?</td>
        <td></td>
        <td>Remotes placed and tested?</td>
        <td></td>
        <td>Machines loaded?</td>
        <td></td>
<!--    </tr>
    <tr>-->
        <td>Extra targets present?</td>
        <td></td>
        <td>Station sign present?</td>
        <td></td>
        <td>Garbage bins/boxes present?</td>
        <td></td>
    </tr>
</table>

HERE;

    }




?>