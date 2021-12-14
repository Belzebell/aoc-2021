<?php

//how many times to apply the rules to the template
if(!isset($steps_to_run)) {
    $steps_to_run = 10;
}

//read input and split it into the initial templates and the polymer rules
$template = "";
$rules = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    if(trim($input_line) == "") continue; //skip the empty line between template and rules

    $rule_split = explode('->', trim($input_line)); //split up the rules

    //if there are less then two results after split, must be the template now, so set the whole input line
    if(count($rule_split) < 2) {
        $template = trim($input_line);
    }
    else {
        $rules[trim($rule_split[0])] = trim($rule_split[1]); //add rule to associative array for easy key lookup
    }

}
fclose($input);

//convert the $template into a pair count
$template_pairs = [];
$template_pieces = str_split($template);
foreach ($template_pieces as $key => $poly) {
    if (!isset($template_pieces[$key + 1])) {
        break;//for the last key we cannot make another pair so stop here
    }

    //get the pair
    $lookup_pair = $poly . $template_pieces[$key+1];

    //if there is no count for this pair, set it to 1
    if(!isset($template_pairs[$lookup_pair])) {
        $template_pairs[$lookup_pair] = 1;
    }
    else {
        $template_pairs[$lookup_pair]++; //else increase count
    }
}

//apply rules until we ran $steps_to_run times
$i = 0;
while ($i < $steps_to_run) {
    $template_pairs = applyRulesToPairs($template_pairs, $rules);
    $i++;
}

//Count occurrences, order by count, find highest and lowest, and subtract for result
$occurrences = countOccurrences($template_pairs);
$result = array_pop($occurrences) - $occurrences[0];

//result
echo "After " . $steps_to_run . " steps, most common minus least common element quantities result in $result." . PHP_EOL;


/**
 * Loop through input list of pairs, create two new pairs for each input pair, using the char obtained from rules.
 * Keep count of new pairs based on original pairs.
 *
 * @author Adrian
 * @date_created 2021-12-14
 *
 * @param array $template_pairs the pairs to apply the polymerization rules to
 * @param array $rules rules to apply in the format 'pair' => 'new_char'
 *
 * @return array the new list of pairs and their counts
 */
function applyRulesToPairs($template_pairs, $rules) {
    //start new string as empty
    $new_template_pairs = [];

    //loop through the pairs and convert them into the new pairs, keeping the counts related
    foreach ($template_pairs as $lookup_pair => $count) {

        //for every pair, we have two new pairs afterwards (as the new letter gets added "in the middle"
        $new_pairs = [
            substr($lookup_pair,  0 , 1) . $rules[$lookup_pair],
            $rules[$lookup_pair] . substr($lookup_pair, 1, 1),
        ];

        foreach ($new_pairs as $new_pair) {
            //if there is no count for this pair, set it to as many as there were related lookup pairs
            if(!isset($new_template_pairs[$new_pair])) {
                $new_template_pairs[$new_pair] = $count;
            }
            else {
                $new_template_pairs[$new_pair] += $count; //else increase count by count of lookup pairs
            }
        }
    }

    //return the new pairs
    return $new_template_pairs;
}

/**
 * Count occurrences per character and return them ordered ascending.
 * Note the "hack" by rounding when splitting the count by 2.
 * As we store pairs, every letter is part of two pairs except for the first and last of the initial string.
 * Meaning when we split the char count of those two letters (assuming they are not the same, in which case we are fine),
 * we get a .5 decimal for their count, which we simply round up to count them as 1 character.
 *
 * @author Adrian
 * @date_created 2021-12-14
 *
 * @param array $template_pairs the pairs to count occurrences from
 *
 * @return array occurrence count per character
 */
function countOccurrences(array $template_pairs) {
    $occurrences = [];
    foreach ($template_pairs as $pair => $count) {
        $chars = str_split($pair);

        foreach ($chars as $char) {
            if (!isset($occurrences[$char])) {
                $occurrences[$char] = $count;
            }
            else {
                $occurrences[$char] += $count;
            }
        }
    }

    //loop through and half the occurrences we added
    foreach ($occurrences as $key => $occurrence) {
        $occurrences[$key] = round($occurrence / 2);
    }

    //sort and return
    sort($occurrences);
    return $occurrences;
}