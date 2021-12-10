<?php

const CHUNK_MAP = [
    '(' => ')',
    '[' => ']',
    '{' => '}',
    '<' => '>'
];

const ERROR_SCORE_MAP = [
    ')' => 3,
    ']' => 57,
    '}' => 1197,
    '>' => 25137
];

//read input and format it into multidimensional array as x and y coordinates
$nav_subsystem = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = str_split(trim($input_line)); //split the chunk symbols into array (so we can easily check them one by one)
    $nav_subsystem[] = $split_line; //add array to subsystem so we keep our rows separate
}
fclose($input);

//loop through every row of the subsystem, add open chunks to an array, check closing chunks against the last open chunk
$error_score = 0;
foreach ($nav_subsystem as $subsystem_row) {

    $open_chunks = [];
    foreach ($subsystem_row as $chunk_symbol) {
        if(isset(CHUNK_MAP[$chunk_symbol])) {
            //must be an opening symbol, add to array
            $open_chunks[] = $chunk_symbol;
        }
        else {
            //must be a closing symbol, so get (and remove) the last opening symbol and check against it
            if(CHUNK_MAP[array_pop($open_chunks)] != $chunk_symbol) {
                //if no match, add to error_score and break out of the loop for this row (no need to keep checking the rest)
                $error_score += ERROR_SCORE_MAP[$chunk_symbol];
                break;
            }
        }
    }
}

//result
echo "The syntax error score is $error_score." . PHP_EOL;