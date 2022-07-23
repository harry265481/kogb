<?php 

$a = array(array(1, 200), array(2, 180), array(3, 150));
$b = json_encode($a);
echo $b . "<br><br>";

$a = json_decode($b);
print_r($a);

?>