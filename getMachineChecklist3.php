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
    $machineUse = Shoot::loadMachineUtilisation();
    
    $machines = $shoot->getMachineTargets();
    $minRows = 2;
    
    $count = 0;
    print "<table class=\"Full\">\n";
    foreach ($machines as $machine) {
        $name = $machine->getName();
        $type = $machine->getType();
        $size = $machine->getSize();
        $notes = $machine->getNotes();
        $eventData = $machine->getEventData();
        $totalBoxes = 0;
        if ($machine->getManual()) {
            $isManual = "Manual machine";
        } else {
            $isManual = "Automatic machine";
        }
        
        $a = getAllInArray($name,$machineUse,"name");
        //var_dump($eventData);
        //print "<br/>\n";
        $stations = array();
        $events = array();
        $numTargets = array();
        for ($i=0;$i<sizeof($a);$i++) {
            $tmp = $a[$i]["station"];
            $key = array_search($tmp,$stations);
            if ($key === false) {
                $stations[] = $tmp;
                $events[] = $a[$i]["event"];
                $tmp2 = getAllInArray($tmp,$eventData,"sName");
                $tmp3 = 0;
                foreach ($tmp2 as $tt) {
                    $tmp3 += $tt["numTargets"];
                }
                $numTargets[] = $tmp3;
            } else {
                $events[$key] .= ", " . $a[$i]["event"];
                $tmp2 = getAllInArray($tmp,$eventData,"sName");
                $tmp3 = 0;
                foreach ($tmp2 as $tt) {
                    $tmp3 += $tt["numTargets"];
                }
                $numTargets[$key] = $tmp3;
            }
        }
        //var_dump($numTargets);print "<br/>\n";
        
        $numBoxes = array();
        $remBoxes = array();
        for ($i=0;$i<sizeof($numTargets);$i++) {
            $numBoxes[$i] = round($numTargets[$i]/getTargetsPerBox($type),1);
            $remBoxes[$i] = round(($numTargets[$i]-$size)/getTargetsPerBox($type),1);
            $remBoxes[$i] = ($remBoxes[$i]>=0)?$remBoxes[$i]:0;
        }
        
        $numStations = sizeof($stations);
        $notesRows = max(array(1,$numStations-$minRows + 1));
        

print<<<HERE
<tr style="background-color: #DDDDDD">
    <th>$name</th>
    <th>Type</th>
    <th>Carousel size</th>
    <th>Station</th>
    <th>Events</th>
    <th>Number of boxes with filled carousel</th>
</tr>
<tr>
    <td>$isManual</td>
    <td>$type</td>
    <td>$size</td>
    <td>$stations[0]</td>
    <td>$events[0]</td>
    <td>$remBoxes[0]</td>
</tr>
<tr>
    <td colspan="3" rowspan="$notesRows"><strong>Notes:</strong> $notes</td>

HERE;
    if ($numStations >= $minRows) {
print <<<HERE
    <td>$stations[1]</td>
    <td>$events[1]</td>
    <td>$remBoxes[1]</td>
</tr>
HERE;
    } else if ($numStations < $minRows) {
print <<<HERE
    <td colspan="3"></td>
</tr>
HERE;
    }
    

    if ($numStations > $minRows) {
        for ($i=2;$i<sizeof($stations);$i++) {
            $ss = $stations[$i];
            $ee = $events[$i];
            $rr = $remBoxes[$i];
print <<<HERE
<tr>
    <td>$ss</td>
    <td>$ee</td>
    <td>$rr</td>
</tr>
HERE;
        }
    }
    
    }
    print "</table>\n";




?>