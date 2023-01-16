<?php

require_once("MachineObject.php");
require_once("StationObject.php");
require_once("ShootEventObject.php");

class Shoot {
    protected $name;
    protected $startDate;
    protected $endDate;
    protected $notes;
    protected $events;
    protected $typeCount;
    
    public function __constructor() {
        $this->name = "";
        $this->startDate = "";
        $this->endDate = "";
        $this->notes = "";
        $this->events = null;
    }
    
    public function __clone() {
        $a = array();
        foreach ((array)$this->events as $item) {
            $a[] = clone $item;
        }
        $this->events = $a;
    }
    
/******Intrinsic shoot properties********/

    //Set and get name
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    //Set and get dates
    public function setDates($start,$end) {
        $this->endDate = $end;
        $this->startDate = $start;
    }
    
    public function getDates() {
        $a = array("start" => $this->startDate,"end"=>$this->endDate);
        return $a;
    }
    
    //Set and get notes
    public function setNotes($notes) {
        $this->notes = $notes;
    }
    
    public function getNotes() {
        return $this->notes;
    }
    
    //Set and get events
    public function setEvents($events) {
        $this->events = $events;
    }
    
    public function getEvents() {
        return $this->events;
    }
    
    //Get just the event names
    public function getEventNames() {
        $a = array();
        $count = 0;
        foreach ((array)$this->events as $event) {
            $a[$count] = $event->getName();
            $count++;
        }
        return $a;
    }
    
    //Calculates and returns the number of targets used for each type
    public function setTypeCount() {
        $this->typeCount = getTargetsPerBox(0);
        foreach ((array)$this->events as $event) {
            $a = $event->setTypeCount();
            $this->typeCount = addArrays($this->typeCount,$a);
        }            
        return $this->typeCount;
    }
    
    //Gets the number of boxes per type
    public function getNumBoxes() {
        $a = $this->setTypeCount();
        foreach ((array)$a as $k1 => $subArray) {
            foreach ($subArray as $k2 => $value) {
                $a[$k1][$k2] = round($value/getTargetsPerBox($k1,$k2),1);    
            }
        }
        return $a;
    }


/*****Load, save and get input data*******/
    
    //Load data from file
    public function loadData($filename=SHOOT_FILE) {
        
        $fid = fopen($filename,"r");
        if ($fid===false){
            return false;
        }
        $startDate = "";
        $endDate = "";
        
        while (!feof($fid)) {
            $line = fgets($fid);
            if (preg_match('/name/',$line)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                $this->setName($s);
            } else if (preg_match('/startDate/',$line)===1) {
                $s = substr($line,11);
                $s = substr($s,0,strlen($s)-2);
                $startDate = $s;
            } else if (preg_match('/endDate/',$line)===1) {
                $s = substr($line,9);
                $s = substr($s,0,strlen($s)-2);
                $endDate = $s;
                $this->setDates($startDate,$endDate);
            } else if (preg_match('/notes/',$line)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                $this->setNotes($s);
            }
        }
        return $this;
    }   //End loadData
    
    //Save data to file
    public function saveData($filename=SHOOT_FILE) {
        
        $a = new Shoot();
        $a = $a->loadData();
        
        $fid = fopen($filename,"w");
        if ($fid===false){
            return false;
        }
        try {
            fwrite($fid,$this->displayText());
        } catch (Exception $e) {
            print $e;
            fwrite($fid,$a->displayText());
            return false;
        }
        fclose($fid);
        return true;
        
    }
    
    //Load events from directory structure
    public function loadEvents($dir) {
               
        //Get machines
        $dir->toShoot();    
        $machines = Machine::loadData();
        
        //Find event directories
        $dir->toUser();            
        $dp = opendir($dir->getShootDir());           
        $currentFile = "";
        $events = array();
        $count = 0;
        $event = new ShootEvent();
        while ($currentFile !== false) {
            $currentFile = readdir($dp);
            if ($currentFile == "." or $currentFile == "..") {
                continue;
            }
            if (strlen($currentFile)!=0 and is_dir($dir->getShootDir() . "\\" . $currentFile)) {
                $shootArray[$count] = $currentFile;
                chdir($dir->getShootDir() . "\\" . $currentFile);
                $stations = Station::loadData($machines);
                $event->loadData();
                $event->setStations($stations);
                $events[$count] = clone $event;
                $dir->toShoot();
                $count++;
            }
        }
        closedir($dp);
        
        usort($events,"sortDate");
        $this->setEvents($events);
        return $this->events;
                
    } //End loadEvents
    

/*******Display functions**********/    
    
    //Converts properties to associative array for sending to HTML via json_encode
    public function toArray() {
        $a = array("name" => $this->name,
                   "startDate" => $this->startDate,
                   "endDate" => $this->endDate,
                   "notes" => $this->notes,
                   "events" => $this->getEventNames());
        return $a;
    }
    
    //Returns $msg with text used for saving to text files
    public function displayText() {
        $a = $this->getDates();
        $msg = "name: $this->name\r\n";
        $msg .= "startDate: " . $a["start"] . "\r\n";
        $msg .= "endDate: " . $a["end"] . "\r\n";
        $msg .= "notes: " . $this->notes . "\r\n";
        return $msg;
    }
    
