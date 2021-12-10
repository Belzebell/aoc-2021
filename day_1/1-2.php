<?php

//read input into array
$array = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $array[] = trim($input_line);
}
fclose($input);

//first, let's create the list of 3-piece sets              
$end = count($array)-1;
$sets = [];
foreach($array as $key => $value){
        if($key < $end-1) //do not set last key (from previous or last) (as we wont get 3 added to this anymore)
            $sets[$key+1] = $array[$key];
        
        if($key != 0 && $key != $end) //do not set first or last key (as we wont get 3 items added to these)
            $sets[$key] += $value;
        
        if(isset($sets[$key-1])) { //as the very first one won't exist
            $sets[$key-1] += $array[$key];
        }
}

//then apply the solution from puzzle 1-1
$increases = 0;
$prev = reset($sets); //get the first value
foreach($sets as $value){
    //if new value greater than previous, we have an increase! else do nothing.
    if($value > $prev) {
        $increases++;
    }
    $prev = $value; //afterwards, set new value as the previous one
}

echo "$increases measurement sums are larger than the previous." . PHP_EOL;