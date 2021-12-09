<?php

const NEW_FISH_AGE = 8;
const BIRTH_LENGTH = 6;

if(!isset($days_to_monitor)) {
    $days_to_monitor = 80;
}

$fish_input = [];

//read input and format it into an array
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $fish_input = explode(',', trim($input_line));
}
fclose($input);

//fill the count for all days with 0 to initialise, then loop through the input to do initial count
$fish_group = array_fill(0, NEW_FISH_AGE + 1, 0);
foreach ($fish_input as $fish) {
    $fish_group[$fish]++;
}

$i = 0;
while ($i < $days_to_monitor) {
    $i++;
    $birth_counter = 0;
    foreach ($fish_group as $key => $fish) {
        if ($key == 0) { //fish on 0 need to be added to 6 as well as 8 (for new births)
            $birth_counter = $fish_group[0];
        }

        if ($key == NEW_FISH_AGE) { //this is the last loop, we cannot get it from day above
            break;
        }

        $fish_group[$key] = $fish_group[$key + 1]; //set this day to the day above (to "decrease" the day)
    }

    //now that we're done with this loop, increase day 6 by the fish that had been day 0 before, and add the same amount as new fish for day 8
    $fish_group[BIRTH_LENGTH] += $birth_counter;
    $fish_group[NEW_FISH_AGE] = $birth_counter;
}

//get the sum of all fish in the array
echo array_sum($fish_group) . PHP_EOL;