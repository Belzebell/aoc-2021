<?php

//how many times to apply the rules to the template
const STEPS = 10;

//read input and split it into the initial templates and the polymer rules
$template = "";
$rules = [];
$input = fopen("testinput.txt", "r");
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

//apply rules until we ran STEPS times
$i = 0;
while ($i < STEPS) {

    //start new string as empty
    $new_template = '';
    $template_pieces = str_split($template);

    //for every character, create a lookup of that character and the next
    foreach ($template_pieces as $key => $poly) {
        if(!isset($template_pieces[$key+1])) {
            $new_template .= $poly; //if we are on the last character, just append it - no pair to look up
            break;
        }

        //find new polymer to be added to the middle
        $lookup_pair = $poly . $template_pieces[$key+1];
        $new_poly = $rules[$lookup_pair];

        $new_template .= $poly . $new_poly; //add first char and the new one (second char of the pair will be added on next loop)
    }

    $template = $new_template;
    $i++;
}

//count occurrences, order by count, find highest and lowest, and subtract
$counter = count_chars($template, 1);
sort($counter);
$result = array_pop($counter) - reset($counter);

//result
echo "After " . STEPS . " steps, most common minus least common element quantities result in $result." . PHP_EOL;