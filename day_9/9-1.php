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
foreach ($heightmap as $y_axis => $y_val) {
    foreach ($y_val as $x_axis => $height) {

        //if no previous condition has set this to false, it must be a low point
        if (isLowest($heightmap, $x_axis, $y_axis)) {
            $total_risk += ($height + 1); //risk level of a low point is the point + 1
        }
    }
}

//result
echo "The total risk level of all low points is $total_risk." . PHP_EOL;


/**
 * Get the value of a given point on a map, then compare it in all 4 directions to check if it's the lowest
 *
 * @author Adrian
 * @date_created 2021-12-09
 *
 * @param array $map multidimensional array reflecting a map with x and y values
 * @param int $x_axis x location to check on array
 * @param int $y_axis y location to check on array
 *
 * @return bool true if lower than all neighbours
 */
function isLowest($map, $x_axis, $y_axis) {
    $height = $map[$y_axis][$x_axis];
    if (isset($map[$y_axis][$x_axis + 1]) && $height >= $map[$y_axis][$x_axis + 1]) { //check to the right
        return false;
    }
    if (isset($map[$y_axis][$x_axis - 1]) && $height >= $map[$y_axis][$x_axis - 1]) { //check to the left
        return false;
    }
    if (isset($map[$y_axis + 1][$x_axis]) && $height >= $map[$y_axis + 1][$x_axis]) { //check up
        return false;
    }
    if (isset($map[$y_axis - 1][$x_axis]) && $height >= $map[$y_axis - 1][$x_axis]) { //check down
        return false;
    }

    //if no previous condition has returned false, it must be a low point
    return true;
}