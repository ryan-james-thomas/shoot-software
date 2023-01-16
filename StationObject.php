<?php

class Station {
    
    protected $name;
    protected $inUse;
    protected $machines;
    protected $numShots;
    protected $numMachines;
    protected $targetStyle; //Singles or doubles
    protected $targetFormat;    //True pair, report pair, etc.
    protected $checkFP;
    protected $numTargets;
    protected $notes;
    protected $allowedStyles = array("singles" => 1, "doubles"=>2);
    protected $allowedFormats = array("S","TP","1R2","2R1","FP","1F2","2F1");
    protected $typeCount = array();
    protected $stnError;
    protected $machineErrorMsg;
    protected $eventNames = array();
    protected $eventMachines = array();
    protected $eventData = array();
    
    public function __construct($name="",$inUse=false,$machines=null,$numShots=0,$targetStyle="doubles",$notes="") {
        $this->name = $name;
        $this->inUse = $inUse;
        $this->machines = $machines;
        $this->numShots = +$numShots;
        $this->numMachines = sizeof($this->machines);
        $this->targetStyle = $targetStyle;
        $this->setNumTargets();
        $this->notes = $notes;
        //$this->genTypeCount();
        $this->checkStnError();
    }
    
    public function __clone() {
        $a = array();
        foreach ((array)$this->machines as $item) {
            $a[] = clone $item;
        }
        $this->machines = $a;
    }
    
/******Intrinsic station properties********/

    //Set and get name
    public function setName($name) {
        $this->name=preg_replace('/\s/',"",$name);
    }
    
    public function getName() {
        return $this->name;
    }
    
    //Set and get use.  Yes and No are used because true and false booleans do not save to text
    public function setUse($inUse) {
        if ($inUse === true or $inUse === false) {
            $this->inUse = $inUse;
        } else if ($inUse === "yes") {
            $this->inUse = true;
        } else if ($inUse === "no") {
            $this->inUse = false;
        } else {
            $this->inUse = false;
        }
        $this->checkStnError();
    }
    
    public function getUse() {
        return $this->inUse;
    }
    
    //Set and get numShots
    public function setNumShots($numShots) {
        $numShots = (int)$numShots;
        if ($numShots>=0) {
            $this->numShots = $numShots;
        } else {
            $this->numShots = 0;
        }
        foreach ((array)$this->machines as &$item) {
            $item->setTotalTargets($this->numShots);
        }
        
        $this->setNumTargets();
        $this->checkStnError();
    }
    
    public function getNumShots() {
        return $this->numShots;
    }
    
    //Set and get target styles
    public function setTargetStyle($targetStyle) {
        foreach ((array)$this->allowedStyles as $style => $num) {
            if ($targetStyle === $style) {
            $this->targetStyle = $style;
            $this->setNumTargets();
            $this->checkStnError();
            return true;
            }
        }
        $this->targetStyle = "error";
        $this->checkStnError();
        return false;
        
    }
    
    public function getTargetStyle() {
        return $this->targetStyle;
    }
    
    //Set and get targetFormat
    public function setTargetFormat($targetFormat) {
        $this->targetFormat = strtoupper($targetFormat);
        if ($this->checkFP) {
            $this->targetFormat = "FP";
        } else if ($this->targetFormat == "FP") {
            $this->setCheckFP(true);
        }
        $this->checkStnError();
    }
    
    public function getTargetFormat() {
        return $this->targetFormat;
    }
    
    //Sets and gets checkFP, which is true when the target is a following pair
    public function setCheckFP($checkFP) {
        if ($this->targetFormat == "FP") {
            $this->checkFP = true;
        } else if ($checkFP === true or $checkFP === false) {
            $this->checkFP = $checkFP;
        } else if ($checkFP === "yes") {
            $this->checkFP = true;
        } else if ($checkFP === "no") {
            $this->checkFP = false;
        } else {
            $this->checkFP = false;
        }
        
        if ($this->checkFP) {
            $this->targetFormat = "FP";
        }
        $this->checkStnError();
    }
    
    public function getCheckFP($opt=false) {
        if ($opt) {
            if ($this->checkFP) {
                return "yes";
            } else {
                return "no";
            }
        } else {
            return $this->checkFP;
        }
    }
    
    //Set and get notes
    public function setNotes($notes) {
    $this->notes = ltrim($notes);
    }
    
    public function getNotes(){
        return $this->notes;
    }
    
