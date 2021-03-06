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
 * @param $value (int) The value we're looking at
 * @param $one (string) The output if the value is one
 * @param $many (string) The output if the value is greater than one
 *
 * @return string
 *
 */

function singular($value, $one, $many) {
  if ($value == 1) {
    return number_format($value)." $one";
  } else {
    return number_format($value)." $many";
  }
}

/**
 * spobType
 *
 * Returns the full type of a spob when given the spob type.
 *
 * @param $type (string) The type of the spob we're looking at
 *
 * @return (string) The type of spob we're looking at.
 *
 */

function spobType($type,$return='text') {
  if ('text' === $return) {
    switch ($type) {
      case 'P':
      default:
        return "Planet";
        break;

      case 'M':
        return  "Moon";
        break;

      case 'S':
        return "Station"; //That's no moon...
        break;

      case 'N':
        return "";
        break;
    }
  } elseif ('icon' === $return) {
    switch ($type) {
      case 'P':
      default:
        return icon('globe');
        break;

      case 'M':
        return icon('circle-o');
        break;

      case 'S':
      case 'N':
        return icon('industry','fa-flip-vertical');
        break;
    }
  }
}

/**
 * spobName
 *
 * Returns the full name of the given spob, with the type added as a suffix or prefix respectively.
 *
 * @param $name (string) The name of the spob
 * @param $type (string) The type of spob
 *
 * @return (string) The full name of the spob
 */

function spobName($name,$type) {
  $fullType = spobType($type);
  if ($type == 'P') {
    return "$fullType $name";
  } elseif ('S' == $type || 'M' == $type) {
    return "$name $fullType";
  } else {
    return $name;
  }
}

/**
 * landVerb
 *
 * Returns the correct verbiage to use for a given spob when dealing with
 * pilots landing/lifting off.
 *
 * @param $type (string) The spob type we're looking at
 * @param $tense (string) The verb tense to use.
 * Defaults to PAST (docked at, landed on).
 * Options: Future (LAND on, DOCK with), present (LANDED on, DOCKED with)
 *
 * @return (string) The verbiage we want based on the type and tense
 */
function landVerb($type, $tense = null) {
  if ('future' == $tense) {
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

  } elseif ('present' == $tense) {
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
  } else {
    switch ($type) {
      case 'P':
      case 'M':
      default:
        $type = "landed on";
        break;

      case 'S':
      case 'N':
        $type = "docked with";
        break;
    }
  }

  return ucfirst($type);
}

function pilotStatus($status,$spobname,$spobtype) {
  switch ($status) {
    case 'L':
    return landVerb($spobtype,null). " ".$spobname;
  }
}

/**
 * fuelCost
 *
 * Returns the cost of fuel per unit based on the spob tech level and type
 *
 * @param $techlevel (int) The tech level of the spob we're looking at.
 * Defaults to 1
 *
 * @param $type (string) The spob type we're looking at. Defaults to 'P' if not
 * specified
 *
 * @return (int) The cost of one unit of fuel on this spob
 *
 */

function fuelCost($techlevel=1,$type) {
  switch($type) {
    case 'P':
    default:
      return floor(FUEL_BASE_COST/$techlevel);
      break;

    case 'S':
    case 'N':
      return floor(FUEL_BASE_COST/$techlevel) * 1.5;
      break;

    case 'M':
      return floor(FUEL_BASE_COST/$techlevel) * .5;
      break;
  }
}

/**
 * hexPrint
 *
 * Mutilates a given string into a Cool Looking™ string of hex.
 *
 * @param $string (string) The string we're manipulating
 * @param $prefix (string) The prefix to put in front. Defaults to '0x'
 *
 * @return (string) The hex string!
 */

function hexPrint($string, $prefix = "0x") {
  $return = $prefix;
  $array = str_split(substr(sha1($string),0,18),3);
  foreach ($array as $val) {
    $return.= ':'.$val;
  }
  return $return;
}

/**
 * icon
 *
 * Renders the necessary HTML for a FontAwesome icon!
 *
 * @param $icon (string) The name of JUST the icon.
 * See @link http://fontawesome.io/icons/ for a full listing
 *
 * @param $class (string) An optional class to add to the icon.
 * Technically could be a part of $icon but where's the fun in that?!
 *
 * @return (string) The HTML for a FontAwesome icon!
 */

function icon($icon, $class = '') {
  return "<i class='fa fa-".$icon." ".$class."'></i> ";
}

/**
 * credits
 *
 * Outputs the HTML for a properly formatted credits display.
 *
 * @param $credits (int) The number of credits.
 *
 * @return (string) The properly formatted number of credits followed by the
 * declared icon for the game's currency
 */

function credits($credits) {
  return "<span id='credits'>".number_format($credits + 0)."</span> ".icon(CURRENCY_ICON,'credits');
}

