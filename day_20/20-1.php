<?php

//define how often to reprocess the image
if(!isset($iterations)) {
    $iterations = 2;
}

//read input (first line is the algorithm the rest a multidimensional array acting as input image)
$algorithm = '';
$input_img = [];
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $line = trim($input_line);

    if(empty($line)) continue; //skip the line between algorithm and input image

    //if the algorithm has not been set yet this must be the first line
    if(empty($algorithm)) {
        $algorithm = $line;
    }
    else {
        $input_img[] = str_split($line, 1);
    }
}
fclose($input);

//replace . and # with 0 and 1 already in image and algo, for binary string and later LED count (we have no use for the inital representation)
$algorithm = str_replace(['.', '#'], [0, 1], $algorithm);;
foreach ($input_img as $row_no => $img_row) {
    foreach ($img_row as $loc => $led) {
        $input_img[$row_no][$loc] = str_replace(['.', '#'], [0, 1], $led);
    }
}

//pad image as many times as there are iterations
$input_img = padImage($input_img, $iterations);

//process input as often as defined by $iterations
for($i = 0; $i < $iterations; $i++) {
    $input_img = processImage($input_img, $algorithm, $i);
}

//count switched on LEDs
$led_count = 0;
foreach ($input_img as $row_no => $img_row) {
    $led_count += array_sum($img_row);
}

//result
echo "After processing the input image twice, $led_count LEDs are lit up." . PHP_EOL;


/**
 * Add 0 values in columns and rows to all four sides of the input as often as specified by count
 *
 * @author Adrian
 * @date_created 2021-12-20
 *
 * @param array $input_img multidimensional array to be padded
 * @param int $count how many rows/cols to add (per side)
 *
 * @return array the padded image
 */
function padImage(array $input_img, int $count)
{
    for ($i = 1; $i <= $count; $i++) {

        //add columns left and right
        foreach ($input_img as $row_no => $input_row) {
            array_unshift($input_img[$row_no], '0');
            array_push($input_img[$row_no], '0');
        }

        //add rows above and below
        array_unshift($input_img, array_fill(0, count($input_img[0]), '0'));
        array_push($input_img, array_fill(0, count($input_img[0]), '0'));
    }

    return $input_img;
}

/**
 * Use the supplied algorithm to "enhance" the image by changing 0s and 1s into each other as specified.
 * Takes as optional parameter the current iteration count to handle the outer "infinite" image.
 *
 * @author Adrian
 * @date_created 2021-12-20
 *
 * @param array $input_img image to be processed/enhanced
 * @param string $algorithm The algorithm to apply to the image
 * @param int $i Optional iteration count to handle an algorithm that changes 0 to 1 for decimal 0
 *
 * @return array the processed/enhanced image
 */
function processImage($input_img, $algorithm, $i = 0) {

    //if the algorithm converts 9 dots to 0, nothing changes, if it converts them to 1, means the "infinite" pixels aroud need to all become 1 every odd iteration
    $alt = $algorithm[0] == 0 ? 0 : $i%2;

    $output_img = [];
    foreach ($input_img as $row_no => $img_row) {
        foreach ($img_row as $loc => $led) {

            //find the nine LEDs around it and build the string, add 0 if not exists as "infinite image"
            $led_string = '';
            foreach (range(-1, 1) as $row_mod) {
                foreach (range(-1, 1) as $loc_mod) {
                    $led_string .= $input_img[$row_no+$row_mod][$loc+$loc_mod] ?? $alt;
                }
            }

            //lookup dec location on algorithm and set on new output
            $offset = bindec($led_string);
            $output_img[$row_no][$loc] = substr($algorithm, $offset, 1);
        }
    }

    return $output_img;
}
