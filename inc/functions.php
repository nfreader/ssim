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
