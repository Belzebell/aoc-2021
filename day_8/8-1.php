<?php

//define array constant for the unique segment counts we're after (count => resulting number)
const SEGMENT_COUNT = [2 => 1,
    4 => 4,
    3 => 7,
    7 => 8];

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

//loop through all outputs and check if their char count matches one of the unique ones
$unique_seg_digit_count = 0;
foreach ($outputs as $output_row) {
    foreach ($output_row as $single_output) {
        if(isset(SEGMENT_COUNT[strlen($single_output)])) {
            $unique_seg_digit_count++;
        }
    }
}

echo "The digits 1, 4, 7 or 8 appeared $unique_seg_digit_count times." . PHP_EOL;