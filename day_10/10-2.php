<?php

const CHUNK_MAP = [
    '(' => ')',
    '[' => ']',
    '{' => '}',
    '<' => '>'
];

const COMPLETION_SCORE_MAP = [
    ')' => 1,
    ']' => 2,
    '}' => 3,
    '>' => 4
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
$completion_scores = [];
foreach ($nav_subsystem as $subsystem_row) {

    $open_chunks = [];
    $corrupt = false;
    foreach ($subsystem_row as $chunk_symbol) {
        if(isset(CHUNK_MAP[$chunk_symbol])) {
            //must be an opening symbol, add to array
            $open_chunks[] = $chunk_symbol;
        }
        else {
            //must be a closing symbol, so get (and remove) the last opening symbol and check against it
            if(CHUNK_MAP[array_pop($open_chunks)] != $chunk_symbol) {
                //if no match, break - we do not care for syntax error lines
                $corrupt = true;
                break;
            }
        }
    }

    //if we get here, means the row is fine, just incomplete - so let's find what we need to complete it
    if(!$corrupt) {
        $completion_score = 0;
        //loop through the open chunk array BACKWARDS and add the needed closing symbol to the score
        foreach (array_reverse($open_chunks) as $open_chunk) {
            //note we do not actually preserve the closing symbols as all we need is the scoring
            //scoring rule: multiply by 5 and then add the score
            $completion_score *= 5;
            $completion_score += COMPLETION_SCORE_MAP[CHUNK_MAP[$open_chunk]];
        }

        //add the score of this row to the list of scores
        $completion_scores[] = $completion_score;
    }
}

//sort the scores, then get the middle one (floor, in case it's an uneven number, which according to the puzzle it will always be)
sort($completion_scores);
$middle_score = $completion_scores[floor(count($completion_scores)/2)];

//result
echo "The completion score is $middle_score." . PHP_EOL;