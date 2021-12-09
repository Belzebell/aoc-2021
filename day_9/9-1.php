<?php

//read input and format it into multidimensional array as x and y coordinates
$heightmap = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = str_split(trim($input_line)); //split numbers into array (these are our x coordinates)
    $heightmap[] = $split_line; //add array to heightmap array (the row count is our y coordinate)
}
fclose($input);

//loop through the heightmap and check every value against values right and left and up and down (x or y, +1 or -1, for each)
$total_risk = 0;
foreach ($heightmap as $y_axis => $height_row) {
    foreach ($height_row as $x_axis => $height) {
        $lowest = true;
        if(isset($height_row[$x_axis+1]) && $height >= $height_row[$x_axis+1]) { //check to the right
            $lowest = false;
            continue;
        }
        if(isset($height_row[$x_axis-1]) && $height >= $height_row[$x_axis-1]) { //check to the left
            $lowest = false;
            continue;
        }
        if(isset($heightmap[$y_axis+1][$x_axis]) && $height >= $heightmap[$y_axis+1][$x_axis]) { //check up
            $lowest = false;
            continue;
        }
        if(isset($heightmap[$y_axis-1][$x_axis]) && $height >= $heightmap[$y_axis-1][$x_axis]) { //check down
            $lowest = false;
            continue;
        }

        //if no previous condition has set this to false, it must be a low point
        if($lowest) {
            $total_risk += ($height + 1); //risk level of a low point is the point + 1
        }
    }
}

//result
echo "The total risk level of all low points is $total_risk." . PHP_EOL;