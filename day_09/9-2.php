<?php

/**
 * constants for the four directions we will need to recursively check
 */
const X_RIGHT = 0;
const X_LEFT = 1;
const Y_UP = 2;
const Y_DOWN = 3;

//read input and format it into multidimensional array as x and y coordinates
$heightmap = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = str_split(trim($input_line)); //split numbers into array (these are our x coordinates)
    $heightmap[] = $split_line; //add array to heightmap array (the row count is our y coordinate)
}
fclose($input);

//loop through the heightmap and check every value for a low point, when found, check the basin size from there
$basins = [];
foreach ($heightmap as $y_axis => $y_val) {
    foreach ($y_val as $x_axis => $x_val) {

        //if no previous condition has set this to false, it must be a low point
        if (isLowest($heightmap, $x_axis, $y_axis)) {
            //from the low point, check (recursively) in all 4 directions
            $map = $heightmap; //create a "copy" of the map, as the recursion modifies this
            $location_count = 1;
            foreach ([X_RIGHT, X_LEFT, Y_UP, Y_DOWN] as $dir) {
                $location_count += getNextHeight($map, $x_axis, $y_axis, $dir);
            }
            $basins[] = $location_count;
        }
    }
}

//sort the basin array by size, then multipy the first (largest) 3
rsort($basins);
$result = $basins[0] * $basins[1] * $basins[2];

//result
echo "The three largest basins reach a total of $result." . PHP_EOL;


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

/**
 * Recursive function.
 * Modifies the given axes depending on direction.
 * If the resulting location on a map is not 9, makes it a 9, increases count by 1 and recursively continues these checks in all directions.
 *
 * @author Adrian
 * @date_created 2021-12-09
 *
 * @param array $map this should be a copy of the original map, as it gets referenced and modified to make the checks work
 * @param int $x_loc x location from previous check to modify
 * @param int $y_loc y location from previous check to modify
 * @param int $direction X_RIGHT|X_LEFT|Y_UP|Y_DOWN
 *
 * @return int
 */
function getNextHeight(&$map, $x_loc, $y_loc, $direction)
{
    $map[$y_loc][$x_loc] = 9; //set the "previous" location to 9 so it won't get counted twice

    //depending on direction, modify the corresponding axis
    switch ($direction) {
        case X_LEFT:
            $x_loc--;
            break;
        case X_RIGHT:
            $x_loc++;
            break;
        case Y_UP:
            $y_loc--;
            break;
        case Y_DOWN:
            $y_loc++;
            break;
        default:
            return 0;
    }

    //get new height - if not set, same as 9
    $height = $map[$y_loc][$x_loc] ?? 9;

    //stop at 9 (return 0 to not add more to the count total)
    if ($height == 9) {
        return 0;
    }

    //if we did not stop, set count to 1 and get check in all 4 dirs again
    //note, this step could be optimised, e.g. we never need to check the dir we are coming from, it will always be 9
    $location_count = 1;
    foreach ([X_RIGHT, X_LEFT, Y_UP, Y_DOWN] as $dir) {
        $location_count += getNextHeight($map, $x_loc, $y_loc, $dir);
    }

    return $location_count;
}