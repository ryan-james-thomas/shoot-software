<?php

    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toUser();            
    $dp = opendir(".");           
    $currentFile = "";
    $shoots = array();
    $count = 0;
    $shoot = new Shoot();
    $shootArr = array();
    while ($currentFile !== false) {
        $currentFile = readdir($dp);
        if ($currentFile == "." or $currentFile == "..") {
            continue;
        }
        if (strlen($currentFile)!=0 and is_dir($dir->getUserDir() . "\\" . $currentFile)) {
            $shootArray[$count] = $currentFile;
            chdir($dir->getUserDir() . "\\" . $currentFile);
            $shoots[$count] = clone $shoot;
            $shoots[$count]->loadData();
            $dir->toUser();
            $count++;
        }
    }
    closedir($dp);   
    usort($shoots,"cmp_date");
    foreach ($shoots as $shoot) {
        $shootArr[] = $shoot->getName();
    }
    
    print json_encode($shootArr);
    print DIVIDER;
    
    $count = 0;
    foreach ($shoots as $shoot) {
        $name = $shoot->getName();
        $dates = $shoot->getDates();
        $startDate = $dates["start"];
        $endDate = $dates["end"];
        $notes = $shoot->getNotes();
        $classname = "clr" . ($count%2+1);
print<<<HERE
<div id="shoot_$count" class="$classname ShootContainer">
<label><strong>Name: </strong>$name</label>
<label><strong>Start: </strong>$startDate</label>
<label><strong>End: </strong>$endDate</label>
<input type="button" class="button" value="Delete" onclick="deleteShoot($count);"/>
<input type="button" class="button" value="Copy" onclick="newShoot($count);"/>
<input type="button" class="button" value="Edit" onclick="editShoot($count);"/>
</div>

HERE;
    $count++;
    }



?>