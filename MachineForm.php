<?php
        //Creates a form for editing or creating a new machine
        require_once("ShootingObjects.php");
        require_once("FunctionList.php");
    
        $dir = new DirList();
        $dir->getInput();
        $dir->toShoot();
        
        $machines = Machine::loadData();
        $idx = filter_input(INPUT_POST,"idx");
        if ($idx==-1) { //For new machines use blank or default values
                $idx = sizeof($machines);
                $name = "";
                $type = "regular";
                $size = "";
                $numThrown = 1;
                $owner = "LWD R&G";
                $isManual = false;
                $notes = "";
                $machine = new Machine();
                $newMachine = 1;
        } else {        //Otherwise read from database
                $machine = $machines[$idx];
                $name = $machine->getName();
                $type = $machine->getType();
                $size = $machine->getSize();
                $numThrown = $machine->getNumThrown();
                $owner = $machine->getOwner();
                $isManual = $machine->getManual();
                $notes = $machine->getNotes();
                $newMachine = 0;
        }
        $classname = "clr" . (($idx%2)+1);
        $chkManual = $isManual?"checked":"";

        
print<<<HERE
<form action="" class="$classname" id="Machine_$idx">
        <fieldset>
            <label>Machine name</label>
            <input type="text"
                   id="name_idx$idx"
                   name="name_idx$idx"
                   value="$name"/>
            <label id="name_chk_idx$idx" style="display: none">Name is not unique!</label>
            <br/>
            <label for="isManual_idx$idx">Manual machine?</label>
            <input type="checkbox" id="isManual_idx$idx" $chkManual/>
            <br/>
            <label>Type</label>
            
HERE;
        //Machine type
        $s = '<input type="radio" name="type_nn" id="radtt_nn" value="tt" cc/><label for="radtt_nn">tt</label>';
        foreach ((array)$machine->getAllowedTypes() as $allowedType) {
                $s2 = str_replace("nn","idx$idx",$s);
                $s2 = str_replace("tt",ucfirst($allowedType),$s2);
                //print $type . " " . $allowedType . " " . (preg_match("/$allowedType/",$type)) . "<br/>\n";
                if (preg_match("/$allowedType/",$type)==1) {
                        $s2 = str_replace("cc",'checked="checked"',$s2);        
                } else if (preg_match("/$allowedType/",$type)==0) {
                        $s2 = str_replace("cc","",$s2); 
                }
                print $s2 . "\n";
          
        }
        //Number of targets thrown
print<<<HERE
            <br/>
            <label>Number thrown</label>
HERE;
        $s1 = '<input type="radio" name="numThrown_nn" id="rad1_nn" value="1" cc/><label for="rad1_nn">1</label>';
        $s2 = '<input type="radio" name="numThrown_nn" id="rad2_nn" value="2" cc/><label for="rad2_nn">2</label>';
        
        $sArr = array(1=>$s1,2=>$s2);
        
        foreach ($sArr as $key => $value) {
                $value = str_replace("nn","idx$idx",$value);
                if ($numThrown == $key) {
                        $value = str_replace("cc",'checked="checked"',$value);
                } else {
                        $value = str_replace("cc","",$value);
                }
                print $value . "\n";
        }
        //Machine's carousel size
print<<<HERE
            <br/>
            <label>Size</label>
            <input type="text"
                   id="size_idx$idx"
                   name="size_idx$idx"
                   value="$size"/>
            <br/>
            <label>Owner</label>
HERE;
        //Machine's owner
        $s = '<input type="radio" name="owner_nn" id="radkk_nn" value="oo" cc/><label for="radkk_nn">oo</label>';
        $arrOwner = $machine->getAllowedOwners();
        foreach($arrOwner as $key => $value){
                $s2 = str_replace("kk",$key,$s);
                $s2 = str_replace("oo",$value,$s2);
                $s2 = str_replace("nn","idx$idx",$s2);
                if (preg_match("/$value/",$owner)==1) {
                        $s2 = str_replace("cc",'checked="checked"',$s2);
                } else {
                        $s2 = str_replace("cc","",$s2);
                }
                print $s2 . "\n";
        }
        
        //Additional notes
print<<<HERE
            <br/>
            <label>Notes</label>
            <br/>
            <textarea id="notes_idx$idx" name="notes_idx$idx" rows="4" cols="50">$notes</textarea>
            <div style="float: left;clear: both">
            <input type="button" id="Save" value="Save" onclick="SaveMachineForm($idx);"/>
            <input type="button" id="Cancel" value="Cancel" onclick="CancelMachineForm($idx,$newMachine);"/>
            </div>
            
        </fieldset>
</form>
HERE;
?>