<?php 

function print_r2($val){
    echo '<pre>';
    print_r($val);
    echo  '</pre>';
}

$array = array(floatval(0.0), floatval(0.0), floatval(0.0));
print_r2($array);

if(isset($array[0])) {
    echo "It is set<br>\n";
} else {
    echo "It is not<br>\n";
}

$array[0] -= 5.1;

print_r2($array);
?>