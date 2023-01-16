<?php
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $filename = "MachineDatabase.txt";
    $directory = "RyanThomas";
    chdir('./' . $directory);
    $machines = readMachineList($filename);
    $stations = readStationList("StationList.txt",$machines);
    
    //$station = new Station();
    //$station->setName("1");
    //$station->setUse(true);
    //$station->setNotes("");
    //$station->setNumShots(4);
    //$station->setTargetStyle("doubles");
    //$station->addMachine($machines[3]);
    //$station->addMachine($machines[5]);
    
    $idx=0;
    

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <script type="text/javascript"
            src="jquery-3.2.1.min.js"></script>
    <link rel="stylesheet"
          type="text/css"
          href="MachineFormStyle2.css"/>
  
    <script type="text/javascript">
        
        function HideStationInfo(id) {
            var a = document.getElementById("check_" + id);
            if (a.checked) {
                $("#StationInfo_" + id).css("display","inline");
            } else {
                $("#StationInfo_" + id).css("display","none");
            }
        }
        
        
    </script>

</head>

<body>
<?php
    foreach ($stations as $item) {
        $classname = "clr" . (($idx%2)+1);
        $item->displayHTML($idx,$classname);
        $idx++;
    }

?>

    
</body>

</html>