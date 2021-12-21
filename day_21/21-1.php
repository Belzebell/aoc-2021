<?php

//define constants for some fixed values
const WIN_SCORE = 1000;
const MAX_DIE = 100;
const MAX_FIELD = 10;

//read input (just need number after last space of each line for the actual position)
$player_pos = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $line = trim($input_line);

    $split_str = explode(' ', $line);
    $player_pos[] = array_pop($split_str);
}
fclose($input);

//initialise player scores
$player_scores = [];
foreach ($player_pos as $no => $pos) {
    $player_scores[$no] = 0;
}

//count die rolls and increase the die value every roll
$die_rolls = 0;
$die_val = 1;
while (max($player_scores) < WIN_SCORE) {
    foreach ($player_pos as $no => $pos) {

        //increase move with 3 rolls, if the die reaches it max it resets to 1
        $move = 0;
        for($i = 0; $i < 3; $i++) {
            if($die_val > MAX_DIE) {
                $die_val = 1;
            }
            $move += $die_val++;
        }

        //add the 3 rolls to the counter and move the player
        $die_rolls += 3;
        $new_pos = $pos + $move;

        //decrement new position to 10 or below
        while($new_pos > MAX_FIELD) {
            $new_pos -= MAX_FIELD;
        }

        //update player position & score
        $player_pos[$no] = $new_pos;
        $player_scores[$no] += $new_pos;

        //game ends immmediately when a player wins, do not do next player's turn
        if($player_scores[$no] >= WIN_SCORE) {
            break;
        }
    }
}

//result
$losing_score = min($player_scores);
$result = $losing_score * $die_rolls;

echo "Losing player's score times dice rolls equals $result." . PHP_EOL;

