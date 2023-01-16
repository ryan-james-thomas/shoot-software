<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();   
    $dir->getInput();
    $dir->toUser();
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
<div id="shoot_$idx" class="$classname ShootContainer">
<label><strong>Name</strong></label>
<input type="text" id="shootName_$idx" value="$name2"/>
<label id="name_chk_$idx" style="display: none">Name is not unique!</label>
<label $disp><strong>Copy of $name</strong></label>
<input type="button" class="button" value="Cancel" onclick="cancelShoot($idx);"/>
<input type="button" class="button" value="Make Event" onclick="makeShoot($idx,$copyIdx);"/>
</div>
HERE;

?>