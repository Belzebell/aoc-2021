<?php

/**
 * array constant for the unique segment counts we're after (count => resulting number)
 */
const SEGMENT_COUNT = [2 => 1,
 4 => 4,
 3 => 7,
 7 => 8];

//
/**
 * map for comparison, (split into segment lengths (5 and 6) if I compare number x with number y, how many fields should I have left
 * i.e if my length is 5 and I remove all "9" segments from the "3", I should only have 1 segment left -> it's number 2!
 * logically in this map I needed to make sure there's no "loop" where a number can only be found by comparing to another that can only be found from this one
 */
const COMPARISON_MAP = [
    5 => [ //strlen
        2 => [9, 1], //result => [input, remaining_segments]
        3 => [1, 3],
        5 => [2, 2]
    ],
    6 => [
        0 => [5, 2],
        6 => [7, 4],
        9 => [4, 2]
    ],
];

//read input and format it into arrays (part before pipe are the signal patterns, part after are the output values
$patterns = [];
$outputs = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = explode('|', trim($input_line));
    $patterns[] = explode(' ', trim($split_line[0]));
    $outputs[] = explode(' ', trim($split_line[1]));
}
fclose($input);

//we will solve this by comparing the numbers we now, rather than creating a cipher map to decrypt by that
//if it were needed, we could comfortably create the cipher map from all numbers afterwards

//loop through all patterns, every pattern contains all numbers, so we use this to figure out the mapping
$result = 0;
foreach ($patterns as $line_no => $pattern_row) {
    $number_map = createNumberMap($pattern_row);

    //loop through the outputs belonging to that pattern and read the related number from the map
    $decrypted_output = "";
    foreach ($outputs[$line_no] as $output) {
        //bless PHP for string manipulation with integers :) (put the output digits together to get the output number
        $decrypted_output .= $number_map[sortString($output)];
    }

    //now just add it to our result
    $result += $decrypted_output;
}

echo "The result of all output values is $result." . PHP_EOL;


/**
 * Loop through all encrypted numbers until we have identified them all.
 * Returns a map where keys are the encrypted numbers and values are the decrypted numbers.
 *
 * @author Adrian
 * @date_created 2021-12-08
 *
 * @param $encrypted_numbers
 *
 * @return array
 */
function createNumberMap($encrypted_numbers) {

    //first, we'll find the numbers we can identify based on length and add them to the map
    $number_map = [];
    foreach ($encrypted_numbers as $key => $enc_number) {
        $length = strlen($enc_number);
        if(isset(SEGMENT_COUNT[$length])) {
           $number_map[SEGMENT_COUNT[$length]] = sortString($enc_number);
           unset($encrypted_numbers[$key]); //remove from the "unknown" numbers
        }
    }

    //keep looping through the comparison until we have the map finished (by having figured out every encrypted number)
    while(!empty($encrypted_numbers)) {
        foreach ($encrypted_numbers as $key => $enc_number) {
            if(decryptNumber($enc_number, $number_map)) {
                unset($encrypted_numbers[$key]);
            }
        }
    }

    return array_flip($number_map); //flip it, so we can get our results by key lookup
}

/**
 * Accepts an encrypted number and an incomplete map.
 * Loops through the comparison map, and for any number that is not yet set on the number map,
 * where we have the related number needed for comparison, it runs comparison and checks if the result matches.
 * If it does - we have now decrypted a new number. Add it to the map to make it a little less incomplete.
 *
 * @author Adrian
 * @date_created 2021-12-08
 *
 * @param $enc_number
 * @param $number_map
 *
 * @return bool true if we found a new number, else false
 */
function decryptNumber($enc_number, &$number_map) {
    foreach (COMPARISON_MAP[strlen($enc_number)] as $result_number => $diff_map) {
        if(isset($number_map[$result_number])) {
            continue; //we already have this number, no need to compare
        }
        elseif(!isset($number_map[$result_number]) && isset($number_map[$diff_map[0]])) { //we have the number we need to identify the result!
            $remaining_segments = str_replace(str_split($number_map[$diff_map[0]]), [], $enc_number); //remove all characters from input from my encrypted number so we only have the others left
            if (strlen($remaining_segments) == $diff_map[1]) {
                //match! sort, add to map, and remove from encrypted numbers
                $number_map[$result_number] = sortString($enc_number);
                return true; //no need to keep looping through the map for this number in particular as we just figured it out
            }
        }
    }

    return false; //we could not decrypt the number with the existing number_map
}


/**
 * Quick function to sort string alphabetically.
 * Explode to array, sort it, implode back to string.
 * We need these numbers sorted as the output numbers can have different sorting from input.
 *
 * @author Adrian
 * @date_created 2021-12-08
 *
 * @param $string
 *
 * @return string
 */
function sortString($string) {
    $string_arr = str_split($string);
    sort($string_arr, SORT_STRING);
    return implode("", $string_arr);
}