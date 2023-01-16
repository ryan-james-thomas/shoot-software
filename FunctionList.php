<?php

    define("DIVIDER","---HTML begins below---");
    define("STATION_FILE","StationList.txt");
    define("MACHINE_FILE","MachineDatabase.txt");
    define("EVENT_FILE","EventData.txt");
    define("SHOOT_FILE","ShootData.txt");
    define("MAX_NUM_MACHINES",4);
    define("MACHINE_USE","MachineUtilisation.txt");
    
    function recurse_copy($src,$dst) { 
        $dir = opendir($src); 
        mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '\\' . $file) ) { 
                    recurse_copy($src . '\\' . $file,$dst . '\\' . $file); 
                } 
                else { 
                    copy($src . '\\' . $file,$dst . '\\' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }
    
    function recurse_delete($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        if (substr($dir, strlen($dir) - 1, 1) != '\\') {
            $dir .= '\\';
        }
        
        $files = glob($dir . "*", GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                recurse_delete($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
        return true;
    }

    //function getTargetsPerBox($key=null) {
    //    $targets = array("regular" => 135,
    //                     "rabbit" => 117,
    //                     "midi" => 144,
    //                     "mini" => 250,
    //                     "battue" => 180);
    //    if ($key===null) {
    //        return $targets;
    //    } else if (array_key_exists($key,$targets)) {
    //        return $targets[$key];
    //    } else if ($key===0) {
    //        foreach ($targets as $k => &$value) {
    //            $value = 0;
    //        }
    //        return $targets;
    //    } else {
    //        return false;
    //    }
    //}
    
    function getTargetsPerBox($key=null,$key2=null) {
        $targets2 = array("regular" => 135,
                     "rabbit" => 117,
                     "midi" => 144,
                     "mini" => 250,
                     "battue" => 180);
        
        $targets = array();
        $a = new Machine();
        $types = $a->getAllowedTypes();
        $colours = getTargetColours();
        foreach ($types as $type) {
            foreach ($colours as $colour) {
                $targets[$type][$colour] = $targets2[$type];
            }
        }
               
        if ($key===null) {
            return $targets;
        } else if (array_key_exists($key,$targets)) {
            if (array_key_exists($key2,$targets)) {
                return $targets[$key][$key2];
            } else {
                return $targets2[$key];
            }
        } else if ($key===0) {
            foreach ($targets as $k1 => &$subArray) {
                foreach ($subArray as $k2 => &$value) {
                    $value = 0;
                }
            }
            return $targets;
        } else {
            return false;
        }
    }
    
    function getTargetColours($key=null) {
        $colours = array("orange top","orange dome","all black","all pink");
        if ($key===null) {
            return $colours;
        } else if ($key >= 0) {
            return $colours[$key];
        } else {
            $a = array();
            foreach ($colours as $colour) {
                $a[$colour] = 0;
            }
            return $a;
        }
    }
    
    //Sums arrays with same keys
    function addArrays($array1,$array2) {
        $r = array();
        foreach ($array1 as $k1 => $subArray) {
            foreach ($subArray as $k2 => $value) {
                $r[$k1][$k2] = $value + $array2[$k1][$k2];
            }
        }
        return $r;
    }
    
    function dispTargetColour($type) {
        if ($type == "regular") {
            return true;
        } else {
            return false;
        }
    }
    
    //Displays number of targets as table
    function displayTargetTable($numTargets,$numBoxes) {    
print <<<HERE
<table>
    <tr>
        <th>Target type</th>
        <th>Number of targets</th>
        <th>Number of boxes</th>
    </tr>
HERE;
        foreach ($numTargets as $key => $value) {
            $label = ucfirst($key);
            $vSum = array_sum($value);
            $box = array_sum($numBoxes[$key]);
print<<<HERE
    <tr>
        <th>$label</th>
        <td>$vSum</td>
        <td>$box</td>
    </tr>
    
HERE;
            if (dispTargetColour($key)) {
                foreach ($value as $k2 => $v2) {
                    if ($v2 == 0) {
                        continue;
                    }
                    $label = ucfirst($k2);
                    $b2 = $numBoxes[$key][$k2];
print<<<HERE
<tr style="font-size: small">
    <th>$label</th>
    <td>$v2</td>
    <td>$b2</td>
</tr>
HERE;
                }
            }
        }
        print "</table>\n";
    }   //End displayTargetTable

    //function cmp($a,$b) {
    //    //Used for comparing the names of two machines
    //    return strcmp($a->getName(),$b->getName());
    //
    //}
    
    function cmp($a,$b) {
        $name1 = strtoupper($a->getName());
        $name2 = strtoupper($b->getName());
        
        $reg = '/-.+$/';
        $r1 = preg_match($reg,$name1,$s1);
        $r2 = preg_match($reg,$name2,$s2);
        $s1 = (($r1===1)?substr($s1[0],1):"");
        $s2 = (($r2===1)?substr($s2[0],1):"");
        
        
        $name1 = preg_replace($reg,"",$name1);
        //$name1 = ($r1===null)?$name1:$r1;
        $name2 = preg_replace($reg,"",$name2);
        //$name2 = ($r2===null)?$name2:$r2;
        
        if (strcmp($name1,$name2) != 0) {
            return strcmp($name1,$name2);
        } else {
            if (ctype_digit($s1) and ctype_digit($s2)) {
                if ($s1 == $s2) {
                    return 0;
                } else {
                    return ($s1 > $s2)?1:-1;
                }
            } else {
                return strcmp($s1,$s2);
            }
        }       
    }
    
    function cmp_date($a,$b) {
        $dates = $a->getDates();
        $date1 = strtotime($dates["start"]);
        $dates = $b->getDates();
        $date2 = strtotime($dates["start"]);
        
        if ($date1 == $date2) {
            return 0;
        } else {
            return ($date1 < $date2)?-1:1;
        }
        
    }
    
    //Compares station names so that a list like 3, 17, 3A is sorted to 3, 3A, 17
    function stncmp($a,$b) {
        $name1 = strtoupper($a->getName());
        $name2 = strtoupper($b->getName());
        
        $reg = '/^\d+/';
        preg_match($reg,$name1,$s1);
        preg_match($reg,$name2,$s2);
        
        //print $s1[0] . " " . $s2[0] . "<br/>\n";
        $s1 = (int)$s1[0];
        $s2 = (int)$s2[0];
        //print "<em>" . $s1 . " " . $s2 . "</em><br/>\n";
        if ($s1>$s2) {
            return 1;
        } else if ($s1<$s2) {
            return -1;
        } else {
            $r1=preg_match("/[a-zA-z]+/",$name1,$c1);
            $r2=preg_match("/[a-zA-z]+/",$name2,$c2);
            if ($r1===0 and $r2===0) {
                return 0;    
            } else if ($r1===0 and $r2===1) {
                return -1;
            } else if ($r1===1 and $r2===0) {
                return 1;
            } else {
                //print $c1[0] . " " . $c2[0] . "<br/>\n";
                return strcmp($c1[0],$c2[0]);
            }
        
        }
    }
    

    
    //Finds a name if $obj has a getName() method
    function findName($name,$obj,$opt=0) {
        $count = 0;
        foreach ($obj as $item) {
            if (strcmp($item->getName(),$name)==0) {
                if ($opt===0) {
                    return $item;    
                } else if ($opt===1) {
                    return $count;
                }
            }
            $count++;
        }
        return false;
    }
    
    //Finds all instances of an item in an array
    function getAllInArray($item,$array,$idx=0) {
        $out = array();
        foreach ((array)$array as $subArray) {
            if ($item == $subArray[$idx]) {
                $out[] = $subArray;
            }
        }
        if (sizeof($out) == 0) {
            return false;
        } else {
            return $out;
        }
    }
    
    function sortDate($a,$b) {
        $dates = array("Thursday","Friday","Saturday","Sunday","Monday","Tuesday","Wednesday");
        $dates = array_flip($dates);    //Changes values to keys and vice-versa
        $d1 = explode(" ",$a->getDate());
        $d2 = explode(" ",$b->getDate());
        
        $dd1 = $d1[0];
        $dd2 = $d2[0];
        if (array_key_exists($dd1,$dates) === false or array_key_exists($dd2,$dates) === false) {
            return 0;
        }
        if ($dates[$dd1] != $dates[$dd2]) {
            return ($dd1 >$dd2) ? 1:-1;
        } else {
            if ($d1[1] == "AM" and $d1[1] == "PM") {
                return 1;
            } else if ($d1[1] == "PM" and $d1[1] == "AM") {
                return -1;
            } else {
                return 0;
            }
        }
        
    }
    
    function getUniqueNames($array) {
        $arrayNew = array();
        foreach ($array as $a) {
            $item = findName($a->getName(),$arrayNew,1);
            if ($item === false) {
                $arrayNew[] = clone $a;
            }
        }
        return $arrayNew;
    }
    

    

?>