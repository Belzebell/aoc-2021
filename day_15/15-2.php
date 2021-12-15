<?php

//new array size, of which we have 1 tile
const NEW_SIZE_X = 5;
const NEW_SIZE_Y = 5;

//read input and format it into multidimensional of risk levels
$risk_tile = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = str_split(trim($input_line)); //split numbers into array
    $risk_tile[] = $split_line; //add array to risk map array
}
fclose($input);

//transform risk map to a 5x5 size of original, with increased values
$risk_map = $risk_tile;

//get current sizes as we can skip them and this is how far we need to jump to add 1
$current_size_x = count(reset($risk_tile));
$current_size_y = count($risk_tile);

//expand in dir X
for($i = $current_size_x; $i < NEW_SIZE_X * $current_size_x; $i++) {
    foreach ($risk_map as &$risk_row) {
        //get new value from same row five values before and add 1
        $new_val = $risk_row[$i-$current_size_x] + 1;
        $risk_row[$i] = $new_val > 9 ? 1 : $new_val; //if greater 9, set it to 1
    }
}

//expand in dir Y
for($i = $current_size_y; $i < NEW_SIZE_Y * $current_size_y; $i++) {
    //get new value from the same key five rows above and add 1
    foreach ($risk_map[$i-$current_size_x] as $key => $value) {
        $risk_map[$i][$key] = ($value + 1) > 9 ? 1 : ($value + 1); //if greater 9, set it to 1
    }
}

include('15-1.php');
