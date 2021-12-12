<?php

//SAME CODE AS PART 1 solution - just instead of looping a fixed x times, we just loop until the flash count of a single loop is exactly 100 (the count of octopuses)

const MAX_ENERGY = 9; //if charge is higher than this, the octopus flashes

//read input and format it into multidimensional of octopuses (10 by 10)
$octopus_group = [];
$octopus_count = 0; //with the given 10x10 grids this will always be 100 - but checking it during input makes the code dynamic for different size grids
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = str_split(trim($input_line)); //split numbers into array
    $octopus_group[] = $split_line; //add array to octopus_group array
    $octopus_count += count($split_line);
}
fclose($input);

$steps = 0;
$flash_count = 0;
//loop until the flash count of the last loop was exactly same as octopus count
while ($flash_count != $octopus_count) {
    $steps++; //increase steps
    $flash_list = [];

    //first, increase all octopuse values by 1
    increaseOctopusEnergy($octopus_group);

    //now recursively check for flashes
    findFlashes($octopus_group);

    //lastly, set the flashed octopuses to zero (this is a good time to count how many octopuses flashed, so we pass the flash list to see WHICH flashed)
    $flash_count = resetFlashedOctopuses($octopus_group, $flash_list);
}

//result
echo "At " . $steps . " steps, all octopuses synchronise." . PHP_EOL;


/**
 * Loop through the multidimensional array and increase the energy level of every octopus by 1
 *
 * @author Adrian
 * @date_created 2021-12-12
 *
 * @param array $group the multidimensional array of octopuses
 *
 * @return array
 */
function increaseOctopusEnergy(&$group) {
    foreach ($group as $k_row => $row) {
        foreach ($row as $k_octo => $octopus) {
            $group[$k_row][$k_octo]++;
        }
    }

    return $group;
}

/**
 * Loop through the multidimensional array and trigger a flash if energy level reached MAX.
 * This calls a recursive function that checks followup-flashes from surrounding octopuses.
 *
 * @author Adrian
 * @date_created 2021-12-12
 *
 * @param array $group the multidimensional array of octopuses
 *
 * @return array
 */
function findFlashes(&$group) {
    foreach ($group as $k_row => $row) {
        foreach ($row as $k_octo => $octopus) {
            //access the octopus from array instead of $octopus, as the referenced array may have been modified in the recursive function - this does not reflect on $row in this loop yet
            if($group[$k_row][$k_octo] > MAX_ENERGY) {
                triggerFlash($group, $k_octo, $k_row);
            }
        }
    }

    return $group;
}

/**
 * Loop through the multidimensional array and reset every "null" value back to 0 so it is a natural number again
 *
 * @author Adrian
 * @date_created 2021-12-12
 *
 * @param array $group the multidimensional array of octopuses
 *
 * @return int how many octopuses where reset (read: how many octopuses flashed)
 */
function resetFlashedOctopuses(&$group, &$flash_list) {
    $reset_count = 0;
    foreach ($group as $k_row => $row) {
        foreach ($row as $k_octo => $octopus) {
            if($octopus == null) {
                //reset the energy to 0 and increase the reset (flash) counter
                $group[$k_row][$k_octo] = 0;
                $reset_count++;
            }
        }
    }

    return $reset_count;
}

/**
 * Loop through the multidimensional array and trigger a flash if energy level reached MAX.
 * This calls a recursive function that checks followup-flashes from surrounding octopuses.
 *
 * @author Adrian
 * @date_created 2021-12-12
 *
 * @param array $group the multidimensional array of octopuses
 * @param int $x_loc array location of this octopus on the "inner" (second) array
 * @param int $y_loc array location of this octopus on the "outer" (first) array
 *
 */
function triggerFlash(&$group, $x_loc, $y_loc) {

    //set this octopus to "null" instead of a number, as an octopus flash cannot be triggered twice in the same step, so we do NOT want to increase more after a flash
    $group[$y_loc][$x_loc] = null;

    //array of all locations the flash would affect/increase (array of array as some $x keys are the same and would overwrite)
    $locations = [
        [$x_loc-1, $y_loc-1], //top left
        [$x_loc, $y_loc-1], //top middle
        [$x_loc+1, $y_loc-1], //top right
        [$x_loc-1, $y_loc], //left
        [$x_loc+1, $y_loc], //right
        [$x_loc-1, $y_loc+1], //bottom left
        [$x_loc, $y_loc+1], //bottom
        [$x_loc+1, $y_loc+1], //bottom right
    ];

    //only increase surrounding values if isset - this not only skips outer bounds, but also the octopuses that already flashed this step, as they are now "null"
    foreach ($locations as list($x, $y)) {
        if(isset($group[$y][$x])) {
            $group[$y][$x]++;
            //if the increase surpasses max energy, trigger a new flash from this octopus
            if($group[$y][$x] > MAX_ENERGY) {
                triggerFlash($group, $x, $y);
            }
        }
    }
}