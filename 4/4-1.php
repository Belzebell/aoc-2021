<?php

$number_set = [];
$bingo_boards = [];
$board_count = 0;

//read input and format it into the set of numbers and the bingo boards
$input = fopen("input.txt", "r");
while (($line = fgets($input)) !== false) {
    if(empty($number_set)) { //must be first line
        $number_set = explode(',', $line);
    }
    else { //part of the bingo boards
        if($line == PHP_EOL) { //means the next board is coming up
            $row_count = 0;
            $board_count++;
        }
        else {
            //remove leading spaces, and make double spaces to single, so we can explode by space
            $clean_line = trim(str_replace('  ', ' ', $line));
            $bingo_boards[$board_count][$row_count] = explode(' ', $clean_line);
            $row_count++;
        }
    }
}
fclose($input);

$winner = false;
foreach ($number_set as $called_number) {
    foreach ($bingo_boards as &$board) {
        //mark the number on the board and check if it won
        $winner = markBoard($board, $called_number);

        if ($winner) {
            echo $called_number * sumOfBoard($board); //multiply for puzzle answer
            exit; //we're done!
        }
    }
}

function markBoard(&$board, $number) {
    foreach ($board as $key => $row) {
        //find number and remove it
        $board[$key] = array_filter($row, function($value) use($number){
            return $value != $number; //only keep values that are NOT that number
        });

        //if the sum of a row or column is 0, means all numbers are marked & this board won!
        if(array_sum($board[$key]) == 0) {
            return true; //winner by row!
        }
    }

    //after we've replaced all numbers of the board, check the columns (we already checked rows when going per row)
    $i = 0;
    while($i < 5) { //loop through columns up to  5 (cannot use count($row) as some rows may have gotten shorter)
       $column = array_column($board, $i++);
        if(array_sum($column) == 0) {
            return true; //winner by column!
        }
    }

    //this board did not win on that number call
    return false;
}

function sumOfBoard($board) {
    //find number in all boards
    $sum = 0;
    foreach ($board as $row) {
        $sum += array_sum($row);
    }

    return $sum;
}