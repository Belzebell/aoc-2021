<?php

//read input and format it into multidimensional of risk levels
if(!isset($risk_map)) { //check if set here in case we are running part 2 of day 15
    $risk_map = [];
    $input = fopen("input.txt", "r");
    while (($input_line = fgets($input)) !== false) {
        $split_line = str_split(trim($input_line)); //split numbers into array
        $risk_map[] = $split_line; //add array to risk map array
    }
    fclose($input);
}

//we are solving today with the Dijkstra algorithm
//distances are all MAX INTEGER
for($i = 0; $i < count($risk_map); $i++) {
    $distances[] = array_fill(0, count(reset($risk_map)), PHP_INT_MAX);
}

$visited[0][0] = true; //starting point
$distances[0][0] = 0; //no distance to starting point

//our current path starts with just 0, 0
$queue = ["0_0"];

//while there are more elements in the path, keep looping (cannot use foreach as we add more elements to current_path)
while(!empty($queue)) {

    //find smallest key in queue as that's the next one we need to check
    //note: there's room for optimisation here by making the queue a key value pair where value is the distance
    // this would enable array functions like sort or min rather than a manual lookup and comparison of all values
    $min_distance = PHP_INT_MAX;
    $min_distance_loc = 0;
    foreach ($queue as $key => $q_item) {
        list($y, $x) = explode('_', $q_item);
        if($min_distance > $distances[$y][$x]) {
            $min_distance = $distances[$y][$x];
            $min_distance_loc = $key;
        }
    }
    $lowest_risk = $queue[$min_distance_loc];
    unset($queue[$min_distance_loc]); //remove the key we are checking from the queue, as we loop until queue is empty

    list($y, $x) = explode('_', $lowest_risk);
    foreach (getNeighbour($risk_map, $y, $x) as $neighbour) {
        //for every neighbour of my current path, check if their distance or the risk map plus current node value are smaller, and assign that
        list($ny, $nx) = explode('_', $neighbour);
        if($distances[$ny][$nx] > ($distances[$y][$x] + $risk_map[$ny][$nx])) {
            $distances[$ny][$nx] = $distances[$y][$x] + $risk_map[$ny][$nx];
        }

        //if this node had not been visited before, add id to the path to visit it in the next loop
        if(!isset($visited[$ny][$nx])) {
            $visited[$ny][$nx] = true; //mark node as visited
            $queue[] = $neighbour; //add neighbour to the current path
        }
    }
}

//result (the result is the distance of the bottom right node)
$total_risk = $distances[count($distances) - 1][count(reset($distances)) - 1];
echo "The lowest total risk of the possible tasks is " . $total_risk . "." . PHP_EOL;


function getNeighbour($map, $y, $x) {

    $neighbours = [];
    if(isset($map[$y-1][$x])) { //above
        $neighbours[] = ($y-1) . '_' . $x;
    }
    if(isset($map[$y+1][$x])) { //below
        $neighbours[] = ($y+1) . '_' . $x;
    }
    if(isset($map[$y][$x+1])) { //to the right
        $neighbours[] = $y . '_' . ($x+1);
    }
    if(isset($map[$y][$x-1])) { //to the left
        $neighbours[] = $y . '_' . ($x-1);
    }

    return $neighbours;
}


