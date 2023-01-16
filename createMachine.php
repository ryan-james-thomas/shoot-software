<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>
        Create Machine
    </title>
    <link rel="stylesheet"
          type="text/css"
          href="Style.css"/>
    
</head>


<body>
<?php
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");

    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    
    $a->setName(filter_input(INPUT_POST,"txtName"));
    $a->setType(filter_input(INPUT_POST,"type"));
    $a->setOwner(filter_input(INPUT_POST,"owner"));
    $a->setNumThrown(filter_input(INPUT_POST,"numThrown"));
    $a->setSize(filter_input(INPUT_POST,"size"));
    $a->setNotes(filter_input(INPUT_POST,"notes"));
  
    //$a->displayAllowedTypes();
    $a->displayHTML();

    $fid=fopen("TestList.txt","a");
    fwrite($fid,$a->displayText());
    fclose($fid);
    
?>
    
    
    
    
    
    
</body>
</html>