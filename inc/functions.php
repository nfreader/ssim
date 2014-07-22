<?php

function directLoad($page) {
  echo "<script>$('#game').load('".$page."');</script>";
}

/* spobType
 *
 * Outputs the correct title for a spob based on its type
 *
 * @type (string) The spob type
 *
 * @return (string) Planet, Moon, Station or '' (nothing, ie: The Death Star)
 *
*/

function spobType($type) { 
  switch ($type) {
    case 'P':
    default:
    $type = "Planet";
    break;

    case 'M':
    $type = "Moon";
    break;

    case 'S':
    $type = "Station"; //That's no moon...
    break;

    case 'N':
    $type = "";
    break;
  }
  return $type;
}

/* hexPrint
 *
 * Mutilates a given string into a unique, Cool Looking(tm) hex string
 *
 * @string (string) The string we're abusing
 * @prefix (string) A few characters we can use to denote a hex string. Default
 * is '0x'
 *
 * @return (string) A Cool Looking(tm) hex string
 *
*/

function hexPrint($string,$prefix="0x") {
  $string = str_split(bin2hex(substr(sha1($string),32)));
  //First, we're taking the sha1 sum of the string.
  //Next, we grab the first 32 characters of that...
  //Convert it to hex and finally split the string into an array
  $output = $prefix;
  $i = 1;
  foreach($string as $char) {
    $output.= $char;
    if ($i==2) {
      $output.=':'; //Add the separator...
      $i = 0;
    }
    $i++;
  }
  return substr($output,0,-1); //And output the string minus the trailing ':'
}

// function commodPrice() {
//   $types = array(
//     'Food',
//     'Technology',
//     'Materials'
//   ); 
// }

function randVessel(){
  global $adjectives;
  global $gods;
  
  $vessel = $adjectives[array_rand($adjectives)];
  $vessel .= " ";
  $vessel .= $gods[array_rand($gods)];
  return $vessel;
}