    //Add a machine to the station
    public function addMachine($machine) {
        if ($machine!==false) {
            $machine->setTotalTargets($this->numShots);
            $this->machines[] = $machine;
            $this->numMachines = sizeof($this->machines);
        }
        $this->checkStnError();
    }
    
    //Delete a machine from the station
    public function deleteMachine($idx) {
        $tmp = $this->machines;
        $this->machines = array();
        for ($i=0;$i<sizeof($tmp);$i++) {
            if ($i != $idx) {
                $this->machines[] = $tmp[$i];
            }
        }
        $this->numMachines = sizeof($this->machines);
        $this->checkStnError();
    }
    
    //Set the machines all at once
    public function setMachines($machines) {
        foreach ((array)$machines as $machine) {
            $machine->setTotalTargets($this->numShots);
        }
        $this->machines = $machines;
        $this->numMachines = sizeof($this->machines);
        $this->checkStnError();
    }
    
    //Return all machines (-1) or specified index
    public function getMachines($idx=-1) {
        if ($idx==-1) {
            return $this->machines;
        } else {
            return $this->machines[$idx];
        }
    }
    
    //Get number of machines
    public function getNumMachines() {
        return $this->numMachines;
    }
    
    //Return string with machine names, separated by ", "
    public function displayMachineNames() {
        $size = sizeof($this->machines);
        $msg = "";
        for ($i=0;$i<$size;$i++) {
            $tmp = $this->machines[$i];
            $msg .= $tmp->getName();
            if ($i != ($size-1)) {
                $msg .= ", ";
            }
        }
        return $msg;
    }
    
    public function getTargetColours() {
        $colours = array();
        foreach ((array)$this->machines as $machine) {
            $colours[] = $machine->getTargetColour();
        }
        
        return $colours;
    }
    
    public function getTrappers() {
        $trappers = array();
        foreach ((array)$this->machines as $machine) {
            $trappers[] = $machine->getTrapper();
        }
        return $trappers;
    }
    
    //Get allowed target styles
    public function getAllowedStyles() {
        return $this->allowedStyles;
    }
    
    //Get allowed formats
    public function getAllowedFormats() {
        return $this->allowedFormats;
    }
    
    //Sets the number of targets based on targetStyle and numShots
    private function setNumTargets() {
        if ($this->targetStyle === "doubles") {
            $this->numTargets = $this->numShots * 2;    
        } else if ($this->targetStyle === "singles") {
            $this->numTargets = $this->numShots * 1;
        } else {
            $this->numTargets = $this->numShots * 2;
        }   
    }
    
    //Checks whether or not the target type (singles or doubles) matches with the machine characteristics
    public function checkStnError() {
        $totalTargets = 0;
        foreach ((array)$this->machines as $item) {
            $totalTargets += $item->getNumThrown();                
        }
        
        if ($this->inUse === false) {
            $this->stnError = false;
        } else if ($this->targetStyle == "error") {
            $this->stnError = true;
        } else if (array_key_exists($this->targetStyle,$this->allowedStyles)) {
            $n = $this->allowedStyles[$this->targetStyle];
            if ($totalTargets == $n) {
                $this->stnError = $this->checkFP;            
            } else if ($totalTargets == 1 and $n == 2 and $this->checkFP === true) {
                $this->stnError = false;
            } else {
                $this->stnError = true;
            }
        }
        
        return $this->stnError;        
    }   //End checkStnError
    
    
    public function checkDuplicateNames($stations,$idx) {
        $stationsUsed = array();
        $count = 0;
        foreach ($stations as $station) {
            if ($count != $idx) {
                $stationsUsed[] = $station;
            }
            $count++;
        }
        
        $val = findName($this->name,$stationsUsed,1);
        return $val;
        
    }
    
    //Checks to make sure machine use isn't duplicated in current station or any other station
    public function checkMachineUse($stations,$idx) {       
        $msg = "";
        $names = explode(", ",$this->displayMachineNames());
        if ($this->inUse) {
            $count = array_count_values($names);
            foreach ($count as $key => $value) {
                if ($value > 1) {
                    $msg .= "Machine $key already used in this station!<br/>\n";
                }
            }
            
            foreach ($names as $name) {
                for ($i=0;$i<sizeof($stations);$i++) {
                    if ($stations[$i]->getUse() === false or ($i == $idx)) {
                        continue;
                    }
                    $machineNames = explode(", ",$stations[$i]->displayMachineNames());
                    foreach ($machineNames as $item) {
                        if ($name == $item) {
                            $msg .= "Machine $name already used in station " . $stations[$i]->getName() . "!<br/>\n";
                        }
                    }
                }
            }
        }
        $this->machineErrorMsg = $msg;
        return $msg;
    }   //end checkMachineUse
    
