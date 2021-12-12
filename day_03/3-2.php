<?php

//read input into array
$array = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $array[] = trim($input_line);
}
fclose($input);

__main($array);

function __main($array)
{
    //initalise arrays and values for both oxygen and co2
    $bytelength = strlen($array[0]);
    $offset = $oxygen_rating = $co2_rating = 0;
    $oxygen_array = $co2_array = $array;

    //now for every position we need to count 0 bits
    while ($offset < $bytelength && (!$co2_rating || !$oxygen_rating)) {

        if ($oxygen_rating == 0) { //if we haven't figured out oxygen rating yet, keep checking the next position
            list($oxy_zero_on_pos, $oxy_one_on_pos) = splitBitIntoOnesAndZeroes($oxygen_array, $offset);
            $oxygen_array = compareArraySize($oxy_zero_on_pos, $oxy_one_on_pos);
        }

        if ($co2_rating == 0) { //if we haven't figured out co2 rating yet, keep checking the next position
            list($co2_zero_on_pos, $co2_one_on_pos) = splitBitIntoOnesAndZeroes($co2_array, $offset);
            $co2_array = compareArraySize($co2_zero_on_pos, $co2_one_on_pos, true);
        }

        //if there's only 1 value left in the array, that's our rating!
        if (count($oxygen_array) == 1 && $oxygen_rating == 0) {
            $oxygen_rating = reset($oxygen_array);
        }
        if (count($co2_array) == 1 && $co2_rating == 0) {
            $co2_rating = reset($co2_array);
        }

        $offset++; //increase here so we check the next position on the next loop
    }

    //result!
    echo "The life support rating of the submarine is " . bindec($oxygen_rating) * bindec($co2_rating) . "." . PHP_EOL;
}


/**
 * Splits an array of bytes into two arrays based on bit (1 on $position or 0 on $position),
 * returns the two resulting arrays, as well as the counters of how many were added to each
 *
 * @author Adrian
 * @date_created 2021-12-03
 *
 * @param array $array the array of bytes to be split
 * @param int $position which position of each byte to check
 *
 * @return array first array returned contains all bytes with a 0 on that position, second array all bytes with a 1 on that position
 */
function splitBitIntoOnesAndZeroes($array, $position) {
    $zero_on_pos = $one_on_pos = [];
    foreach ($array as $byte) {
        if(substr($byte, $position, 1) == 0) { //if the bit on that position is a 0, add it to the respective array
            $zero_on_pos[] = $byte;
        }
        else { //else it's a 1, so add it to the other array
            $one_on_pos[] = $byte;
        }
    }
    return [$zero_on_pos, $one_on_pos];
}

/**
 * Compares two arrays based on their counts
 *
 * @author Adrian
 * @date_created 2021-12-03
 *
 * @param array $first first array for comparison
 * @param array $second second array for comparison
 * @param bool $find_lesser invert comparison, by default returns the bigger array
 *
 * @return array
 */
function compareArraySize($first, $second, $find_lesser = false) {
    if (count($first) > count($second)) {
        return $find_lesser ? $second : $first;
    }
    else {
        return $find_lesser ? $first : $second;
    }
}



