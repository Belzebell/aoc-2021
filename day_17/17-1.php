<?php

//read input (it is only one line in this puzzle)
$target_area = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $line = trim($input_line);
    $line_parts = explode('=', $line); //three pieces, discard the first, the second is x, the third is y

    //y part is fine, but x part as a comma, space and 'y' in its split string behind
    $x_part = explode(',', $line_parts[1])[0];

    //assign the 2 coords each as the x and y coordinates
    $target_area['x'] = explode('..', $x_part);
    $target_area['y'] = explode('..', $line_parts[2]);

    break; //break as only one line
}
fclose($input);

//min y to see when we can stop trying the trajectory, max x and max y to give us an idea of what range to bruteforce
$min_y = min($target_area['y'][0], $target_area['y'][1]);
$max_y = max(abs($target_area['y'][0]), abs($target_area['y'][1]));
$max_x = max(abs($target_area['x'][0]), abs($target_area['x'][1]));

//brute force possibilities from a high y going down, for each y try every x, first time this works is the highest we can go
$highest = 0;
$hit_target = false;
for($y = $max_y*2; $y > -($max_y*2); $y--) {
    for($x = $max_x*2; $x > -($max_x*2); $x--) {
        //now run the trajectory, keeping track of the highest y coordinate
        $highest = 0;
        $x2 = $x;
        $y2 = $y;
        $curr_x = $curr_y = 0;
        while ($y2 >= $min_y) { //follow the trajectory until it's "below" the target area

            //only modify x towards 0, until it hits 0, then no change
            if ($x2 > 0) {
                $curr_x += $x2--;
            }
            elseif ($x2 < 0) {
                $curr_x += $x2++;
            }

            //y just always decreases
            $curr_y += $y2--;

            //find the higher value between the current y position and the highest in this trajectory so far
            $highest = max($curr_y, $highest);

            if (($target_area['x'][0] <= $curr_x && $curr_x <= $target_area['x'][1]) && ($target_area['y'][0] <= $curr_y && $curr_y <= $target_area['y'][1])) {
                $hit_target = true; //we are in the target area, stop looping
                break;
            }
        }

        //stop looping when we found a $y that hit the target (as this must be the highest y to do so)
        if ($hit_target) {
            break;
        }
    }

    //stop looping when we found a $y that hit the target (as this must be the highest y to do so)
    if ($hit_target) {
        break;
    }
}

//result
echo "The highest y position the probe could reach is $highest while still hitting the target in its trajectory." . PHP_EOL;
