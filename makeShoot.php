<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dirCopy = new DirList();
    $dir->getInput();
    $dir->toUser();
    $shoot = new Shoot();
    
    $name = filter_input(INPUT_POST,"name");
    
    if (!filter_has_var(INPUT_POST,"copyName")) {
        try {
            $dir->setShootDir($name);
            mkdir($dir->getShootDir());
            $dir->toShoot();
            $fid = fopen(MACHINE_FILE,"w");
            fclose($fid);
            $fid = fopen(SHOOT_FILE,"w");          
            $shoot->setName($name);
            fwrite($fid,$shoot->displayText());
            fclose($fid);
        } catch (E_WARNING $e) {
            print "Error creating event.<br/>\n";
        }
    } else {        
        $dirCopy = clone $dir;
        $dirCopy->setShootDir(filter_input(INPUT_POST,"copyName"));
        try {
            $dir->setShootDir($name);
            recurse_copy($dirCopy->getShootDir(),$dir->getShootDir());
            $dir->toShoot();
            $shoot->loadData();
            $shoot->setName($name);
            $shoot->saveData();
            
        } catch (E_WARNING $e) {
            print "Error creating event.<br/>\n";
        }
    }





?>