<?php

//read input into array
$array = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $array[] = trim($input_line);
}
fclose($input);
              
$increases = 0;
$previous = reset($array); //get the first value
foreach( $array as $value ){
    if($value > $previous) //if new value greater than previous, we have an increase! else do nothing.
        $increases++; 
    $previous = $value; //afterwards, set new value as the previous one
}
echo "$increases measurements are larger than the previous." . PHP_EOL;

