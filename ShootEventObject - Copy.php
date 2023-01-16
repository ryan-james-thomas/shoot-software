<?php

class ShootEvent {
        
    protected $name;
    protected $date;
    protected $numShooters;
    protected $overcapacity;
    protected $stations;
    protected $notes;
    protected $numStations;
    protected $typeCount;
    protected $idx;
    protected $numTargetsPerShooter;
    
    public function __constructor($name="",$date="",$numShooters=0,$overcapacity=0,$notes="",$stations=null) {
        $this->name = $name;
        $this->date = $date;
        $this->numShooters = $this->setNumShooters($numShooters);
        $this->overcapacity = $this->setOvercapacity($overcapacity);
        $this->stations = $stations;
        $this->$numStations = sizeof($stations);
        $this->notes = $notes;
        $this->setTypeCount();
    }
    
/**********Intrinsic Event Properties*************/

    //Set and get name
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    //Set and get date
    public function setDate($date) {
        $this->date = $date;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    //Set and get number of shoots
    public function setNumShooters($numShooters) {
        $this->numShooters = (int)$numShooters;
        $this->setNumTargetsPerShooter();
        $this->setTypeCount();
    }
    
    public function getNumShooters() {
        return $this->numShooters;
    }
    
    //Set overcapacity as a percentage over, like 25%.  Converts to decimal form 25% => 1.25
    public function setOvercapacity($overcapacity) {
        $overcapacity = (int)$overcapacity;
        if ($overcapacity>=0) {
            $this->overcapacity = $overcapacity/100 + 1;
        } else {
            $this->overcapacity = 1;
        }
    }
    
    //Returns overcapacity.  Default returns percent 1.25 => 25%.  $opt!=0 gives 1.25
    public function getOvercapacity($opt=0) {
        if ($opt==0) {
            return round(($this->overcapacity-1)*100);
        } else {
            return $this->overcapacity;
        }
        
    }
    
    //Set and get notes
    public function setNotes($notes) {
    $this->notes = ltrim($notes);
    }
    
    public function getNotes(){
        return $this->notes;
    }
    
    //Sets stations in event, counting only those that are used
    public function setStations($stations) {
        $this->stations = $stations;
        $count = 0;
        foreach ($stations as $station) {
            if ($station->getUse()) {
                $count++;
            }
        }
        $this->numStations = $count;
        $this->setNumTargetsPerShooter();
        $this->setTypeCount();
    }
    
    //Gets all stations in event, including not-used ones
    public function getStations() {
        return $this->stations;
    }
    
    //Gets number of used stations
    public function getNumStations() {
        return $this->numStations;
    }
    
    //Sets and returns the number of targets shot per shooter
    public function setNumTargetsPerShooter() {
        $a = getTargetsPerBox(0);
        foreach ((array)$this->stations as $station) {
            $b = $station->setTargetInfo(1);
            foreach ($b as $key => $value) {
                $a[$key] += $value;
            }
        }
        $this->setTypeCount();
        $this->numTargetsPerShooter = array_sum($a);
        return $this->numTargetsPerShooter;
    }
    
    //Sets and returns the number of targets used during an event of each type
    public function setTypeCount() {
        $this->typeCount = getTargetsPerBox(0);            
        foreach ((array)$this->stations as &$item) {
            $a = $item->setTargetInfo($this->numShooters * $this->overcapacity);
            foreach ($a as $key => $value) {
                $this->typeCount[$key] += $value;
            }
        }            
        return $this->typeCount;           
    }
    
    //Returns the number of boxes of each type of target needed
    public function getNumBoxes() {
        $a = $this->setTypeCount();
        foreach ((array)$this->setTypeCount() as $key => $value) {
            $a[$key] = round($value/getTargetsPerBox($key),1);
        }
        return $a;
    }

/*********Load, save, and input functions************/

    //Populates ShootEvent from filter
    public function getInput($input_type=INPUT_POST) {        
        $this->setName(filter_input($input_type,"name"));
        $this->setDate(filter_input($input_type,"date"));
        $this->setNumShooters(filter_input($input_type,"numShooters"));
        $this->setOvercapacity(filter_input($input_type,"overcapacity"));
        $this->setNotes(filter_input($input_type,"notes"));
        
        return $this;
    }

    //Loads event data from file
    public function loadData($filename=EVENT_FILE) {
        
        $fid = fopen($filename,"r");
        if ($fid===false) {
            return false;
        }
                
        while (!feof($fid)) {
            $line = fgets($fid);
            if (preg_match('/name/',$line)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                $this->setName($s);
            } else if (preg_match('/date/',$line)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                $this->setDate($s);
            } else if (preg_match('/numShooters/',$line)===1) {
                $s = substr($line,13);
                $s = substr($s,0,strlen($s)-2);
                $this->setNumShooters($s);
            } else if (preg_match('/overcapacity/',$line)===1) {
                $s = substr($line,14);
                $s = substr($s,0,strlen($s)-2);
                $this->setOvercapacity($s);
            } else if (preg_match('/notes/',$line)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                $this->setNotes($s);
            }
        }
        return $this;
        
    } //End loadData
    
    //Saves event data
    public function saveData($dir,$filename=EVENT_FILE) {
        
        $a = new ShootEvent();
        $a->loadData();
        
        //Save file
        $fid = fopen(EVENT_FILE,"w");
        try {
            if ($fid!==false) {
                fwrite($fid,$event->displayText());
                fclose($fid);
                print "Data saved!";
            } else {
                print "Error in opening file.";
            }
        } catch (E_WARNING $e) {
            fwrite($fid,$event->displayText());
            fclose($fid);
            print $e . "\n";
        }
        
        //Change directory
        try {
            $dir->toShoot();
            rename($dir->getEventDir(),$dir->getShootDir() . "\\" . $this->name);
        } catch (E_WARNING $e) {
            print "Event name already exists!";
        }        
    }   //End saveData
    
    
/**********Error Checking****************/

    //Checks to see if there are any stations with errors
    public function checkTargetError() {
        foreach ($this->stations as $item) {
            $item->setTargetInfo($this->numShooters * $this->overcapacity);
            if ($item->checkStnError()) {
                return true;
            }
        }
        return false;
    }
    
    //Checks to see if there are any duplicated machines
    public function checkMachineError() {
        $count = 0;
        $names = array();
        foreach ($this->stations as $station) {
            if ($station->getUse() === false) {
                continue;
            }
            foreach ($station->getMachines() as $machine) {
                $names[$count] = $machine->getName();
                $count++;
            }
        }
        $msg = "";
        for ($i=0;$i<sizeof($names);$i++) {
            for ($j=$i+1;$j<sizeof($names);$j++) {
                if ($names[$i] == $names[$j]) {
                    $msg .= "Machine " . $names[$i] . " is duplicated<br/>\n";
                }
            }
        }
        return $msg;
    }
    
    
/*********Display functions**************/

    //Converts properties to associative array
    public function toArray() {
    $a = array("name" => $this->name,
               "date" => $this->date,
               "numShooters" => $this->numShooters,
               "overcapacity" => $this->getOvercapacity(),
               "notes" => $this->notes,
               "numTargets" => $this->numTargetsPerShooter);
        $this->setTypeCount();
        $b = (array)$a + (array)$this->typeCount;   //This merges the two arrays
        $b["numStations"] = $this->getNumStations();
        foreach ((array)$this->typeCount as $key => $value) {
            $newKey = $key . "Box";
            $b[$newKey] = round($value/getTargetsPerBox($key),1);
        }
        return $b;
    }
    
    //Converts array of events into an array of associative arrays
    function eventArray($eventsIn) {
        $a = array();
        for ($i=0;$i<sizeof($eventsIn);$i++) {
            $a[$i] = $eventsIn[$i]->toArray($i);
        }
        return $a;
    }
       
    //Displays text for saving data
    public function displayText() {
        $msg = "name: $this->name\r\n";
        $msg .= "date: $this->date\r\n";
        $msg .= "numShooters: $this->numShooters\r\n";
        $msg .= "overcapacity: " . $this->getOvercapacity() . "%\r\n";
        $msg .= "notes: $this->notes\r\n";
        return $msg;
    }
    
    //Displays a table of needed targets
    public function dispTargetTable() {
        displayTargetTable($this->setTypeCount(),$this->getNumBoxes());
    }
    
    //Displays a summary of the stations in the event
    public function dispStationSummary() {
        $i=0;
        $globalStnError = $this->checkTargetError();
        $msg = $this->checkMachineError();
        if ($globalStnError) {
            print "<h3 class=\"StationError\">There were target style matching errors</h3>\n";
        }
        if ($msg!=="") {
            print "<h3 class=\"StationError\">$msg</h3>\n";
        }
        print "<h3 class=\"StationError\">$msg</h3>\n";
        
        foreach ($this->stations as &$item) {
            if ($item->getUse() == false) {
                continue;
            }
            $item->setTargetInfo($this->numShooters * $this->overcapacity);
            $name = $item->getName();
            $numShots = $item->getNumShots();
            $targetStyle = $item->getTargetStyle();
            $numShots = $item->getNumShots();
            $notes = $item->getNotes();
            $machineNames = $item->displayMachineNames();
            $classname = "clr" . (($i%2)+1);
            $machines = $item->getMachines();
            if ($item->checkStnError()) {
                $stnErrorDisp = "inline";
            } else {
                $stnErrorDisp = "none";
            }
            
print <<<HERE
<div class="$classname Summary">
<h3 class="StationError" style="display: $stnErrorDisp">Target style does not match machine characteristics!</h3>
<div class="SummaryParagraph">
Station: $name<br/>
Number of shots: $numShots<br/>
Target style: $targetStyle<br/>
Machines used: $machineNames<br/>
Notes: $notes<br/>
</div>
HERE;

        foreach($machines as $machine) {
            $mName = $machine->getName();
            $mSize = $machine->getSize();
            $mType = $machine->getType();
            $mTargets = ceil($machine->getTotalTargets());
            $totalBoxes = round($mTargets/getTargetsPerBox($mType),1);
            $remBoxes = round(($mTargets - $mSize)/getTargetsPerBox($mType),1);
            $remBoxes = ($remBoxes>=0)?$remBoxes:0;
print <<<HERE
<div class="SummaryParagraph">
Machine name: $mName<br/>
Target type: $mType<br/>
Carousel size: $mSize<br/>
Total targets needed: $mTargets<br/>
Total number of boxes: $totalBoxes<br/>
Boxes with filled carousel: $remBoxes<br/>
</div>
HERE;
        }
        print "</div>\n";
        $i++;
    }
    }   //End dispStationSummary
        
          
        
}   //End ShootEvent class


?>