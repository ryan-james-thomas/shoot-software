<?php
    //Adds a new machine to the database and saves it to the text file
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    $opt = filter_input(INPUT_POST,"opt");
    if ($opt == 0) {
print<<<HERE
This will create blank, not-used stations from the low number to the high number where a station does not already exist.<br/>
<label>Low station number</label>
<input type="text" id="txtLow" style="width: 3em"/>
<br/>
<label>High station number</label>
<input type="text" id="txtHigh" style="width: 3em"/>
</br>
<input type="button" value="Create Stations" onclick="GenStations(1);"/>
<input type="button" value="Cancel" onclick="CancelGenStations();"/>
HERE;
        
    } else {
        $low = (int)filter_input(INPUT_POST,"low");
        $high = (int)filter_input(INPUT_POST,"high");
        //print "$low $high</br>\n";
        
        $stationsBlank = array();
        for ($i=$low;$i<=$high;$i++) {
            $stationsBlank[] = new Station();
            $stationsBlank[$i-$low]->setName("$i");
            $stationsBlank[$i-$low]->setUse(false);
        }
        //print sizeof($stationsBlank) . "<br/>\n";
        
        $stations = array_merge($stations,$stationsBlank);
        //print sizeof($stations) . "<br/>\n";
        $stations = getUniqueNames($stations);
        //print sizeof($stations) . "<br/>\n";
        usort($stations,"stncmp");
        //print sizeof($stations) . "<br/>\n";
        
        Station::saveData($stations,$machines);
    }

?>