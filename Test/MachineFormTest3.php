<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>
        Create Machine
    </title>
    <link rel="stylesheet"
          type="text/css"
          href="MachineFormStyle.css"/>
    
</head>


<body>
    <h1>Create machine</h1>
    <form action="MachineFormTest3.php"
          method="post">
        <fieldset>
            <label>Machine name</label>
            <input type="text"
                   id="txtName"
                   name="txtName"/>
            <br/>
            <label>Type</label>         
            <input type="radio"
                   name="type"
                   id="radReg"
                   value="Regular"
                   checked="checked"/>
            <label for="radReg">Regular</label>
            <input type="radio"
                   name="type"
                   id="radRabbit"
                   value="Rabbit"/>
            <label for="radRabbit">Rabbit</label>
            <input type="radio"
                   name="type"
                   id="radMini"
                   value="Mini"/>
            <label for="radMini">Mini</label>
            <input type="radio"
                   name="type"
                   id="radMidi"
                   value="Midi"/>
            <label for="radMidi">Midi</label>
            <input type="radio"
                   name="type"
                   id="radBattue"
                   value="Battue"/>
            <label for="radBattue">Battue</label>
            <br/>
            <label>Number thrown</label>         
            <input type="radio"
                   name="numThrown"
                   id="radNum1"
                   value="1"
                   checked="checked"/>
            <label for="radNum1">1</label>
            <input type="radio"
                   name="numThrown"
                   id="radNum2"
                   value="2"/>
            <label for="radNum2">2</label>
            <br/>
            <label>Size</label>
            <input type="text"
                   id="txtSize"
                   name="size"/>
            <br/>
            <label>Owner</label>
            <input type="radio"
                   name="owner"
                   id="radLWD"
                   checked="checked"
                   value="LWD R&G"/>
            <label for="radLWD">LWD R&G</label>
            <input type="radio"
                   name="owner"
                   id="radGolden"
                   value="Golden R&G"/>
            <label for="radGolden">Golden R&G</label>
            <input type="radio"
                   name="owner"
                   id="radTAF"
                   value="TAF"/>
            <label for="radTAF">Tom Ferguson</label>
            <br/>
            <label>Notes</label>
            <br/>
            <textarea id="notes" name="notes">
            </textarea>
            
            <button type="submit"
                    class="button"
                    id="createMachine">
                Create Machine
            </button>
            
        </fieldset>
    </form>
    
    <div id="machineList">
        <?php
        
            require_once("MachineObject.php");
            require_once("FunctionList.php");
            
            if (filter_has_var(INPUT_POST,"txtName")) {
                $a = new Machine();
    
                $a->setName(filter_input(INPUT_POST,"txtName"));
                $a->setType(filter_input(INPUT_POST,"type"));
                $a->setOwner(filter_input(INPUT_POST,"owner"));
                $a->setNumThrown(filter_input(INPUT_POST,"numThrown"));
                $a->setSize(filter_input(INPUT_POST,"size"));
                $a->setNotes(filter_input(INPUT_POST,"notes"));
              
                //$a->displayAllowedTypes();
                //$a->displayHTML();
                
                $fid=fopen("TestList.txt","a");
                fwrite($fid,$a->displayText());
                fclose($fid);
            }
        
        
            $machines = readMachineList("TestList.txt");
            $i = 0;
            foreach ($machines as $item) {
                $item->displayHTML("clr" . (($i%2)+1));
                $i++;
            }

        ?>
    </div>
    
</body>



</html>