<?php

$lines = [];

//read input and format it into the set of numbers and the bingo boards
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $lines[] = trim($input_line);
}
fclose($input);


$line_map = []; //multidimensional array with every point where lines are detected (first key is y, second x, as y is vertical)
foreach ($lines as $line) {
    $line = str_replace(' -> ', ',', $line); //replace the arrow with another comma so we can explode into 4 coordinates easily
    list($x1, $y1, $x2, $y2) = explode(',', $line); //first two are start, second two are end

    //first, only consider horizontal or vertical lines (x1 == x2 or y1 == y2)
    if($x1 == $x2 || $y1 == $y2) {
        if($y1 == $y2) { //if y stays the same, mark the range of all x
            foreach (range($x1, $x2) as $x_coordinate) {
                $line_map = markMap($line_map, $x_coordinate, $y1);
            }
        }
        else { //if x stays the same, mark the range of all y
            foreach (range($y1, $y2) as $y_coordinate) {
                $line_map = markMap($line_map, $x1, $y_coordinate);
            }
        }
    }
    else { //we have a diagonal line
        //luckily they are always 45 degrees, so absolute difference between x1 and x2 is same as between y1 and y2
        $diff = abs($x1-$x2);
        while($diff != 0) {
            $line_map = markMap($line_map, $x1, $y1);

            $diff = abs($x1-$x2); //check the diff before modifying values, else we will miss the last loop when the diff is 0
            ($x1 - $x2) < 0 ? $x1++ : $x1--; //if difference is negative, x1 is smaller, so increase, else, decrease
            ($y1 - $y2) < 0 ? $y1++ : $y1--; //if difference is negative, y1 is smaller, so increase, else, decrease
        }
    }
}

//now we need to find every value in the array that is higher than 1
$line_count = 0;
foreach ($line_map as $row) {
    foreach ($row as $single_pos) {
        if($single_pos > 1) {
            $line_count++; //if a value is greater than one increase the counter
        }
    }
}
echo $line_count . PHP_EOL; //done!

function markMap($map, $x, $y) {
    if(!isset($map[$y])){
        $map[$y] = []; //check if we already have an x row for this y coordinate
    }
    if(!isset($map[$y][$x])){
        $map[$y][$x] = 1; //if no entry for this y/x position existed, initalise with 1 as it's the first occurrence
    }
    else {
        $map[$y][$x]++; //otherwise increase the count
    }

    return $map;
}