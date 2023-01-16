<?php
    require_once("FunctionList.php");
    require_once("ShootingObjects.php");
    

    $dir = new DirList();
    $dir->getInput();
    $dir->toUser();
    $shootPaths = $dir->getShoots();
    $shoots = array();
    $tmp = new Shoot();
    foreach ($shootPaths as $path) {
        if ($path == $dir->getShootDir()) {
            continue;
        }
        chdir($path);
        $tmp->loadData();
        $shoots[] = clone $tmp;
    }
    usort($shoots,"cmp_date");
    
    $opt = filter_input(INPUT_POST,"opt");
    if ($opt == 0) {
        $i = 0;
        foreach ($shoots as $shoot) {
            $shootName = $shoot->getName();
            $newDir = $dir->getUserDir() . "\\" . $shoot->getName();
            chdir($newDir);
            $numMachines = sizeof(Machine::loadData());
            $classname = "clr" . ($i%2+1);
print<<<HERE
<div class="$classname">
<input type="checkbox" id="check_$i"/><label for="check_$i">$shootName, $numMachines machines</label>
</div>
HERE;
            $i++;
        }
        print '<div class="Buttons">' . "\n";
        print '<input type="button" value="Merge with existing database" onclick="AppendDatabase(' . $i . ');"/>' . "\n";
        print '<input type="button" value="Cancel" onclick="CancelAppendDatabase();"/>' . "\n";
        print '</div>' . "\n";
    } else {
        $chk = filter_input(INPUT_POST,"chk");
        $chk = json_decode($chk);
        $dir->toShoot();
        $machines = Machine::loadData();
        for ($j=0;$j<sizeof($chk);$j++) {
            if ($chk[$j]) {
                $newDir = $dir->getUserDir() . "\\" . $shoots[$j]->getName();
                chdir($newDir);
                $machines = array_merge($machines,Machine::loadData());
            }
        }
        
        $machinesNew = getUniqueNames($machines);
        
        
        $dir->toShoot();
        Machine::saveData($machinesNew);
    }
    
?>