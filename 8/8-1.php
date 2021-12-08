<?php

//define constants for the unique segment counts we're after
const SEGMENT_COUNT_1 = 2;
const SEGMENT_COUNT_4 = 4;
const SEGMENT_COUNT_7 = 3;
const SEGMENT_COUNT_8 = 7;

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

//create array of unique counts so we can check "in_array" in the loop later
$unique_segment_lengths = [SEGMENT_COUNT_1, SEGMENT_COUNT_4, SEGMENT_COUNT_7, SEGMENT_COUNT_8];

//loop through all outputs and check if their char count matches one of the unique ones
$unique_seg_digit_count = 0;
foreach ($outputs as $output_row) {
    foreach ($output_row as $single_output) {
        if(in_array(strlen($single_output), $unique_segment_lengths)) {
            $unique_seg_digit_count++;
        }
    }
}

echo "The digits 1, 4, 7 or 8 appeared $unique_seg_digit_count times" . PHP_EOL;