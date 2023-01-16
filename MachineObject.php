<?php

class Machine {
    
    public static $trapperType = array("None","Wanted","Needed");
    
    protected $name;
    protected $size;
    protected $type;
    protected $numThrown;
    protected $owner;
    protected $notes;
    protected $isManual;
    protected $totalTargets;
    protected $allowedTypes= array("regular","rabbit","mini","midi","battue");
    protected $allowedOwners = array("LWD"=>"LWD R&G","GOL"=>"Golden R&G","TAF"=>"Tom Ferguson");
    protected $stations = array();
    protected $eventNames = array();
    protected $targetColour;
    protected $eventData = array();
    protected $needTrapper;
    
    public function __construct($name="",$size=0,$type="regular",$numThrown=1,$owner='LWD R&G',$notes=""){
        $this->name=$name;
        $this->size=$size;
        $this->type=$type;
        $this->numThrown=$numThrown;
        $this->owner=$owner;
        $this->notes=$notes;
        $this->totalTargets = 0;
        $this->isManual = false;
        $this->targetColour = getTargetColours(0);
        $this->needTrapper = self::$trapperType[1]; //Default is wanted
    }
    
/*****Attributes common to all machines*******/
    public function getAllowedTypes() {
        return $this->allowedTypes;
    }
    
    public function getAllowedOwners() {
        return $this->allowedOwners;
    }
    
    public function displayAllowedTypes() {
        foreach ((array)$this->allowedTypes as $item) {
            print "$item<br/>";
        }
    }
    
/******Intrinsic machine properties*************/
//These properties are saved

    //Set and get name
    public function setName($name) {
        $this->name=preg_replace('/\s/',"",$name);
    }
    
    public function getName() {
        return $this->name;
    }
    //Set and get size
    public function setSize($size) {
        if ($size == "") {
            $size=0;
        }
        $size = +$size;
        if ($size>=0) {
            $this->size=$size;
        } else {
            $this->size=0;
        }
    }
    
    public function getSize() {
        return $this->size;
    }
    
    //Set and get type
    public function setType($type) {
        $ttype = "";
        foreach ((array)$this->allowedTypes as $item) {
            if (preg_match("/$item/",strtolower($type))===1) {
                $ttype = $item;
            } 
        }
        $this->type = $ttype;
    }
    
    public function getType() {
        return $this->type;
    }
    
    //Set and get numThrown
    public function setNumThrown($numThrown){
        $numThrown = +$numThrown;
        if ($numThrown==1 or $numThrown==2) {
            $this->numThrown = $numThrown;
            return $numThrown;
        } else {
            return -1;
        }
    }
    
    public function getNumThrown(){
        return $this->numThrown;
    }
    
    //Set and get owner
    public function setOwner($owner) {
        $this->owner = ltrim($owner);
    }
    
    public function getOwner() {
        return $this->owner;
    }
    
    //Set and get notes
    public function setNotes($notes) {
        $this->notes = ltrim($notes);
    }
    
    public function getNotes(){
        return $this->notes;
    }
    
    //Set and get isManual
    public function setManual($isManual) {
        if (is_bool($isManual)) {
            $this->isManual = $isManual;
        } else if ($isManual == "yes" or $isManual == "no") {
            $this->isManual = ($isManual == "yes")?true:false;
        } else {
            $this->isManual = false;
        }
        
        if (!$this->isManual) {
            $this->needTrapper = self::$trapperType[0];
        }
    }
    
    public function getManual($opt = null) {
        if ($opt === null) {
            return $this->isManual;
        } else if ($opt == "string") {
            return ($this->isManual)?"yes":"no";
        }
    }
    
    public function checkDuplicateNames($machines,$idx) {
        $machinesUsed = array();
        $count = 0;
        foreach ($machines as $machine) {
            if ($count != $idx) {
                $machinesUsed[] = $machine;
            }
            $count++;
        }
        
        $val = findName($this->name,$machinesUsed,1);
        return $val;
    }

/******Functions for loading and saving data**********/

    //Acquires data from filter
    public function getInput($input_type=INPUT_POST) {
        
        $this->setName(filter_input($input_type,"name"));
        $this->setType(filter_input($input_type,"type"));
        $this->setSize(filter_input($input_type,"size"));
        $this->setNumThrown(filter_input($input_type,"numThrown"));
        $this->setOwner(filter_input($input_type,"owner"));
        $this->setNotes(filter_input($input_type,"notes"));
        $this->setManual(filter_input($input_type,"isManual"));
        //print $this->getManual("string") . "<br/>";
        
        return $this;
    }
    
