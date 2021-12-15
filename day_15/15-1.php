<?php

//read input and format it into multidimensional of risk levels
$risk_map = [];
$input = fopen("testinput.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $split_line = str_split(trim($input_line)); //split numbers into array
    $risk_map[] = $split_line; //add array to risk map array
}
fclose($input);

//we are solving today with the Dijkstra algorithm
//distances are all MAX INTEGER
for($i = 0; $i < count($risk_map); $i++) {
    $distances[] = array_fill(0, count(reset($risk_map)), PHP_INT_MAX);
}

$visited[0][0] = true; //starting point
$distances[0][0] = 0; //no distance to starting point

//our current path starts with just 0, 0
$current_path = ["0_0"];

//while there are more elements in the path, keep looping (cannot use foreach as we add more elements to current_path)
for ($i = 0; $i < count($current_path); $i++) {
    list($y, $x) = explode('_', $current_path[$i]);
    foreach (getNeighbour($risk_map, $y, $x) as $neighbour) {
        //for every neighbour of my current path, check if their distance or the risk map plus current node value are smaller, and assign that
        list($ny, $nx) = explode('_', $neighbour);
        if($distances[$ny][$nx] > ($distances[$y][$x] + $risk_map[$ny][$nx])) {
            $distances[$ny][$nx] = $distances[$y][$x] + $risk_map[$ny][$nx];
        }

        //if this node had not been visited before, add id to the path to visit it in the next loop
        if(!isset($visited[$ny][$nx])) {
            $visited[$ny][$nx] = true; //mark node as visited
            $current_path[] = $neighbour; //add neighbour to the current path
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


