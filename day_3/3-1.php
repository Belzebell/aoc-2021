<?php

//read input into array
$array = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $array[] = trim($input_line);
}
fclose($input);

//we need an array to count the occurences of 0 per position in the byte
//below, I dynamically set the counter array to as many fields as there are characters in the first byte (this assumes all bytes have the same length)
//we could alternatively just initalise it like $zero_counts = [0,0,0,0,0,0,0,0,0,0,0,0];
$i = 0;
$zero_counts = []; 
while ($i < strlen($array[0])) {
    $zero_counts[$i++] = 0; //set for $i, then increase $i
}

//now for every position we need to count 0 bits
foreach ($array as $byte) {
    foreach(str_split($byte) as $i => $bit) { //split string to array and loop through
        if($bit == '0') {
            $zero_counts[$i]++; //for a zero, increase the count of that position
        }
    }
}

$resulting_byte = ''; //start with empty string so we can append 0s and 1s
$total_numbers = count($array); //take the total to compare the 0 count again
foreach($zero_counts as $zero_count) {
    $one_count = $total_numbers - $zero_count; //count of 1s is the total - the count of 0s we have
    
    if($zero_count > $one_count) { //more zeroes than ones
        $resulting_byte .= 0;
    }
    else { //more ones than zeroes
        $resulting_byte .= 1;
    }
}

//get inverted byte by replacing as string
$inverted_byte = str_replace(0, 2, $resulting_byte); //make 0s into 2s to get them out of the way
$inverted_byte = str_replace(1, 0, $inverted_byte); //make 1s into 0s
$inverted_byte = str_replace(2, 1, $inverted_byte); //now make 2s (old 0s) into 1s

//convert to decimal and multiply
echo "The power consumption of the submarine is " . bindec($resulting_byte) * bindec($inverted_byte) . "." . PHP_EOL;