    public function checkMachineUseAll(&$stations) {
        $msg = "";
        for ($i=0;$i<sizeof($stations);$i++) {
            $msg .= $stations[$i]->checkMachineUse($stations,$i);
        }
        return $msg;
    }
    
    public function checkStnErrorAll($stations,$idx) {
        $this->checkStnError();
        $val = $this->checkDuplicateNames($stations,$idx);
        $msg = $this->checkMachineUse($stations,$idx);
        
        $noErr = ((!$this->stnError) and ($val === false) and (strlen($msg) == 0)); //true if no error
        
        $r = array("noErr" => $noErr,
                   "targetCheck" => !$this->stnError,
                   "duplicateName" => $val,
                   "duplicateMachine" => $msg);
        
        return $r;
        
    }
    
    
/*****Load and save station data*****/

    //Get input from filter
    public function getInput($machines,$input_type=INPUT_POST) {
        $this->setName(filter_input($input_type,"name"));
        $this->setUse(filter_input($input_type,"inUse"));
        $this->setTargetStyle(filter_input($input_type,"style"));
        $this->setTargetFormat(filter_input($input_type,"format"));
        $this->setCheckFP(filter_input($input_type,"checkFP"));
        $this->setNumShots(filter_input($input_type,"numShots"));
        $this->setNotes(filter_input($input_type,"notes"));
        $colours = json_decode(filter_input($input_type,"colours"));
        $trappers = json_decode(filter_input($input_type,"trapperOptions"));
        
        $stationMachines = json_decode(filter_input($input_type,"machines"));    //A list of indicies, not actual machines
        for ($i=0;$i<sizeof($stationMachines);$i++) {
            if ($stationMachines[$i]!=-1) {
                $machines[$stationMachines[$i]]->setTargetColour($colours[$i]);
                $machines[$stationMachines[$i]]->setTrapper($trappers[$i]);
                $this->addMachine($machines[$stationMachines[$i]]);
            }
        }
        
        return $this;
    }

