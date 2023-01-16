
var DIVIDER = "---HTML begins below---";
var divideRegEx = new RegExp("^.*" + DIVIDER);


function DirList(ud="",sd="",ed="") {
    this.userDir = ud;
    this.shootDir = sd;
    this.eventDir = ed;
}

function getRadioValue(eName) {
    //Gets the value of the checked radio button specified by eName
    var a = document.getElementsByName(eName);
    var b;
    var c;
    for (i=0;i<a.length;i++){
        b=a[i];
        if (b.checked) {
            c = b.value;
        }
    }
    return c;
}
    
function checkNames(inName,id,obj) {
    if (inName==="") {
        return false;
    }
    
    if (obj.length == 0) {
        return true;
    }
    
    if (obj[0].hasOwnProperty('name'))
        for (i=0;i<obj.length;i++) {
            if (inName == obj[i].name & id != i) {
                return false;
            }
        }
    else {
       for (i=0;i<obj.length;i++) {
            if (inName == obj[i] & id != i) {
                return false;
            }
       }
    }
    return true;           
}

function checkMachineUse(stations,id,machines) {
    
    var inUse = [];
    var count = 0;
    
    for (i=0;i<machines.length;i++) {
        for (j=0;j<stations.length;j++) {
            if (j == id || stations[j].inUse === false) {
                continue;
            }
            var str = stations[j].machines.split(", ");
            for (k=0;k<str.length;k++) {
                if (str[k] == machines[i].name) {
                    inUse[count] = machines[i].idx;
                    count++;
                }
            }
        }
    }
    
    return inUse;
}

function getMachineIndicies(name,machines) {
    for (i=0;i<machines.length;i++) {
        if (name == machines[i].name) {
            return machines[i].idx;
        }
    }
    return -1;
}

function globalMachineCheck(names,stations,id) {
    var msg = "";
    var station;
        
    var count = 0;   
    for (i=0;i<names.length;i++) {
        //Checks within current set of names
        for (j=i+1;j<names.length;j++) {
            if (names[i] == names[j]) {
                count++;
                msg += "Machine " + names[i] + " already used in current station!<br/>\n";
            }
        }
        
        for (j=0;j<stations.length;j++) {
            if (j == id || stations[j].inUse == false) {
                continue;
            }
            station = stations[j];
            var str = station.machines.split(", ");
            for (k=0;k<str.length;k++) {
                if (str[k] == names[i]) {
                    count++;
                    msg += "Machine " + names[i] + " already used in station " + station.name + "!<br/>\n";
                }
            }
        }   
    }
    
    return msg;  
}

function checkTargetStyle(style,format,indicies,machines) {
    
    var numThrown = 0;
    var styleNum;
    
    if (style === "doubles") {
        styleNum = 2;
    } else if (style === "singles") {
        styleNum = 1;
    }
    
    for (i=0;i<indicies.length;i++) {
        if (indicies[i]!=-1) {
            numThrown += machines[indicies[i]].numThrown;
        }
    }
        
    var noErr;
    if (numThrown == styleNum) {
        if (numThrown == 1 && format == "S") {
            noErr = true;
        } else if (numThrown == 2 && format == "FP") {
            noErr = false;    
        } else if (numThrown == 2 && format != "S") {
            noErr = true;
        } else {
            noErr = false;
        }
    } else if (numThrown == 1 && styleNum == 2 && format == "FP") {
        noErr = true;
    } else {
        noErr = false;
    }
    return noErr;
    
    
}

