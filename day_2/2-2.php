<?php

//read input into array
$array = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $array[] = trim($input_line);
}
fclose($input);

//initialise x and y axis and aim at 0 
$horizontal = 0;
$depth = 0;
$aim = 0;

foreach( $array as $value ){
    $movement = explode(' ', $value); //split the string by space, giving us direction and count as different array values
    
    switch($movement[0]) {
        case 'forward':
            $horizontal += $movement[1];
            $depth += $aim * $movement[1];
            break;
        case 'down':
            $aim += $movement[1];
            break;
        case 'up':
            $aim -= $movement[1];
            break;
    }
}

//end result should be multiplication of both
echo "The final horizontal position multiplied by depth is " . $horizontal * $depth . "." . PHP_EOL;

