<?php

//names of start and end node of the graph
const CAVE_START = 'start';
const CAVE_END = 'end';

//read input and split it into links for the cave "graph"
$node_links = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $link = explode('-', trim($input_line)); //split into links
    $node_links[$link[0]][] = $link[1]; //add link b to possible links from a
    $node_links[$link[1]][] = $link[0]; //add link a to possible links from b
}
fclose($input);

//trigger recursive depth search for every node from 'start':
findDepth(CAVE_START, $node_links,$paths);

//result
echo "There are " . count($paths) . " paths through the cave system." . PHP_EOL;


/**
 * Recursively loop through the $node_links, and, from a given $node, find subsequent paths
 *
 * @author Adrian
 * @date_created 2021-12-13
 *
 * @param $node //current node to find paths from
 * @param $node_links //"map" or "graph" of all nodes and where they link to
 * @param $found_paths //referenced variable providing a list of all found paths from start to end
 * @param array $visited //current path, checked against to ensure small caves are only visited once
 *
 * @return bool
 */
function findDepth($node, $node_links, &$found_paths, $visited = []) {

    //add node to the path
    $visited[] = $node;

    //if we found the end node here, add to found paths
    if($node == CAVE_END) {
        $found_paths[] = $visited;
        return true;
    }

    //find all linked nodes, and recursively "go deeper" for them
    foreach ($node_links[$node] as $node_link) {
        if(!ctype_lower($node_link) || !in_array($node_link, $visited)) { //for lowercase only keep going if it hasn't already been visited
            findDepth($node_link, $node_links, $found_paths, $visited);
        }
    }

    //return $found_paths
    return $found_paths;
}