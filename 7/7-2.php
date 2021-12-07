<?php

$locations = [];

//read input and format it into an array
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $locations = explode(',', trim($input_line));
}
fclose($input);

//get the average of all locations (rounded to full number)
$total = 0;
foreach ($locations as $location) {
    $total += $location;
}

//during testing I found there's scenarios where we need to floor despite rounding resulting in a ceil
//so instead we do both and compare of the two which fuel cost is lower
$averages[] = floor($total / count($locations));
$averages[] = ceil($total / count($locations));

//in the rare occasion where we might have a whole number be the average, we don't need to loop twice for fuel cost
if($averages[0] == $averages[1]) {
    unset($averages[1]);
}

//figure out fuel cost to move every submarine to the average (twice, for the two averages)
$fuel_cost = [];
foreach ($averages as $key => $average) {
    $fuel_cost[$key] = 0;
    foreach ($locations as $location) {
        $diff = abs($location - $average) . PHP_EOL;
        $fuel_cost[$key] += array_sum(range(1, $diff)); //add sum of range, as every step costs 1 more than the previous
    }
}


//log how much fuel was used (use the lower of the fuel costs we have)
echo min($fuel_cost) . ' fuel used.' . PHP_EOL;