/**
 * pick
 *
 * Grabs one item from a list or an array of choices
 *
 * @param $list (mixed) Either a comma separated list or an array of choices to
 * pick from
 *
 * @return (string) A random item from the specified list
 */

function pick($list) {
  if (!is_array($list)) {
    $list = explode(',',$list);
  }
  return $list[floor(rand(0,count($list)-1))];
}

/**
 * methodRequires
 *
 * Used to check that a function that accepts an array or list of arguments is
 * being provided the correctly formatted data
 *
 * @param $list (mixed) A list of required data for the method
 * @param $data (array) The data we're checking against
 *
 * @return (bool) True if all fields are in the data, false if not
 */
function methodRequires($list,$data) {
  if(is_array($list)) {
    $list = $requirements;
  } else {
    $requirements = explode(',',$list);
  }
  foreach ($requirements as $requires) {
    if(!array_key_exists($requires, $data) || empty($data[$requires])) {
      return false;
    }
  }
  return true;
}

/**
 * returnError
 *
 * Formats an array to be returned() by the calling method
 *
 * @param $msg (string) The error message
 *
 * @return (array) An array consisting of the error message and the error level
 * code.
 */

function returnError($msg) {
  return json_encode(array('message'=>$msg,'level'=>2),JSON_FLAGS);
}

function returnMessage($msg) {
  return json_encode(array('message'=>$msg,'level'=>0),JSON_FLAGS);
}

function returnSuccess($msg) {
return json_encode(array('message'=>$msg,'level'=>1),JSON_FLAGS);
}

function sieve(array $post, array $accept) {
  $filtered = [];
  foreach ($post as $k => $v) {
    if (in_array($k, $accept)) {
      $filtered[$k] = $v;
    }
  }
  return $filtered;
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

		case 'RV':
		$action = 'Renamed Vessel';
		break;

		case 'CS':
		$action = 'Sold Commod';
		break;

		case 'CB':
		$action = 'Bought Commod';
		break;
	}
	return $action;
}

function documentType($type) {
	switch ($type) {
		case 'CS':
		$type = array();
		$type['text'] = 'Commodity Sale';
		$type['class'] = 'commodity commod-sale';
		return $type;
		break;

		case 'CB':
		$type = array();
		$type['text'] = 'Commodity Purchase';
		$type['class'] = 'commodity commod-buy';
		return $type;
		break;
	}
}

// function table($cols, $rows, $class, $rowclass){
// 	$header = "<table class="table"  class='table ".$class."'><thead><tr>";
//     foreach ($columns as $column) {
//         $header.= "<th>".$column."</th>";
//     }
//     $header.= "</thead><tbody>";
//     $row = '';
//     foreach ($rows as $row) {
//     	$row.="<tr>"
//     }
// }

