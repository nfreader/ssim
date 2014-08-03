<?php

function directLoad($page) {
	echo "<script>$('#game').load('".$page."');console.log('".$page."')</script>";
}

function returnMsg($content) {
	echo "<script>returnMsg('".$content."');</script>";
}

/* Singular
 *
 * Based on the input, outputs the singular or plural of the specified unit
 *
 * @value (int) The value we're looking at
 * @one (string) The output if the value is one
 * @many (string) The output if the value is greater than one
 *
 * @return string
 *
 */

function singular($value, $one, $many) {
	if ($value == 1) {
		return $one;
	} else {
		return $many;
	}
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
			$type = "Station";//That's no moon...
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

function hexPrint($string, $prefix = "0x") {
	$string = str_split(bin2hex(substr(sha1($string), 32)));
	//First, we're taking the sha1 sum of the string.
	//Next, we grab the first 32 characters of that...
	//Convert it to hex and finally split the string into an array
	$output = $prefix;
	$i      = 1;
	foreach ($string as $char) {
		$output .= $char;
		if ($i == 2) {
			$output .= ':';//Add the separator...
			$i = 0;
		}
		$i++;
	}
	return substr($output, 0, -1);//And output the string minus the trailing ':'
}

// function commodPrice() {
//   $types = array(
//     'Food',
//     'Technology',
//     'Materials'
//   );
// }

function randVessel() {
	global $adjectives;
	global $gods;

	$vessel = $adjectives[array_rand($adjectives)];
	$vessel .= " ";
	$vessel .= $gods[array_rand($gods)];
	return $vessel;
}

function landVerb($type, $tense = 'future') {
	if ($tense == 'future') {
		switch ($type) {
			case 'P':
			case 'M':
			default:
				$type = "Land on";
				break;

			case 'S':
			case 'N':
				$type = "Dock with";
				break;

		}

	} elseif ($tense == 'then') {
		switch ($type) {
			case 'P':
			case 'M':
			default:
				$type = "Lift off from";
				break;

			case 'S':
			case 'N':
				$type = "Undock with";
				break;
		}
	} elseif ($tense == 'past') {
		switch ($type) {
			case 'P':
			case 'M':
			default:
				$type = "Landed on";
				break;

			case 'S':
			case 'N':
				$type = "Docked at";
				break;
		}
	}

	return $type;
}

function fuelMeter($fuel, $max, $fuelMeter) {
	$meter = "<strong>Fuel</strong> $fuel " .singular($fuel, 'jump', 'jumps')." remaining";
	$meter .= "<div class='progress fuel'><div class='progress-bar' style='width: ".$fuelMeter."%'></div></div>";
	return $meter;
}

function shieldMeter($shields) {
	$meter = "<strong>Shields</strong>";
	$meter .= "<div class='progress shields'><div class='progress-bar progress-bar-info' style='width: ".$shields."%'></div></div>";
	return $meter;
}

function armorMeter($armor) {
	$meter = "<strong>Hull Integrity</strong>";
	$meter .= "<div class='progress armor'><div class='progress-bar progress-bar-warning' style='width: ".$armor."%'></div></div>";
	return $meter;
}

function cargoMeter($cargometer, $cargo, $cargohold) {
	$meter = "<strong>Cargo Hold</strong> ($cargo of $cargohold tons used)";
	$meter .= "<div class='progress cargo'><div class='progress-bar progress-bar-success' style='width: ".$cargometer."%'></div></div>";
	return $meter;
}

/* Function icon
 *
 * Renders the HTML for a Font Awesome icon!
 *
 * @icon (string) Icon to display
 * @class (string) (optional) Additional class to add to the icon. Technically could be a part of @icon, but where's the fun in that?
 *
 * @return string
 *
 */

function icon($icon, $class = '') {
	return "<span class='fa fa-".$icon." ".$class."'></span> ";
}

function gameLogActionTypes($action) {
	switch ($action) {
		default: 
		case 'O':
		$action = 'logged';
		break;

		case 'R':
		$action = 'Refueled';
		break;

		case 'MH':
		$action = 'Made homeworld';
		break;

		case 'J':
		$action = 'Jumped';
		break;

		case 'A':
		$action = 'Arrived';
		break;

		case 'D':
		$action = 'Departed';
		break;

	}

	return $action;
}

function tableHeader($columns) {
    $header = "<table class='table'><thead><tr>";
    foreach ($columns as $column) {
        $header.= "<th>".$column."</th>";
    }
    $header.= "</thead><tbody>";
    
    return $header;
}

function tableCell($cell) {
	return "<td>".$cell."</td>";
}

function tableCells($cells) {
	$return = '';
	foreach ($cells as $cell) {
		$return.= "<td>".$cell."</td>";
	}
	return $return;
}

function tableFooter() {
	return "</tbody></table>";
}

function beaconTypes($type) {
	$data = array();
	switch ($type) {
		default:
		case 'R':
		$data['class']='regular';
		$data['text']='Regular';
		$data['icon']='';
		break;

		case 'D':
		$data['class']='distress';
		$data['text']='Distress';
		$data['icon']='exclamation-triangle';
		$data['header']='<h1>'.icon($data['icon']).'Distress Beacon</h1>';
		break;
	}
	return $data;
}
