<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dirCopy = new DirList();
    $dir->getInput();
    $dir->toEvent();
    
    array_map("unlink",glob("*"));
    rmdir($dir->getEventDir());
    
    Shoot::updateStations($dir);


?>