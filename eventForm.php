<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();   
    $dir->getInput();
    $dir->toShoot();
    $idx = filter_input(INPUT_POST,"idx");
    
    if (filter_has_var(INPUT_POST,"name")) {
        $copyIdx = filter_input(INPUT_POST,"copyIdx");
        $name = filter_input(INPUT_POST,"name");
        $name2 = "Copy of " . $name;
        $disp = 'style="display: inline"';
    } else {
        $name = "";
        $name2 = $name;
        $copyIdx = -1;
        $disp = 'style="display: none"';
    }
    $classname = "clr" . ($idx%2+1);

print<<<HERE
<div id="event_$idx" class="$classname EventContainer">
<label><strong>Name</strong></label>
<input type="text" id="eventName_$idx" value="$name2"/>
<label id="name_chk_$idx" style="display: none">Name is not unique!</label>
<label $disp><strong>Copy of $name</strong></label>
<input type="button" class="button" value="Cancel" onclick="cancelEvent($idx);"/>
<input type="button" class="button" value="Make Event" onclick="makeEvent($idx,$copyIdx);"/>
</div>
HERE;

?>