    //Load and return station data into array of stations
    public function loadData($machines,$filename=STATION_FILE) {
        $fid = fopen($filename,"r");
        if ($fid===false) {
            return false;
        }
        
        $stations = array();
        $stationCount = 0;
        $colours = null;
        $trappers = null;
        
        while (!feof($fid)) {
            $line = fgets($fid);
            //print $line . "<br/>";
            if (preg_match('/name/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-4);
                //print $s . "<br/>";
                $tmp = new Station();
                $tmp->setName($s);
            } else if (preg_match('/inUse/',$line,$match)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                if (preg_match('/yes/',$s)===1) {
                    $a = true;
                } else {
                    $a = false;
                }
                $tmp->setUse($a);
            } else if (preg_match('/machines/',$line,$match)===1) {
                $s = substr($line,11);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $ss = explode(", ",$s);
                foreach ($ss as $item) {
                    if (isset($machines)) {
                        $tmp->addMachine(findName($item,$machines));
                    } else {    //Used only when names are needed
                        $r = new Machine();
                        $r->setName($item);
                        $tmp->addMachine($r);
                    }
                }
            } else if (preg_match('/colours/',$line)===1) {
                $s = substr($line,10);
                $s = substr($s,0,strlen($s)-2);
                //print strlen($s) . " " . $s . "<br/>\n";
                $colours = explode(", ",$s);
            } else if (preg_match('/trappers: /',$line)===1) {
                $s = substr($line,11);
                $s = substr($s,0,strlen($s)-2);
                //print strlen($s) . " " . $s . "<br/>\n";
                $trappers = explode(", ",$s);
            } else if (preg_match('/numShots/',$line,$match)===1) {
                $s = substr($line,10);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setNumShots($s);
            } else if (preg_match('/targetStyle/',$line,$match)===1) {
                $s = substr($line,13);
                $s = substr($s,0,strlen($s)-2);
                $s = preg_replace('/\s/',"",$s);
                //print $s . "<br/>";
                $tmp->setTargetStyle($s);
            } else if (preg_match('/targetFormat: /',$line,$match)===1) {
                $s = substr($line,14);
                $s = substr($s,0,strlen($s)-2);
                $s = preg_replace('/\s/',"",$s);
                //print $s . "<br/>";
                $tmp->setTargetFormat($s);
            } else if (preg_match('/checkFP: /',$line)===1) {
                $s = substr($line,9);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                if (preg_match('/yes/',$s)===1) {
                    $a = true;
                } else {
                    $a = false;
                }
                $tmp->setCheckFP($a);
            } else if (preg_match('/notes/',$line,$match)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setNotes($s);
            } else if (preg_match('/}/',$line,$match)===1) {
                $tmp->checkStnError();
                $stations[$stationCount]=new Station();
                $mm = $tmp->getMachines();
                for ($i=0;$i<sizeof($mm);$i++) {
                    $mm[$i]->setTargetColour($colours[$i]);
                    $mm[$i]->setTrapper($trappers[$i]);
                    //print $colours[$i] . " " . $mm[$i]->getTargetColour() . " " . strcmp($colours[$i],$mm[$i]->getTargetColour()) . "<br/>\n";
                    //print strlen($colours[$i]) . " " . strlen($mm[$i]->getTargetColour()) . "<br/>\n";
                }
                //print $mm[0]->getTargetColour() . "<br/>\n";
                $tmp->setMachines($mm);
                $stations[$stationCount] = clone $tmp;               
                $stationCount++;
            }
        }
        
        fclose($fid);
        Station::checkMachineUseAll($stations);
        return $stations;
    }   //End loadData
    
    //Save station data
    public function saveData($stations,$machines=null,$filename=STATION_FILE) {
        $a = Station::loadData($stations);   //Loads copy of stations
                
        $fid = fopen($filename,"w");
        if ($fid === false) {
            return false;
        }
        try {
            foreach($stations as $item) {
                fwrite($fid,$item->displayText());
            }
            fclose($fid);
        } catch (E_WARNING $e) {
            print $e . "\n";
            foreach($a as $item) {
                fwrite($fid,$item->displayText());
            }
            fclose($fid);
        }
    }   //End saveData
    
    //Deletes a station
    public function deleteStation($stations,$idx) {
        $tmp = $stations;
        $stations = array();
        $count = 0;
        for ($i=0;$i<sizeof($tmp);$i++) {
            if ($i != $idx) {
                $stations[$count] = $tmp[$i];
                $count++;
            }
        }
        Station::checkMachineUseAll($stations);
        return $stations;
    }
    
/*******Display of station properties********/

    //Creates associative array of station properties for json_encode
    public function toArray($idx) {
        $a = array("idx" => (int)$idx,
                   "name" => $this->name,
                   "inUse" => $this->inUse,
                   "machines" => $this->displayMachineNames(),
                   "numShots" => $this->numShots,
                   "targetStyle" => $this->targetStyle,
                   "targetFormat" => $this->targetFormat,
                   "checkFP" => $this->checkFP,
                   "notes" => $this->notes,
                   "stnError" => $this->stnError,
                   "machineErrorMsg" => $this->machineErrorMsg);
        return $a;
    }
    
    //Creates array of associative array of station properties
    public function stationArray($stationsIn) {
        $a = array();
        for ($i=0;$i<sizeof($stationsIn);$i++) {
            $a[$i] = $stationsIn[$i]->toArray($i);
        }
        return $a;
    }
    
    //Returns a string used for saving data to text files
    public function displayText() {
        $msg = "name: " . $this->name . " {\r\n";
        if ($this->inUse) {
            $msg .= "\tinUse: yes\r\n";
        } else {
            $msg .= "\tinUse: no\r\n";
        }
        $msg .= "\tmachines: " . $this->displayMachineNames() . "\r\n";
        $msg .= "\tcolours: " . implode(", ",$this->getTargetColours()) . "\r\n";
        $msg .= "\ttrappers: " . implode(", ",$this->getTrappers()) . "\r\n";
        $msg .= "\tnumShots: " . $this->numShots . "\r\n";
        $msg .= "\ttargetStyle: " . $this->targetStyle . "\r\n";
        $msg .= "\ttargetFormat: " . $this->targetFormat . "\r\n";
        $msg .= "\tcheckFP: " . $this->getCheckFP(true) . "\r\n";
        $msg .= "\tnotes: " . $this->notes . "\r\n";
        $msg .= "}\r\n";
        return $msg;
        
    }
    
    //Displays HTML code used for viewing station properties
    public function displayHTML($dispContainer,$id,$classname) {
        if ($this->getUse()) {
            $check = 'checked="checked"';
            $disp = "inline";
            $classUse = "";
        } else {
            $check = "";
            $disp = "none";
            $classUse = "notUsed";
        }
        $checkFP = $this->getCheckFP(true);
        $str = $this->displayMachineNames();
        if ($dispContainer) {
            print "<div id=\"StationContainer_$id\" class=\"$classname StationContainer $classUse\">\n";
        }
print <<<HERE
<form action="" id="Station_$id" class="StationForm">
    <fieldset>
        Name: $this->name
        <label for="check_$id" style="margin-left: 2em">In Use?</label>
        <input type="checkbox" style="margin-left: 0.25em" id="check_$id" $check onclick="SendStationUse($id);"/><br/>
        <div id="StationInfo_$id" class="StationInfo_$id" style="display: $disp">
            Number of shots: $this->numShots<br/>
            Target style: $this->targetStyle<br/>
            Target format: $this->targetFormat<br/>
            Single machine following pair? $checkFP<br/>
            Machines used: $str<br/>
            Notes: $this->notes<br/>
        </div>
        <input type="button" id="edit_$id" value="Edit" onclick="StationForm($id);"/>
        <input type="button" id="delete_$id" value="Delete" onclick="DeleteStation($id);"/>
    </fieldset>
</form>
HERE;
    $stnError = $this->checkStnError();
    if ($stnError===true) {
        $errorDisp = "inline";
    } else {
        $errorDisp = "none";
    }
    print "<h3 id=\"StationError_$id\" class=\"StationError\" style=\"display: $errorDisp\">Target style does not match machine characteristics!</h3>\n";
    for ($i=0;$i<$this->numMachines;$i++) {
        print "<div class=\"MachineInfo StationInfo_$id\" id=\"MachineDisp_stn$id-mac$i\" style=\"display: $disp\">\n";
        $this->machines[$i]->displayHTML(false,"","",false);
        print "</div>\n";
    }
    print "<h3 id=\"MachineErrors_$id\" class=\"StationError\" style=\"display: none\"></h3>\n";
    if ($dispContainer) {
        print "</div>\n";   //End div for StationContainer
    }
    
    }   //End displayHTML
    
    
    
/*******Properties associated with Events********/
       
    //Based on numShooters, calculate number of targets of each type shot at each station
    //public function setTargetInfo($numShooters) {
    //    //$this->genTypeCount();
    //    $this->typeCount = getTargetsPerBox(0);
    //    if ($this->inUse) {
    //        foreach ((array)$this->machines as &$item) {
    //            $type = $item->getType();
    //            $total = $item->setTotalTargets($this->numShots * $numShooters);
    //            //print "$type $total<br/>\n";
    //            foreach ($this->typeCount as $key => &$value) {
    //                //print $key . " " . $type . "<br/>\n";
    //                if ($key == $type) {
    //                    $value += $total;
    //                    break;
    //                }
    //            }
    //        }
    //    }
    //    return $this->typeCount;
    //}
    
    //Calculates the number of targets of each type and colour for this station
    public function setTargetInfo($numShooters) {
        //$this->genTypeCount();
        $this->typeCount = getTargetsPerBox(0);
        if ($this->inUse) {
            foreach ((array)$this->machines as &$item) {
                $type = $item->getType();
                $colour = $item->getTargetColour();
                if ($this->checkFP) {   //If its a following pair, then even though there is one machine there are two shots
                    $total = $item->setTotalTargets($this->numShots * $numShooters * 2);
                } else {
                    $total = $item->setTotalTargets($this->numShots * $numShooters);
                }               
                $this->typeCount[$type][$colour] += $total;
            }
        }
        return $this->typeCount;
    }
    
    
    //Add and get associated event names
    public function addEventName($eventName) {
        $this->eventNames[] = $eventName;
    }
    
    public function getEventNames() {
        return $this->eventNames;
    }
    
    //Get number of associated events
    public function getNumEvents() {
        return sizeof($this->eventNames);
    }
    
    //Add a string containing machine names used for this station in a particular event.  Machine names separated by ", "
    public function addEventMachines($machines) {
        $this->eventMachines[] = $machines;
    }
    
    //Get array of strings of machine names separated by ", " for event $i
    public function getEventMachines($i=-1) {
        if ($i==-1) {
            return $this->eventMachines;
        } else if ($i>=0) {
            return $this->eventMachines[$i];
        }
    }
    
    //public function addEventData($eventName,$machines) {
    //    $this->eventData[$eventName] = array("name" => $)
    //    
    //}
    
    //Returns unique machine names
    public function getUniqueMachines() {
        $keep = array();
        $machines = implode(", ",$this->eventMachines);
        $machines = explode(", ",$machines);
        
        $unique = array_keys(array_flip($machines));
        return $unique;
    }
    


}   //End Station Class

?>