<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    $event = new ShootEvent();
    $event->loadData();
    $event->setStations($stations);
    $event->setTypeCount();
    
    $count = 0;
    foreach ((array)$event->getStations() as $station) {
        if ($station->getUse()===false) {
            continue;
        }
        $name = $station->getName();
        $targetStyle = $station->getTargetStyle();
        $notes = $station->getNotes();
        $stnMachines = $station->getMachines();
        $machineNames = $station->displayMachineNames();
        
print<<<HERE
<div class="Full">
<h4>Station $name</h4>

<div class="StationInfo">
Target style: $targetStyle<br/>
Machines used: $machineNames<br/>
Notes: $notes<br/>
</div>
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
        $totalBoxes = round($mTargets/getTargetsPerBox($mType),1);
        $remBoxes = round(($mTargets - $mSize)/getTargetsPerBox($mType),1);
        $remBoxes = ($remBoxes>=0)?$remBoxes:0;
        

print <<<HERE
<div class="MachineInfo">
Machine name: <strong>$mName</strong><br/>
Target type: $mType $mColour<br/>
Notes: $mNotes<br/>
Carousel size: $mSize<br/>
Total number of boxes: $totalBoxes<br/>
Boxes with filled carousel: $remBoxes<br/>
</div>

HERE;
    }
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