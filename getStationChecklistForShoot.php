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
    $machines = $shoot->getMachineTargets();
    $stations = $shoot->getAllStations();
    
    
    foreach ((array)$stations as $station) {
        if ($station->getUse()===false) {
            continue;
        }
        $name = $station->getName();
        $eventNames = $station->getEventNames();
        $machineNames = $station->getEventMachines();
        
print<<<HERE
<div class="Full2">
<h4>Station $name</h4>

<div class="StationInfo">
<table>
<tr>
    <th>Event</th>
    <th>Machines</th>
</tr>
HERE;
        $j=0;
        foreach ((array)$eventNames as $event) {
            $mNames = $machineNames[$j];
            $j++;
    
print<<<HERE
<tr>
    <td>$event</td>
    <td>$mNames</td>
</tr>
HERE;
        }
        print "</table>\n";
        
        $machineNames = $station->getUniqueMachines();
        print "<table>\n<tr><th></th>\n";
        $numBoxes = array();
        $remBoxes = array();
        $type = array();
        $colour = array();
        $size = array();
        $count = 0;
        foreach ((array)$machineNames as $name) {
            $machine = findName($name,$machines,0);
            $mName = $machine->getName();
            $type[$count] = $machine->getType();
            $colour[$count] = $machine->getTargetColour();
            $size[$count]=$machine->getSize();
            print "<th>$mName</th>\n";
            $eventData = $machine->getEventData();
            $numTargets = 0;
            for ($i=0;$i<sizeof($eventData);$i++) {
                if ($eventData[$i]["sName"] == $station->getName()) {
                    $numTargets += $eventData[$i]["numTargets"];
                }
            }
            $numBoxes[$count] = round($numTargets/getTargetsPerBox($type[$count]),1);
            $remBoxes[$count] = round(($numTargets-$size[$count])/getTargetsPerBox($type[$count]),1);
            $remBoxes[$count] = ($remBoxes[$count]>=0)?$remBoxes[$count]:0;
            $count++;
        }
        print "</tr>\n";
        
        print "<tr>\n<th>Type</th>\n";
        foreach ($type as $k => $a) {
            if (dispTargetColour($a)) {
                print "<td>$a " . $colour[$k] . "</td>\n";
            } else {
                print "<td>$a</td>\n";
            }
            
        }
        print "</tr>\n";
        
        print "<tr>\n<th>Total boxes</th>\n";
        foreach ($numBoxes as $a) {
            print "<td>$a</td>\n";
        }
        print "</tr>\n";
        
        print "<tr>\n<th>Boxes with filled carousel</th>\n";
        foreach ($remBoxes as $a) {
            print "<td>$a</td>\n";
        }
        print "</tr>\n</table>\n";
        
        print "</div>\n";
    

print<<<HERE
<div class="TableDiv">
<table>
    <tr>
        <th>Item</th>
        <th>Done?</th>
    </tr>
    <tr>
        <td>Targets set?</td>
        <td></td>
    </tr>
    <tr>
        <td>Remotes placed and tested?</td>
        <td></td>
    </tr>
</table>
<table>
    <tr>
        <th>Item</th>
        <th>Done?</th>
    </tr>
    <tr>
        <td>Machines loaded?</td>
        <td></td>
    </tr>
    <tr>
        <td>Extra targets present?</td>
        <td></td>
    </tr>
</table>
<table>
    <tr>
        <th>Item</th>
        <th>Done?</th>
    </tr>
    <tr>
        <td>Station sign present?</td>
        <td></td>
    </tr>
    <tr>
        <td>Garbage bins/boxes present?</td>
        <td></td>
    </tr>
</table>
</div>
</div>

HERE;

    }




?>