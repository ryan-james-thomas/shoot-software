<?php

    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    $event = new ShootEvent();
    $event->loadData();
    $event->setStations($stations);

    $numRows = filter_input(INPUT_POST,"numRows");
    $nameSpanCol = 8;
    $nameSpanRow = 5;
    
    
    print "<h2>Scorecard for " . $event->getName() . "</h2>\n"; //Title
    print "<h2 id=\"rotation\">Rotation: _________________</h2>\n";
    print "<table>\n<tr class=\"HeaderRow\">\n<th class=\"NameColumn HeaderRow\" >Names</th>\n";
    foreach ($stations as $station) {
        if ($station->getUse() === false) {
            continue;
        }
        $name = $station->getName();
        print "<th class=\"StationColumn HeaderRow\">$name</th>\n";
    }
    $numTargets = $event->setNumTargetsPerShooter();
    print "<th class=\"StationColumn HeaderRow\">Total (/$numTargets)</th>\n";
    print "</tr>\n";
    
    for ($i=0;$i<$numRows;$i++) {
        print "<tr>\n";
        print "<td class=\"NameColumn\"></td>\n";
        foreach ($stations as $station) {
            if ($station->getUse() === false) {
               continue;
            }
            print "<td class=\"StationColumn TableContainer\">\n";
            print "<table class=\"InnerTable\">\n";
            for ($j=0;$j<=$nameSpanRow;$j++) {
                if ($j == $nameSpanRow) {
                    print "<tr><td colspan=\"2\" style=\"border-top: 2px solid black;height: 0.2in\"></td></tr>\n";  
                } else if ($j < $station->getNumShots()) {
                    if ($station->getTargetStyle() == "doubles") {
                        print "<tr><td></td><td></td></tr>\n";
                    } else {
                        print "<tr><td></td><td class=\"Unused\"></td></tr>\n";
                    }                
                } else {
                    print "<tr class=\"Unused\"><td></td><td></td></tr>\n";
                }
                
            }
            print "</td></table>\n";
        }
        print "<td class=\"StationColumn\"></td>\n";
        print "</tr>\n";
    }
    
    print "<tr style=\"height: 0.25in\">\n<th>Format</th>\n";
    foreach ($stations as $station) {
        if ($station->getUse() === false) {
               continue;
            }
        $targetFormat = $station->getTargetFormat();
        print "<th class=\"FormatColumn\">$targetFormat</th>\n";
    }
    print "<td class=\"FormatColumn\"></td>\n";
    print "</tr>\n";
    
    print "</table>\n";


?>