<?php

class Machine {
    
    protected $name;
    protected $size;
    protected $type;
    protected $numThrown;
    protected $owner;
    protected $notes;
    protected $allowedTypes= array("regular","rabbit","mini","midi","battue");
    protected $allowedOwners = array("LWD"=>"LWD R&G","GOL"=>"Golden R&G","TAF"=>"Tom Ferguson");
    
    public function __constructor($name="",$size=0,$type="regular",$numThrown=1,$owner='LWD R&G',$notes=""){
        $this->name=$name;
        $this->size=$size;
        $this->type=$type;
        $this->numThrown=$numThrown;
        $this->owner=$owner;
        $this->notes=$notes;
    }
    
    public function setName($name) {
        $this->name=$name;
    }
    
    public function getName() {
        return $this->name;
    }
    
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
    
    public function setType($type) {
        $ttype = "";
        foreach ((array)$this->allowedTypes as $item) {
            //if (strtolower($type)==$item) {
            //    $ttype = $item;
            //}
            if (preg_match("/$item/",strtolower($type))===1) {
                $ttype = $item;
            } 
        }
        $this->type = $ttype;
    }
    
    public function getType() {
        return $this->type;
    }
    
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
    
    public function setOwner($owner) {
        $this->owner = ltrim($owner);
    }
    
    public function getOwner() {
        return $this->owner;
    }
    
    public function setNotes($notes) {
        $this->notes = ltrim($notes);
    }
    
    public function getNotes(){
        return $this->notes;
    }
    
    public function displayHTML($classname) {
print <<<HERE
<p class="$classname">
Name: $this->name<br/>
Type: $this->type<br/>
Number of targets thrown: $this->numThrown<br/>
Carousel size: $this->size<br/>
Owner: $this->owner<br/>
Notes: $this->notes<br/>
</p>
HERE;
    }
    
    public function displayHTMLbutton($id,$classname) {
print <<<HERE
<form action="" class="$classname" id="Machine_$id">
<fieldset>
Name: $this->name<br/>
Type: $this->type<br/>
Number of targets thrown: $this->numThrown<br/>
Carousel size: $this->size<br/>
Owner: $this->owner<br/>
Notes: $this->notes<br/>
<input type="button" id="edit_$id" value="Edit" onclick="MachineForm($id);"/>
<input type="button" id="delete_$id" value="Delete" onclick="DeleteMachine($id);"/>
</fieldset>
</form>
HERE;
    }
    
    public function displayText() {
        $msg = "name: " . $this->name . " {\r\n";
        $msg .= "\ttype: " . $this->type . "\r\n";
        $msg .= "\tnumThrown: " . $this->numThrown . "\r\n";
        $msg .= "\tsize: " . $this->size . "\r\n";
        $msg .= "\towner: " . $this->owner . "\r\n";
        $msg .= "\tnotes: " . $this->notes . "\r\n";
        $msg .= "}\r\n";
        return $msg;
    }
    
    public function toArray($idx) {
        $a = array("idx" => $idx,
                   "name" => $this->name,
                   "type" => $this->type,
                   "numThrown" => $this->numThrown,
                   "size" => $this->size,
                   "owner" => $this->owner,
                   "notes" => $this->notes);
        return $a;
    }

}

?>