<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $shoot = new Shoot();
    $shoot->loadData();
    $events=$shoot->loadEvents($dir);
    $machineUse = Shoot::loadMachineUtilisation();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    $event = new ShootEvent();
    $event->loadData();
    $event->setStations($stations);
    $event->setTypeCount();
    
    //$avoid = "class=\"Avoid\"";
    $headerClass = "class=\"headerClass\"";
    
    $s1 = "style=\"width: 3em\"";
    $s2 = "style=\"width: 2em\"";
    $s3 = "style=\"width: 3em\"";
    $s4 = "style=\"width: 3em\"";
    $s5 = "style=\"width: 2em\"";
    $s6 = "style=\"width: 3em\"";
    print "<table class=\"Full\">\n";
    foreach ((array)$event->getStations() as $station) {
        //print "<div class=\"Full\">\n";
        $count = 0;
        if ($station->getUse()===false) {
            continue;
        }
        $name = $station->getName();
        $targetStyle = $station->getTargetStyle();
        $notes = $station->getNotes();
        $stnMachines = $station->getMachines();
        $machineNames = $station->displayMachineNames();
        $numRows = sizeof($stnMachines);
print<<<HERE
<!--<table class="Full">-->
<tr $headerClass>
    <th $s1>Station $name</th>
    <th $s2>Machine</th>
    <th $s3>Notes</th>
    <th $s3>Trapper</th>
    <th $s4>Target type</th>
    <th $s5>Boxes with filled carousel</th>
    <th $s6>Moved from?</th>
</tr>
HERE;
        foreach($stnMachines as $machine) {
            $mName = $machine->getName();
            $mSize = $machine->getSize();
            $mType = $machine->getType();
            if ($mType == "regular") {
                $mColour = $machine->getTargetColour();
            } else {
                $mColour = "";
            }
            $mNotes = $machine->getNotes();
            $mTargets = ceil($machine->getTotalTargets());
            $mTrapper = $machine->getTrapper();
            $totalBoxes = round($mTargets/getTargetsPerBox($mType),1);
            $remBoxes = round(($mTargets - $mSize)/getTargetsPerBox($mType),1);
            $remBoxes = ($remBoxes>=0)?$remBoxes:0;
            $b = ($count == 0)?"<td $s1 rowspan=\"$numRows\">$notes</td>":"";
            $m = $event->getMachinePreviousEvent($machine,$machineUse,$events);
            if ($m === 0 or $m === false) {
                $moveInfo = "Currently there";
                $moveColour = "class=\"NotMoved\"";
            } else if ($m[0]["station"] == $name) {
                $moveInfo = "Currently there";
                $moveColour = "class=\"NotMoved\"";
            } else {
                $moveInfo = "Move from station " . $m[0]["station"];
                $moveColour = "class=\"Moved\"";
            }
            
            $count++;
print <<<HERE
<tr>
    $b
    <td $s2 $moveColour>$mName</td>
    <td $s3 $moveColour>$mNotes</td>
    <td $s3 $moveColour>$mTrapper</td>
    <td $s4 $moveColour>$mType $mColour</td>
    <td $s5 $moveColour>$remBoxes</td>
    <td $s6 $moveColour>$moveInfo</td>
</tr>
HERE;
        }
        //print "</table>\n";
        //print "</div>\n";
//print <<<HERE
//<tr>
//    <td colspan="5" style="background-color: grey">
//        <table style="margin: 0em;background-color: white">
//            <tr>
//                <th>Item</th>
//                <th>Done?</th>
//                <th>Item</th>
//                <th>Done?</th>
//                <th>Item</th>
//                <th>Done?</th>
//            </tr>
//            <tr>
//                <td>Targets set?</td>
//                <td></td>
//                <td>Remotes placed and tested?</td>
//                <td></td>
//                <td>Machines loaded?</td>
//                <td></td>
//            </tr>
//            <tr>
//                <td>Extra targets present?</td>
//                <td></td>
//                <td>Station sign present?</td>
//                <td></td>
//                <td>Garbage bins/boxes present?</td>
//                <td></td>
//            </tr>
//        </table>
//    </td>
//</tr>
//HERE;

    }
    print "</table>\n";
    //print "</div>"




?>