function tableHeader($columns, $class='') {
    $header = "<table class='table ".$class."'><thead><tr>";
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

function optionlist($options) {
	$list = "<ul class='dot-leader'>";
	foreach($options as $key => $value) {
		$list.= "<li id='".strtolower($key)."'>";
		$list.= "<span class='left'>$key</span>";
		$list.= "<span class='right'>$value</span></li>";
	}
	$list.= "</ul>";
	return $list;
}

function shipValue($id, $date, $cost) {
	$diff = (time() - strtotime($date)) * .25;
	$price = $cost/$diff;
	return $cost*.85; //TODO: Make the trade-in value relative to the date the
	//ship was purchased.
	//OH MY GOD MAKE IT RELATIVE TO THE SPOB TECHLEVEL!
}

function relativeTime($date, $postfix = 'ago') {
  $diff = time() - strtotime($date);
  if ($diff < 0) {
    $diff = strtotime($date) - time();
    $postfix = 'from now';
  }
  if ($diff >= 604800) {
    $diff = round($diff/604800);
    $return = $diff." week". ($diff != 1 ? 's' : '');
  }
  elseif ($diff >= 86400) {
    $diff = round($diff/86400);
    $return = $diff." day". ($diff != 1 ? 's' : '');
  }
  elseif ($diff >= 3600) {
    $diff = round($diff/3600);
    $return = $diff." hour". ($diff != 1 ? 's' : '');
  }
  elseif ($diff >= 60) {
    $diff = round($diff/60);
    $return = $diff." minute". ($diff != 1 ? 's' : '');
  }
  elseif ($diff <= 30) {
   return "just now";
  }
  else {
    $return = $diff." second". ($diff != 1 ? 's' : '');
  }
  return "$return $postfix";
}

function timestamp($date) {
	return "<span class='time' data-toggle='tooltip' title='".date(SSIM_DATE,strtotime($date))."'>".relativeTime($date)."</span>";
}

function isEmpty($string) {
	if (empty($string) || trim($string) == '') {
		return true;
	}
	return false;
}

function govtLabel($govt) {
  return "<div class='label govt' style='color: $govt->color2; background: $govt->color1;'>".icon('shield')."$govt->name</div>";
}

function consoleDump($data) {
  if (TRUE === SSIM_DEBUG) {
    echo "<script>console.log(".json_encode($data).");</script>";
  } else {
    return;
  }
}

function meter($label,$panic,$current) {
  $meter = "<div class='meter'><small>$label</small>";
  if ($current < $panic) {
    $meter .= "<div class='progress panic'>";
  } else {
    $meter .= "<div class='progress'>";
  }
  $meter.= "<div class='progress-bar' style='width: $current%'";
  $meter.= "'title=$current%'>";
  $meter.= "</div></div></div>";
  return $meter;
}

function getArray($array) {

}

function getVKPrompt() {
  $question = pick($vk);
  $html = "<label for='vk-prompt'>".$question['question']."</label>";
  foreach ($question['answers'] AS $answer) {
    $html.= "<input type='radio' name='vk-prompt'>$answer</input>";
  }
  return $html;
}

function outfitFormatter($outfit,$button=FALSE) {
	if ('sell' == $button){
		$btn =  "<a href='sellOutfit&outfit=$outfit->id'
		data-dest='outfit/outfitter'
		class='action'>Sell</a>";
	} elseif ('buy' == $button) {
		$btn = credits($outfit->cost)."<a href='buyOutfit&outfit=$outfit->id'
		data-dest='outfit/outfitter'
		class='action'>Purchase</a>";
	} else {
		$btn = '';
	}
	$html = "<h3>$outfit->name";
	if ('buy' != $button) {
		$html.= "<div class='right'>".$outfit->quantity."x $btn</div></h3>";
	} else {
		$html.= "<div class='right'>$btn</div></h3>";
	}
	switch($outfit->type) {
		default:
			$html.= "<p>$outfit->description</p>";
		break;

		case 'E':
			switch($outfit->subtype) {
				default:
					$type = '';
				break;

				case 'B':
					$type = 'Basic Survival Pod';
				break;
			}
			$html.= "<span class='fingerprint'>$type</span>";
			$html.= "<p><small>$outfit->description</small></p>";
		break;

		case 'W':
			switch($outfit->subtype) {
				default:
					$type = 'Weapon';
				break;

				case 'E':
					$type = "Energy Weapon";
				break;
				}
			$html.= "<span class='fingerprint'>$type</span>";
			$html.= "<p>$outfit->description</p>";
			$html.= "<ul class='dot-leader'>";
			$html.= "<li><span>Damage</span><span>$outfit->value</span></li>";
			$html.= "<li><span>Reload</span><span>$outfit->reload</span></li>";
		break;
	}
	return $html;
}

function relationType($relation) {
  switch($relation) {
    case 'N':
    default:
      $return['Full'] = 'Neutral';
      $return['CSS'] = 'info';
      return $return;
      break;

    case 'A':
      $return['Full'] = 'Allied';
      $return['CSS'] = 'success';
      return $return;
      break;

    case 'W':
      $return['Full'] = 'At War';
      $return['CSS'] = 'danger';
      return $return;
      break;

  }
}

function beaconTypes($type) {
	$data = array();
	switch ($type) {
		default:
		case 'R':
		$data['class']='regular';
		$data['text']='Regular';
		$data['icon']='';
		$data['header'] = '<h2>Message Beacon</h2>';
		break;

		case 'D':
		$data['class']='distress';
		$data['text']='Distress';
		$data['icon']='circle-o';
		$data['header']='<h2>'.icon($data['icon'],'panic-icon').'Distress Beacon</h2>';
		break;

		case 'A':
		$data['class']='admin';
		$data['text']='Important Notice';
		$data['icon']='';
		$data['header']='<h2>Automated Message Beacon</h2>';
		break;
	}
	return $data;
}

function parseBeacons($beacons) {
	foreach ($beacons as $beacon) {
		switch ($beacon->type){
			default:
			case 'R':
				$beacon->class = 'regular';
				$beacon->icon = '';
				$beacon->header = "<h2>Message Beacon</h2>";
				$beacon->footer = "<small>Launched by $beacon->name</small>";
				$beacon->targetable = TRUE;
			break;
			case 'D':
				$beacon->class = 'distress';
				$beacon->icon = 'circle-o';
				$beacon->header = '<h2>'.icon($beacon->icon,'panic-icon').'Distress Beacon</h2>';
				$beacon->footer = "<small>Beacon expires in ". timestamp($beacon->expires)."</small>";
				$beacon->targetable = TRUE;
			break;
			case 'A':
				$beacon->class = 'admin';
				$beacon->icon = '';
				$beacon->header = "<h2>Important Notice</h2>";
				$beacon->footer = "";
				$beacon->targetable = FALSE;
			break;

		}
	}
	return $beacons;
}
