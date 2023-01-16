<?php
    function readData($filename) {
        $fid=fopen($filename,"r");
        while (!feof($fid)) {
            $line=fgets($fid);
            print $line . "<br/>\n";
        }
    }

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">

</head>

<body>
<?php
    print getcwd() . "<br/>\n";
    chdir("./RyanThomas");
    print getcwd() . "<br/>\n";
    readData("MachineDatabase.txt");
?>
    
</body>

</html>