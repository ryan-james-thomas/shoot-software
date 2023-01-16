<?php
        require_once("MachineObject.php");
        require_once("FunctionList.php");
        
        $machine = new Machine();
        
        $filename = filter_input(INPUT_POST,"filename");
        $machines = readMachineList($filename);
        $idx = filter_input(INPUT_POST,"id");
        $machine = $machines[$idx];
        $name = $machine->getName();
        $type = $machine->getType();
        $size = $machine->getSize();
        $owner = $machine->getOwner();
        $notes = $machine->getNotes();
        

print<<<HERE
<form action="">
        <fieldset>
            <label>Machine name</label>
            <input type="text"
                   id="$name"
                   name="$name"
                   value="$name"/>
            <br/>
            <label>Type</label>
            
HERE;
        $s = '<input type="radio" name="type_nn" id="radtt_nn" value="tt" cc/><label for="radtt_nn">tt</label>';
        foreach ((array)$machine->getAllowedTypes() as $allowedType) {
                $s2 = str_replace("nn",$name,$s);
                $s2 = str_replace("tt",ucfirst($allowedType),$s2);
                //print $type . " " . $allowedType . " " . (preg_match("/$allowedType/",$type)) . "<br/>\n";
                if (preg_match("/$allowedType/",$type)==1) {
                        $s2 = str_replace("cc",'checked="checked"',$s2);        
                } else if (preg_match("/$allowedType/",$type)==0) {
                        $s2 = str_replace("cc","",$s2); 
                }
                print $s2 . "\n";
          
        }
print<<<HERE
            <br/>
            <label>Number thrown</label>
HERE;
        $s1 = '<input type="radio" name="numThrown_nn" id="rad1_nn" value="1" cc/><label for="rad1_nn">1</label>';
        $s2 = '<input type="radio" name="numThrown_nn" id="rad2_nn" value="2" cc/><label for="rad2_nn">2</label>';
        
        $sArr = array(1=>$s1,2=>$s2);
        
        foreach ($sArr as $key => $value) {
                $value = str_replace("nn",$name,$value);
                if ($machine->getNumThrown() == $key) {
                        $value = str_replace("cc",'checked="checked"',$value);
                } else {
                        $value = str_replace("cc","",$value);
                }
                print $value . "\n";
        }
print<<<HERE
            <br/>
            <label>Size</label>
            <input type="text"
                   id="size_$name"
                   name="size_$name"
                   value="$size"/>
            <br/>
            <label>Owner</label>
HERE;
        
        $s = '<input type="radio" name="owner_nn" id="radkk_nn" value="oo" cc/><label for="radkk_nn">oo</label>';
        $arrOwner = array("LWD"=>"LWD R&G","GOL"=>"Golden R&G","TAF"=>"Tom Fergueson");
        foreach($arrOwner as $key => $value){
                $s2 = str_replace("kk",$key,$s);
                $s2 = str_replace("oo",$value,$s2);
                if (preg_match("/$value/",$owner)==1) {
                        $s2 = str_replace("cc",'checked="checked"',$s2);
                } else {
                        $s2 = str_replace("cc","",$s2);
                }
                print $s2 . "\n";
        }
        
        
print<<<HERE
            <br/>
            <label>Notes</label>
            <br/>
            <textarea id="notes_$name" name="notes_$name" rows="4" cols="50">$notes</textarea>
            
            <input type="button" id="Save" value="Save" onclick="SaveMachineForm($idx);"/>
            <input type="button" id="Cancel" value="Cancel" onclick="CancelMachineForm($idx);"/>
            
        </fieldset>
</form>
HERE;
?>