<?php
include_once("Wawa.php");
echo "Powered By @BuffFreak ".date("Y")."\n";
$voc = new Wawa("19124971", "d32f3fbc0fa91bb2");
while(true){
    $save = $voc->saveData();
    echo $save[1]."\n";
    sleep(1);
}
?>