    //Displays HTML event summaries
    public function dispEventHTML() {
        $count = 0;
        foreach ((array)$this->events as $event) {
            $classname = "clr" . ($count%2+1);
            $name = $event->getName();
            $date = $event->getDate();
            $errorDisp = $event->checkTargetError();
            $numStations = $event->getNumStations();
            $numShooters = $event->getNumShooters();
            $numTargets = $event->setNumTargetsPerShooter();
            $numTrappersWanted = $event->getTrappers("Wanted");
            $numTrappersNeeded = $event->getTrappers("Needed");
print<<<HERE
<div id="event_$count" class="$classname EventContainer">
<div class="text">
<strong>Name: </strong>$name<br/>
<strong>Date: </strong>$date<br/>
<strong># Optional trappers: </strong>$numTrappersWanted<br/>
<strong># Necessary trappers: </strong>$numTrappersNeeded<br/>
</div>
<div class="text">
<strong>Number of stations: </strong>$numStations<br/>
<strong>Number of shooters: </strong>$numShooters<br/>
<strong>Targets/shooter: </strong>$numTargets<br/>
</div>
HERE;
    if ($errorDisp) {
        print '<label style="background-color: #22CC22"><strong>ERROR</strong></label>';
    }
print<<<HERE
<input type="button" class="button" value="Delete" onclick="deleteEvent($count);"/>
<input type="button" class="button" value="Copy" onclick="newEvent($count);"/>
<input type="button" class="button" value="Edit" onclick="editEvent($count);"/>
</div>

HERE;
$count++;

        }
    }   //End dispEventHTML
    
    
/*********Shoot-level machine and station properties***********/

    //Combines all event uses of the machines
    public function getMachineTargets() {
        $machines = array();
        $count = 0;
        foreach ((array)$this->events as $event) {  //For every event               
            foreach ((array)$event->getStations() as $station) {    //For every station in that event
                if ($station->getUse() === false) {
                    continue;
                }
                $stnMachines = $station->getMachines();
                foreach ($stnMachines as $machine) {    //For every machine used in that station for that event
                    $item = findName($machine->getName(),$machines,1);
                    if ($item !== false) {  //If $machine is already in $machines, add properties to existing entry
                        $machines[$item]->addEventData($event->getName(),$station->getName(),$station->getNumShots() * $event->getOvercapacity(1) * $event->getNumShooters());
                    } else {    //Otherwise create entirely new entry
                        $machines[$count] = clone $machine;
                        $machines[$count]->addEventData($event->getName(),$station->getName(),$station->getNumShots() * $event->getOvercapacity(1) * $event->getNumShooters());
                        $count++;
                    }
                }
            }
        }
        //usort($machines,"cmp");
        return $machines;
    }
    
    //Combines all event uses of stations
    public function getAllStations() {
        $stations = array();
        $count = 0;
        foreach ((array)$this->events as $event) {  //For every event
            foreach ((array)$event->getStations() as $station) {    //For every station in that event
                if ($station->getUse() === false) {
                    continue;
                }
                $item = findName($station->getName(),$stations,1);
                if ($item !== false) {  //If $station is already in $stations, add properties
                    $stations[$item]->addEventName($event->getName());
                    $stations[$item]->addEventMachines($station->displayMachineNames());
                } else {    //Otherwise create entirely new entry
                    $stations[$count] = clone $station;
                    $stations[$count]->addEventName($event->getName());
                    $stations[$count]->addEventMachines($station->displayMachineNames());
                    $count++;
                }
            }
        }
        usort($stations,"stncmp");
        return $stations;
    }   //End getAllStations
    
    //Updates station lists in every event to reflect new machine database
    public function updateStations($dir,$machines=null) {
        if ($machines===null) {
            $dir->toShoot();
            $machines = Machine::loadData();
        }
        foreach ((array)$dir->getEvents() as $newDir) {
            chdir($newDir);
            $stations = Station::loadData($machines);
            Station::saveData($stations,$machines);
        }
        $this->saveMachineUtilisation();
    }
    
    //Gets all machine names, along with the events and stations to which they belong
    public function getAllMachines($name=null) {
        $machines = array();
        $count = 0;
        for ($i=0;$i<sizeof($this->events);$i++) {
            $event = $this->events[$i];
            $stations = $event->getStations();
            foreach ($stations as $station) {
                if ($station->getUse()) {
                    $stnMachines = $station->getMachines();
                    foreach ($stnMachines as $item) {
                        if ($name === null or ($name == $item->getName())) {
                            $machines[$count] = array("name"=>$item->getName(),
                                                      "station"=>$station->getName(),
                                                      "event"=>$event->getName());
                            $count++;
                        }
                    }
                }
            }
        }
        return $machines;
        
    }   //End getAllMachines
    
    
    public function saveMachineUtilisation($filename=MACHINE_USE) {
        $fid = fopen($filename,"w");
        fwrite($fid,json_encode($this->getAllMachines()));
        fclose($fid);
    }
    
    public function loadMachineUtilisation($filename=MACHINE_USE) {
        if (!file_exists($filename)) {
            return false;
        }
        $fid = fopen($filename,"r");
        $machines = json_decode(fgets($fid),true);
        fclose($fid);
        return $machines;
    }
    
    
}   //End class Shoot


?>