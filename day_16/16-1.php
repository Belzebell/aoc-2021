<?php

const VER_LENGTH = 3;
const TYPE_LENGTH = 3;
const BYTE_LENGTH = 4;
const BIT_COUNT_LENGTH = 15;
const PACKET_COUNT_LENGTH = 11;

const TYPE_LITERAL = 4;

const OP_MODE_BIT_COUNT = 0;
const OP_MODE_PACKET_COUNT = 1;

//read input (it is only one line in this puzzle
$hex_str = "";
$input = fopen("input.txt", "r");
while (($input_line = fgets($input)) !== false) {
    $hex_str = trim($input_line);
    break;
}
fclose($input);

//convert hex string to bits (hex by hex as we otherwise get precision issues with base_convert)
$bit_str = "";
foreach(str_split($hex_str, 1) as $part) {
    $bit_str .= str_pad(base_convert($part, 16, 2), BYTE_LENGTH, '0', STR_PAD_LEFT);
}

//recursively read the packet
$pointer = 0;
$ver_sum = readPacket($bit_str, $pointer);

//result
echo "The sum of all versions in this package is $ver_sum." . PHP_EOL;


/**
 * Reads a packet and recursively calls itself for any operator packets it finds in the hierarchy.
 *
 * @author Adrian
 * @date_created 2021-12-16
 *
 * @param string $bin the binary string being parsed
 * @param int $pointer current pointer location (before packet header)
 *
 * @return int the combined sum of versions of all packets in the hierarchy
 */
function readPacket($bin, &$pointer) {

    //get info from packet header
    list($version, $type) = readPacketHeader($bin, $pointer);

    //if the packet contains a literal, we do not actually care about it - just read to increment the pointer
    if($type == TYPE_LITERAL) {
        readLiteralPacket($bin, $pointer) . PHP_EOL;
    }
    else { //this is an operator packet
        $mode = substr($bin, $pointer++, 1);

        //read sub-packets by given bit count
        if($mode == OP_MODE_BIT_COUNT) {
            //find how many bits to read
            $bit_count = bindec(substr($bin, $pointer, BIT_COUNT_LENGTH));
            $pointer += BIT_COUNT_LENGTH;

            //until we have reached the bit count, recursively call this function and pass the pointer to continue it
            $pre_parse = $pointer;
            while(($pointer - $pre_parse) < $bit_count) {
                $version += readPacket($bin, $pointer);
            }
        }
        else { //read sub-packets by given packet count
            //find how many packets there are
            $packet_count = bindec(substr($bin, $pointer, PACKET_COUNT_LENGTH));
            $pointer += PACKET_COUNT_LENGTH;

            //for every packet, recursively call this function and pass the pointer to continue it
            for ($i = 0; $i< $packet_count; $i++) {
                $version += readPacket($bin, $pointer);
            }
        }

    }

    //return the version (or sum of versions of below packets)
    return $version;
}

/**
 * Read the first bits to determine packet header and packet type.
 *
 * @author Adrian
 * @date_created 2021-12-16
 *
 * @param string $bin the binary string being parsed
 * @param int $pointer current pointer location (before packet header)
 *
 * @return array version and type
 */
function readPacketHeader($bin, &$pointer) {
    //read version and increment pointer
    $version = bindec(substr($bin, $pointer, VER_LENGTH));
    $pointer += VER_LENGTH;

    //read type and increment pointer
    $type = bindec(substr($bin, $pointer, TYPE_LENGTH));
    $pointer += TYPE_LENGTH;

    return [$version, $type];
}

/**
 * If the packet is literal, read until a byte starts with 0.
 * Then convert the total to a decimal number and return.
 *
 * @author Adrian
 * @date_created 2021-12-16
 *
 * @param string $bin the binary string being parsed
 * @param int $pointer current pointer location (after packet header)
 *
 * @return int the resulting decimal number
 */
function readLiteralPacket($bin, &$pointer) {
    $bin_number = '';
    do {
        $prefix = substr($bin, $pointer++, 1);
        $bin_number .= substr($bin, $pointer, BYTE_LENGTH);
        $pointer += BYTE_LENGTH;

    } while ($prefix != 0);

    return bindec($bin_number);
}