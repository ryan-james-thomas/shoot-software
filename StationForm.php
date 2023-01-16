<?php
        //Creates a form for editing or creating a new station
        require_once("MachineObject.php");
        require_once("ShootingObjects.php");
        require_once("FunctionList.php");
        
        $station = new Station();
        
        $dir = new DirList();
        $dir->getInput();
        $dir->toShoot();
        $machines = Machine::loadData();
        $machineUse = Shoot::loadMachineUtilisation();
        //$machineUse = null;
        $dir->toEvent();
        $stations = Station::loadData($machines);
        $idx = filter_input(INPUT_POST,"idx");
        $newStation = false;
        if ($idx==-1) { //For new stations use blank or default values
                $idx = sizeof($stations);
                $name = "";
                $inUse = true;
                $numShots = 0;
                $targetStyle = "doubles";
                $targetFormat = "TP";
                $stationMachines = null;
                $notes = "";
                $stnError = false;
                $checkFP = "";
                $station = new Station();
                $newStation = true;
        } else {        //Otherwise read from database
                $station = $stations[$idx];
                $name = $station->getName();
                $inUse = $station->getUse();
                $numShots = $station->getNumShots();
                $targetStyle = $station->getTargetStyle();
                $targetFormat = $station->getTargetFormat();
                $checkFP = ($station->getCheckFP())?"checked":"";
                $stationMachines = $station->getMachines();
                $notes = $station->getNotes();
                $stnError = $station->checkStnError();
        }
        $classname = "clr" . (($idx%2)+1);
        if ($inUse) {
                $check = 'checked="checked"';
        } else {
                $check = "";
        }
        

        //Station name and number of shots
        $newIdx = 0;
        if ($newStation) {
                print "<div id=\"StationContainer_$idx\" class=\"$classname StationContainer\">\n";
                $newIdx = 1;
        }
print<<<HERE
<form action="" class="StationForm" id="Station_$idx">
        <fieldset>
            <label>Station name</label>
            <input type="text" class="StationText"
                   id="name_idx$idx"
                   name="name_idx$idx"
                   value="$name"/>
            <label id="name_chk_idx$idx" style="display: none">Name is not unique!</label>
            <label for="check_$idx" style="margin-left: 2em">In Use?</label>
            <input type="checkbox" style="margin-left: 0.25em" id="check_$idx" $check />
            <br/>
            <label>Number of shots</label>
            <input type="text" class="StationText"
                   id="numShots_idx$idx"
                   name="numShots_idx$idx"
                   value="$numShots"/>
        
            
            
HERE;
        //Target style
        $s = '<input type="radio" name="style_nn" id="radss_nn" value="ss" cc/><label for="radss_nn">ss</label>';
        foreach ((array)$station->getAllowedStyles() as $allowedStyle => $num) {
                $s2 = str_replace("nn","idx$idx",$s);
                $s2 = str_replace("ss",ucfirst($allowedStyle),$s2);
                if (preg_match("/$allowedStyle/",$targetStyle)==1) {
                        $s2 = str_replace("cc",'checked="checked"',$s2);        
                } else if (preg_match("/$allowedStyle/",$targetStyle)==0) {
                        $s2 = str_replace("cc","",$s2); 
                }
                print $s2 . "\n";
          
        }
        
        //Additional notes
print<<<HERE
            <br/>
            <label>Target format</label>
            <input type="text" id="targetFormat_idx$idx" value="$targetFormat" style="width: 5em"/>
            <input type="checkbox" style="margin-left: 0.25em" id="checkFP_idx$idx" $checkFP/>
            <label for="checkFP_idx$idx">Single machine following pair?</label>
            <br/>
HERE;
      
print<<<HERE
            <label>Notes</label>
            <br/>
            <textarea id="notes_idx$idx" name="notes_idx$idx" rows="4" cols="50">$notes</textarea>
            <div id="StationMachine_$idx" class="MachineInfo"></div>
            <div class="FinalButtons">
            <input type="button" id="Save" value="Save" onclick="SaveStationForm($idx);"/>
            <input type="button" id="Cancel" value="Cancel" onclick="CancelStationForm($idx,$newIdx);"/>
            </div>
            
        </fieldset>
</form>
HERE;
        //Error display
            if ($stnError===true) {
                $errorDisp = "inline";
            } else {
                $errorDisp = "none";
            }
        print "<h3 id=\"StationError_$idx\" class=\"StationError\" style=\"display: $errorDisp\">Target style does not match machine characteristics!</h3>\n";
        
        //Machine selector
        for ($i=0;$i<MAX_NUM_MACHINES;$i++) {
                $tmpName="";
                if ($i<sizeof($stationMachines)) {
                        $tmpName = $stationMachines[$i]->getName();
                }
print<<<HERE
        <div class="MachineInfo StationInfo_$idx" id="MachineDisp_stn$idx-mac$i">
        <select id="select_stn$idx-mac$i" class="MachineSelector" onchange="DisplayMachineProperties($idx,$i);">
HERE;
                if ($i<sizeof($stationMachines)) {
                        print "<option value=\"-1\"></option>\n";
                } else {
                        print "<option value=\"-1\" selected=\"selected\"></option>\n";
                }
                $aa=0;
                foreach ($machines as $item) {
                        $tmpName2 = $item->getName();
                        if ($tmpName2 == $tmpName) {
                                print "<option id=\"option_stn$idx-mac$i-$aa\" value=\"$aa\" selected=\"selected\">$tmpName2</option>\n";
                        } else {
                                print "<option id=\"option_stn$idx-mac$i-$aa\" value=\"$aa\">$tmpName2</option>\n";
                        }
                        $aa++;
                }
                print "</select>\n<br/>\n";
                if ($i<sizeof($stationMachines)) {
                        $stationMachines[$i]->displayHTML(true,"","MachineHTML_stn$idx-mac$i",array($idx,$i),$machineUse);
                } else {
                        print "<p class=\"\" id=\"MachineHTML_stn$idx-mac$i\">\n</p>\n";
                }

                print "</div>\n";       //End MachineInfo div
        }
        print "<h3 id=\"MachineErrors_$idx\" class=\"StationError\" style=\"display: none\"></h3>\n";
        if ($newStation) {
                print "</div>\n";
        }

?>