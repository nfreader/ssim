<?php

function evasionChance($accel, $turn, $mass) {
  return floor(($accel * $turn) / $mass);
}

function modifyEvasion($base, $modifier) {
  return $base + ($base * $modifier);
}

function parseOutfits($outfits) {
  $stats = array(
    'firepower'=>0,
    'evasion'=>0
  );

  foreach($outfits as $outfit) {
    switch($outfit->type) {
      default:
      break;

      case 'W': //All weapons in this section
        $type = 'Weapon';
        $stats['firepower'] = $outfit->value*$outfit->quantity;
        $stats['weapons'] = array();
        $i = $outfit->quantity;
        while ($i > 0) {
          if ($outfit->reload > 1) {
            $charge = 0;
          } else {
            $charge = FALSE;
          }
          $stats['weapons'][] = array(
            'name'=>$outfit->name,
            'damage'=>$outfit->value,
            'reload'=>$outfit->reload,
            //How many ticks it takes to reload or recharge
            'charge'=>$charge,
            'rounds'=>$outfit->rounds,
            'subtype'=>$outfit->subtype
          );
          $i--;
        }
        switch($outfit->subtype) {
          default:
          break;

          case 'E':
            $subtype = 'Energy';
            break;
        }
      break; //End of weapons section

      case 'J': //Evasion modifiers
        $type = 'Jammer';
        $stats['evasion'] = $stats['evasion'] + $outfit->value;
        switch ($outfit->subtype) {
          default:
          break;

          case 'R':
            $subtype = 'Radar';
        }
      break; //End of evasion modifiers section
    }
    //$fullname = "$outfit->name ($subtype $type) (".$outfit->quantity."x)";
  }
  return $stats;
}

function battleTick($protag, $antag, $tick) {
  $return = new stdclass;
  $return->fired = '';
  $cointoss = floor(rand(0,1));
  if (0 == $cointoss || 0 == $tick) {
    $atk = $protag;
    $def = $antag;
  } else {
    $atk = $antag;
    $def = $protag;
  }
  $return->cointoss = $atk->vessel->name. " is attacking, ";
  $return->cointoss.= $def->vessel->name." is defending.";

  $evasionChance = floor(rand(0,100));
  if ($def->vessel->ship->baseEvasion > $evasionChance) {
    $return->outcome = $def->vessel->name." evades attack by ".$atk->vessel->name;
    $def->stats->evaded++;
  } else {
    $return->outcome = $atk->vessel->name." attacks ".$def->vessel->name;
    $atk->stats->attacked++;

    foreach($atk->vessel->outfits as $outfit) {
    
    }
  }
  $return->outcome.=" Evasion chance was $evasionChance vs ".$def->vessel->ship->baseEvasion;


  //Return logic
  if (0 == $cointoss || 0 == $tick){
    $return->protag = $atk;
    $return->protag->stats->attack++;
    $return->antag = $def;
    $return->antag->stats->defend++;
  } else {
    $return->protag = $def;
    $return->protag->stats->defend++;
    $return->antag = $atk;
    $return->antag->stats->attack++;
  }
  return $return;
}


function parseOutfitsModify($vessel) {
  $evasionModifier = 0;
  foreach($vessel->outfits as &$outfit) {
    $outfit->usesAmmo = FALSE;
    switch ($outfit->type) {
      default:
      break;

      case 'W':
        switch($outfit->subtype) {
          default:
          break;

          case 'E':
          break;

          case 'M':
          $outfit->usesAmmo = TRUE;
          break;
        }

      case 'J':
        switch($outfit->subtype) {
          default:
          break;

          case 'R':
          $evasionModifier = $evasionModifier + $outfit->value;
          break;
        }
    }
    if (NULL === $outfit->reload) {
      $outfit->reload = false;
    } else {
      $outfit->charge = 0;
    }
  }
  $vessel->ship->baseEvasion = modifyEvasion($vessel->ship->baseEvasion,$evasionModifier);
  return $vessel;
}
