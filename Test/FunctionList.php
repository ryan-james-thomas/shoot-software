<?php

    function readMachineList($filename) {
        //Reads machine database and returns the data
        require_once("MachineObject.php");
        
        $fid = fopen($filename,"r");
        
        $machines = array();
        $machineCount = 0;
        
        while (!feof($fid)) {
            $line = fgets($fid);
            //print $line . "<br/>";
            if (preg_match('/name/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-4);
                //print $s . "<br/>";
                $tmp = new Machine();
                $tmp->setName($s);
            } else if (preg_match('/type/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setType($s);
            } else if (preg_match('/numThrown/',$line,$match)===1) {
                $s = substr($line,11);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setNumThrown($s);
            } else if (preg_match('/size/',$line,$match)===1) {
                $s = substr($line,6);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setSize($s);
            } else if (preg_match('/owner/',$line,$match)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setOwner($s);
            } else if (preg_match('/notes/',$line,$match)===1) {
                $s = substr($line,7);
                $s = substr($s,0,strlen($s)-2);
                //print $s . "<br/>";
                $tmp->setNotes($s);
            } else if (preg_match('/}/',$line,$match)===1) {
                $machines[$machineCount]=new Machine();
                $machines[$machineCount] = $tmp;
                $machineCount++;
            }
            
            
            
        }
        
        fclose($fid);

        return $machines;   
    }

    function cmp($a,$b) {
        //Used for comparing the names of two machines
        return strcmp($a->getName(),$b->getName());

    }



?>