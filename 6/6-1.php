<?php

$fish_group = [];
const NEW_FISH_AGE = 8;
const BIRTH_LENGTH = 6;
const DAYS_TO_MONITOR = 80;

//read input and format it into the set of numbers and the bingo boards
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $fish_group = explode(',', trim($input_line));
}
fclose($input);

$i = 0;
while($i < DAYS_TO_MONITOR) {
    $i++;
    $new_fish_counter = 0; //reset counter of births this loop or day
    foreach ($fish_group as $key => $fish) {
        $fish_group[$key]--; //decrease time until birth

        if ($fish_group[$key] < 0) { //when we hit -1, reset to 6 and add a new fish
            $fish_group[$key] = BIRTH_LENGTH;
            $new_fish_counter++;
        }
    }

    //now add as many fish as counted from that loop (doing it afterwards like this, as we don't wanna decrease or loop them immediately)
    $fish_group = array_merge($fish_group, array_fill(count($fish_group), $new_fish_counter, NEW_FISH_AGE));
}

//count the size of the array
echo count($fish_group) . PHP_EOL;