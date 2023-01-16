<?php

    require_once("MachineObject.php");
    require_once("StationObject.php");
    require_once("ShootEventObject.php");
    require_once("ShootObject.php");
    
    class DirList {
        protected $userDir;
        protected $shootDir;
        protected $eventDir;
        
        public function __constructor($userDir="",$shootDir="",$eventDir="") {
            $this->userDir = $this->setUserDir($userDir);
            $this->shootDir = $this->setShootDir($shootDir);
            $this->eventDir = $this->setEventDir($eventDir);
        }

        
        public function getInput() {
            $this->userDir = $this->setUserDir(filter_input(INPUT_POST,"userDir"));
            $this->shootDir = $this->setShootDir(filter_input(INPUT_POST,"shootDir"));
            $this->eventDir = $this->setEventDir(filter_input(INPUT_POST,"eventDir"));
        }
        
        public function setUserDir($ud) {
            $this->userDir = getcwd() . "\\" . $ud;
            return $this->userDir;
        }
        
        public function getUserDir() {
            return $this->userDir;
        }
        
        public function setShootDir($sd) {
            $this->shootDir = $this->userDir . "\\" . $sd;
            return $this->shootDir;
        }
        
        public function getShootDir() {
            return $this->shootDir;
        }
        
        public function setEventDir($ed) {
            $this->eventDir = $this->shootDir . "\\" . $ed;
            return $this->eventDir;
        }
        
        public function getEventDir() {
            return $this->eventDir;
        }
        
        public function toUser() {
            chdir($this->userDir);
        }
        
        public function toShoot() {
            chdir($this->shootDir);
        }
        
        public function toEvent() {
            chdir($this->eventDir);
        }
        
        public function getEvents() {
            $this->toShoot();            
            $dp = opendir(".");
    
            $currentFile = "";
            $shootArray = array();
            $count = 0;
            while ($currentFile !== false) {
                $currentFile = readdir($dp);
                if ($currentFile == "." or $currentFile == "..") {
                    continue;
                }
                $newDir = $this->shootDir . "\\" . $currentFile;
                if (strlen($currentFile)!=0 and is_dir($newDir)) {
                    $shootArray[$count] = $newDir;
                    $count++;
                }
            }
            closedir($dp);
            return $shootArray;
        }
        
        public function getShoots() {
            $this->toUser();            
            $dp = opendir(".");
    
            $currentFile = "";
            $shootArray = array();
            $count = 0;
            while ($currentFile !== false) {
                $currentFile = readdir($dp);
                if ($currentFile == "." or $currentFile == "..") {
                    continue;
                }
                $newDir = $this->userDir . "\\" . $currentFile;
                if (strlen($currentFile)!=0 and is_dir($newDir)) {
                    $shootArray[$count] = $newDir;
                    $count++;
                }
            }
            closedir($dp);
            return $shootArray;
        }
        
        
    }
    
    

    


    



?>