<?php

const INSTRUCTION_X = 'fold along x'; //not used as we handle this with an "else" block :)
const INSTRUCTION_Y = 'fold along y';

//read input and split it into coordinates for the dots and folding instructions
$trans_paper = [];
$instructions = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    if(trim($input_line) == "") continue; //skip the empty line between coordinates and instructions

    $coords = explode(',', trim($input_line)); //split into x,y coordinates

    //if there are less then two results after split, must be the instructions now, so explode by = to get axis and line to split
    if(count($coords) < 2) {
        $instruction = explode("=", trim($input_line));
        $instructions[] = [$instruction[0], $instruction[1]];
    }
    else {
        $trans_paper[$coords[1]][$coords[0]] = '#'; //add hash for nice display, to the given coordinates (y,x)
    }

}
fclose($input);

foreach ($instructions as $instr) {

    //check instruction whether to fold X or Y
    if($instr[0] == INSTRUCTION_Y) {
        $trans_paper = foldUp($trans_paper, $instr[1]);
    }
    else { //fold on X line
        $trans_paper = foldLeft($trans_paper, $instr[1]);
    }
}

//now that we have the end result, fill it up!
$trans_paper = fillEmptyFields($trans_paper);

//print the map result row by row
foreach ($trans_paper as $row) {
    echo implode("" ,$row) . PHP_EOL;
}


/**
 * Split multidimensional array into two by first key (the y line),
 * then "flip" the second array and "fold" (merge) it back into the first.
 *
 * @author Adrian
 * @date_created 2021-12-13
 *
 * @param array $map_array the multidimensional array to be folded together
 * @param int $line which line to fold the array on (this will usually be the "middle" of the array)
 *
 * @return mixed
 */
function foldUp($map_array, $line) {

    //split array into to along the line
    $array_1 = [];
    $array_2 = [];
    foreach ($map_array as $y => $row) {
        if($y > $line) {
            $array_2[$y] = $row;
        }
        else {
            $array_1[$y] = $row;
        }
    }

    //now decrease by instruction + 1 (as that's where this half starts) and "reverse" they keys in array, so we can merge with array_1
    $array_2_flipped = [];
    foreach ($array_2 as $y => $row) {
        $new_key = $y - ($line+1); //this basically "resets" the keys to 0 and upwards
        $new_key = ($line-1) - $new_key; //flip - cannot use array_reverse as we only hold the "true" keys, not the false ones in between
        $array_2_flipped[$new_key] = $row;
    }

    return mergeArrays($array_1, $array_2_flipped);
}

/**
 * Split multidimensional array into two by second key (the x line),
 * then "flip" the second array and "fold" (merge) it back into the first.
 *
 * @author Adrian
 * @date_created 2021-12-13
 *
 * @param array $map_array the multidimensional array to be folded together
 * @param int $line which line to fold the array on (this will usually be the "middle" of the array)
 *
 * @return mixed
 */
function foldLeft($map_array, $line) {

    //split array into to along the line
    $array_1 = [];
    $array_2 = [];
    foreach ($map_array as $y => $row) {
        foreach ($row as $key => $val) {
            if($key > $line) {
                $array_2[$y][$key] = $val;
            }
            else {
                $array_1[$y][$key] = $val;
            }
        }
    }

    //note below flip logic only works because we always fold exactly in half
    //now decrease by instruction + 1 (as that's where this half starts) and "reverse" they keys in array, so we can merge with array_1
    $array_2_flipped = [];
    foreach ($array_2 as $y => $row) {
        foreach ($row as $key => $val) {
            $new_key = $key - ($line+1); //this basically "resets" the keys to 0 and upwards
            $new_key = ($line-1) - $new_key; //flip - cannot use array_reverse as we only hold the "true" keys, not the false ones in between
            $array_2_flipped[$y][$new_key] = $val;
        }
    }

    return mergeArrays($array_1, $array_2_flipped);
}

/**
 * Loop through array2 and add all it's values to array 1.
 * We cannot use array_merge because we need to preserve keys and it's multidimensional.
 * For integer, array_merge does not preserve keys.
 * So instead we use the union operator on every row.
 *
 * @author Adrian
 * @date_created 2021-12-13
 *
 * @param array $array_1
 * @param array $array_2
 *
 * @return array combined array
 */
function mergeArrays($array_1, $array_2) {

    //loop through second array and set it on first (union operator if the row is already set)
    foreach ($array_2 as $key => $val) {
        $array_1[$key] = isset($array_1[$key]) ? $array_1[$key]+$val : $val;
    }

    return $array_1;
}

/**
 * Take a multidimensional array with a few values and fill in the gaps.
 *
 * @author Adrian
 * @date_created 2021-12-13
 *
 * @param array $arr multidimensional array to fill
 * @param string $filler the value to fill the gaps with
 *
 * @return array the filled up array
 */
function fillEmptyFields($arr, $filler = '.') {

    //find highest X and Y values to see how far we need to fill in both dirs
    $highest_y = max(array_keys($arr));
    $highest_x = 0;
    foreach ($arr as $row) {
        $highest_in_row = max(array_keys($row));
        if($highest_in_row > $highest_x) {
            $highest_x = $highest_in_row;
        }
    }

    //fill every row of the array with the "empty" filler
    $full_arr = [];
    for($i = 0; $i <= $highest_y ; $i++) { //<= as we start from 0
        $full_arr[$i] = array_fill(0 , $highest_x+1, $filler); //dot will be our "empty" fields (highest_x+1 as we start from 0)

        //if this row exists on the array of actual values, here is the time where we replace the "empty" fields of that row with actual fields
        if(isset($arr[$i])) {
            $full_arr[$i] = array_replace($full_arr[$i], $arr[$i]);
        }
    }

    //return the filled up array
    return $full_arr;
}