    //Reads data from file and returns an array of Machines
    public function loadData($filename=MACHINE_FILE) {
        
        $fid = fopen($filename,"r");
        if ($fid===false){
            return false;
        }
        
        $machines = array();
        $machineCount = 0;
        
        while (!feof($fid)) {
            $line = fgets($fid);
            //print $line . "<br/>";
            if (preg_match('/name/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-4);
                //print $s . "<br/>";
                $tmp = new Machine();
                $tmp->setName($s);
            } else if (preg_match('/type/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setType($s);
            } else if (preg_match('/numThrown/',$line,$match)===1) {
                $s = substr($line,11);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setNumThrown($s);
            } else if (preg_match('/size/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setSize($s);
            } else if (preg_match('/owner/',$line,$match)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setOwner($s);
            } else if (preg_match('/isManual/',$line)===1) {
                $s = substr($line,11);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setManual($s);
            } else if (preg_match('/notes/',$line,$match)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setNotes($s);
            } else if (preg_match('/}/',$line,$match)===1) {
                $machines[$machineCount]=new Machine();
                $machines[$machineCount] = $tmp;
                $machineCount++;
            }
        }
        
        fclose($fid);
        return $machines;   
    }   //End of loadData
    
    //Takes an array of Machines and saves it in a text file
    public function saveData($machines,$filename=MACHINE_FILE) {
        
        $a = Machine::loadData();   //Loads copy of machines
                
        $fid = fopen($filename,"w");
        if ($fid === false) {
            return false;
        }
        try {
            foreach($machines as $item) {
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
    }   //End of saveData
    
    //Deletes a machine from an array
    public function deleteMachine($machines,$idx) {
        $tmp = $machines;
        $machines = array();
        $count = 0;
        for ($i=0;$i<sizeof($tmp);$i++) {
            if ($i != $idx) {
                $machines[$count] = $tmp[$i];
                $count++;
            }
        }
        return $machines;
    }
    

/******Display of machine properties*************/  
    
    //Display machine properties as HTML.
    public function displayHTML($dispContainer=true,$classname="",$id="",$idArr = false,$machineUse=null) {
        if ($dispContainer) {
            print "<div class=\"$classname\" id=\"$id\">\n";
        }
        $isManual = ($this->isManual)?"Manual machine":"Automatic machine";
print <<<HERE
<br/>
Name: $this->name<br/>
Type: $this->type<br/>
$isManual<br/>
Number of targets thrown: $this->numThrown<br/>
Carousel size: $this->size<br/>
Owner: $this->owner<br/>
Notes: $this->notes<br/>
<br/>
HERE;
        if ($idArr !== false) {
            $str = "colourSelect_stn" . $idArr[0] . "-mac" . $idArr[1];
            if (dispTargetColour($this->type)) {
                print "<label><strong>Target colour: </strong></label><select id=\"$str\">\n";
                //print "<option val=\"-1\"></option>\n";
                $colours = getTargetColours();
                foreach ($colours as $colour) {
                    if ($this->targetColour == $colour) {
                        print "<option val=\"$colour\" selected>$colour</option>\n";
                    } else {
                        print "<option val=\"$colour\">$colour</option>\n";
                    }
                }
                print "</select>\n";
            } else {
                print "<select style=\"display: none\" id=\"$str\"><option val=\"default\"></option></select>\n";
            }
            
            $str = "trapperSelect_stn" . $idArr[0] . "-mac" . $idArr[1];
            if ($this->isManual) {               
                print "<br/><br/><label><strong>Trapper options</strong></label>\n";
                print $this->getTrapperSelect($str) . "<br/>\n";
            } else {
                print "<select style=\"display: none\" $id=\"$str\"><option val=\"0\"></option></select>\n";
            }
            
        } else if ($idArr === false ) {
            if (dispTargetColour($this->type)) {
                print "Target colour: " . $this->targetColour . "<br/>\n";
            }
            if ($this->isManual) {
                print "Trapper: " . $this->needTrapper . "<br/>\n";
            }
        }
        
        if ($machineUse !== null) {
            $machineUse = getAllInArray($this->getName(),$machineUse,"name");
            print "<table>\n<tr><th colspan=\"2\">Used in</th></tr>\n<tr><th>Event</th><th>Station</th></tr>\n";
            for ($i=0;$i<sizeof($machineUse);$i++) {
                $e = $machineUse[$i]["event"];
                $s = $machineUse[$i]["station"];
                print "<tr><td>$e</td><td>$s</td></tr>\n";
            }
            print "</table>\n";
        }
        
        if ($dispContainer) {
            print "</div>\n";
        } else {
            print "\n";
        }
    }   //End displayHTML
    
    
    
    //Display machine properties with buttons for editing and deleting
    public function displayHTMLbutton($id,$classname) {
        if ($this->isManual) {
            $manual = "Manual machine";
        } else {
            $manual = "Automatic machine";
        }
print <<<HERE
<form action="" class="$classname" id="Machine_$id">
<fieldset>
Name: $this->name<br/>
Type: $this->type<br/>
$manual<br/>
Number of targets thrown: $this->numThrown<br/>
Carousel size: $this->size<br/>
Owner: $this->owner<br/>
Notes: $this->notes<br/>
<input type="button" id="edit_$id" value="Edit" onclick="MachineForm($id);"/>
<input type="button" id="delete_$id" value="Delete" onclick="DeleteMachine($id);"/>
</fieldset>
</form>
HERE;
    }   //End displayHTMLbutton
    
    //Returns a string used for writing to text files
    public function displayText() {
        $msg = "name: " . $this->name . " {\r\n";
        $msg .= "\ttype: " . $this->type . "\r\n";
        $msg .= "\tnumThrown: " . $this->numThrown . "\r\n";
        $msg .= "\tsize: " . $this->size . "\r\n";
        $msg .= "\towner: " . $this->owner . "\r\n";
        $msg .= "\tisManual: " . $this->getManual("string") . "\r\n";
        $msg .= "\tnotes: " . $this->notes . "\r\n";
        $msg .= "}\r\n";
        return $msg;
    }
    
    //Returns properties as associative array for use with json_encode
    public function toArray($idx) {
        $a = array("idx" => (int)$idx,
                   "name" => $this->name,
                   "type" => $this->type,
                   "numThrown" => $this->numThrown,
                   "size" => $this->size,
                   "owner" => $this->owner,
                   "isManual" => $this->isManual,
                   "notes" => $this->notes);
        return $a;
    }
    
    //Takes in an array of machines and converts to indexed array of the results of toArray
    function machineArray($machinesIn) {
        $a = array();
        for ($i=0;$i<sizeof($machinesIn);$i++) {
            $a[$i] = $machinesIn[$i]->toArray($i);
        }
        return $a;
    }

/********Properties related to stations and events**********/

    //Set and get number of targets used by machine.  oldNum is used for adding up number of targets for Shoots
    public function setTotalTargets($numShots,$oldNum=0) {
        $this->totalTargets = $numShots * $this->numThrown + $oldNum;
        return $this->totalTargets;
    }
    
    public function getTotalTargets() {
        return $this->totalTargets;
    }
    
    public function setTargetColour($colour) {
        $a = getTargetColours();
        foreach ($a as $b) {
            if ($colour == $b) {
                $this->targetColour = $b;
                return $b;
            }
        }
        $this->targetColour = $a[0];
        return $this->targetColour;
    }
    
    public function getTargetColour() {
        return $this->targetColour;
    }
    
    public function setTrapper($trapper) {
        if (is_numeric($trapper) and $trapper<sizeof(self::$trapperType)) {
            $this->needTrapper = self::$trapperType[$trapper];
        } else if (array_key_exists($trapper,array_flip(self::$trapperType))) {
            $this->needTrapper = $trapper;
        } else {
            return false;
        }
        
        if (!$this->isManual) {
            $this->needTrapper = self::$trapperType[0];
        }
    }
    
    public function getTrapper() {
        return $this->needTrapper;
    }
    
    public function getTrapperSelect($id) {
        $html = "<select id=\"$id\">\n";
        for ($i=0;$i<sizeof(self::$trapperType);$i++) {
            $sel = ($this->needTrapper == self::$trapperType[$i])?"selected":"";
            $html .= "<option $sel value=\"$i\">" . self::$trapperType[$i] . "</option>\n";
        }
        $html .= "</select>\n";
        return $html;
    }
    
    //Add an associated station.  Only unique stations can be added
    public function addStation($station) {
        if (sizeof($this->stations)>=0) {
            foreach ((array)$this->stations as $item) {
                if (strcmp($station->getName(),$item->getName())===0) {
                    return 0;
                }
            }
        }
        $this->stations[] = clone $station;
    }
    
    //Return all associated stations
    public function getStations() {
        return $this->stations;
    }
    
    //Get the names of associated stations as a single string
    public function getStationNames() {
        $msg = "";
        foreach ($this->stations as $station) {
            $msg .= $station->getName() . ", ";
        }
        $msg = substr($msg,0,sizeof($msg)-3);
        return $msg;
    }
    
    //Add the name of an associated event.  No need to check for uniqueness because event names must be unique
    public function addEventName($eventName) {
        $this->eventNames[] = $eventName;
    }
    
    //Return all associated event names
    public function getEventNames() {
        return $this->eventNames;
    }
    
    //Return the number of associated events
    public function getNumEvents() {
        return sizeof($this->eventNames);
    }
    
    public function addEventData($eventName,$stationName,$numShots) {
        $this->eventData[] = array("eName" => $eventName,
                                   "sName" => $stationName,
                                   "numTargets" => $numShots * $this->numThrown);
    }
    
    public function getEventData() {
        return $this->eventData;
    }
    

    
    
}   //End machine object

?>