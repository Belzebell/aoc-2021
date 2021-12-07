<?php

$locations = [];

//read input and format it into an array
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $locations = explode(',', trim($input_line));
}
fclose($input);

//sort array and get the median
sort($locations);
$length = count($locations);
$median_pos = $length % 2 == 0 ? $length/2 : ($length+1)/2; //if length not even, add plus one before getting half
$median = $locations[$median_pos];

//figure out fuel cost to move every submarine to the median
$fuel_cost = 0;
foreach ($locations as $location) {
    $fuel_cost += abs($location - $median); //absolute result is difference between location and median
}


//log how much fuel was used
echo $fuel_cost . ' fuel used.' . PHP_EOL;