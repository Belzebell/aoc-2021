<?php

$array = [
    '00100',
'11110',
'10110',
'10111',
'10101',
'01111',
'00111',
'11100',
'10000',
'11001',
'00010',
'01010'];
              

$total_numbers = count($array); //take the total to compare the 0 count again
$zero_counts = [0,0,0,0,0];

//now for every digit we need to count 0 bits and see if they are more than the total minus their count
foreach ($array as $bit_number) {
    foreach(str_split($bit_number) as $i => $bit) {
        if($bit == '0') {
            $zero_counts[$i]++;
        }
    }
}

$resulting_bit = '';
foreach($zero_counts as $zero_count) {
    $one_count = $total_numbers - $zero_count; //one count is the total - the zeroes
    
    if($zero_count > $one_count) { //more zeroes than ones
        $resulting_bit .= 0;
    }
    else { //more ones than zeroes
        $resulting_bit .= 1;
    }
}

echo $resulting_bit;




