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
  $return->tickResult = array();
  $return->damage = '';
  $cointoss = floor(rand(0,1));
  if (0 == $cointoss || 1 == $tick) {
    $atk = $protag;
    $def = $antag;
  } else {
    $def = $protag;
    $atk = $antag;
  }
  $return->cointoss = $atk->vessel->name. " is attacking, ";
  $return->cointoss.= $def->vessel->name." is defending.";
  $return->tickResult[] = $return->cointoss;

  if ($def->vessel->ship->baseEvasion > floor(rand(0,100))) {
    $return->tickResult[] = $def->vessel->name." evades attack by ".$atk->vessel->name;
    $def->stats->evaded++;
    if ($def->vessel->ship->armor - $def->vessel->armordam <= $def->flee) {
      if ($def->vessel->ship->baseEvasion > floor(rand(0,100))){
        $def->status = 'F';
        $atk->status = 'V';
        $return->tickResult[] = $def->vessel->name." fled the battle.";
      }
    }
  } else {
    $return->outcome = $atk->vessel->name." attacks ".$def->vessel->name;
    $atk->stats->attacked++;
    foreach ($def->vessel->outfits as $outfit) {
      if ('W' == $outfit->type && $outfit->reload > 0) {
        $outfit->charge++;
      }
    }
    foreach($atk->vessel->outfits as $outfit) {
      if('W' == $outfit->type && $outfit->quantity > 0) {//Only weapons
        $damage = 0;
        if ($outfit->reload > 1 && $outfit->usesAmmo){
          //Weapon uses ammo and recharges
          if ($outfit->charge >= $outfit->reload){
            $isCharged = true;
          } else {
            $isCharged = false;
            $return->tickResult[]= $atk->vessel->name."'s $outfit->name was reloading and did not fire ($outfit->charge)";
            $outfit->charge++;
          }
          if ($outfit->rounds <= 0) {
            $hasAmmo = false;
            $return->tickResult[]= $atk->vessel->name."'s $outfit->name was out of ammo and did not fire ($outfit->rounds)";
          } else {
            $hasAmmo = true;
          }
          if ($isCharged && $hasAmmo) {
            $outfit->charge = 0;
            $outfit->rounds--;
            //FIRE!!
            $damage = $outfit->value * $outfit->quantity;
            $return->tickResult[] = $atk->vessel->name." fired $outfit->name for $damage damage";
          }
        } elseif ($outfit->reload > 1) {
          if ($outfit->charge == $outfit->reload) {
            //FIRE!!
            $damage = $outfit->value * $outfit->quantity;
            $return->tickResult[] = $atk->vessel->name." fired $outfit->name for $damage damage";
            //Reset charge
            $outfit->charge = 0;
          } else {
            $return->tickResult[] = $atk->vessel->name."'s $outfit->name was reloading and did not fire ($outfit->charge)";
            $outfit->charge++;
          }
        } elseif ($outfit->usesAmmo) {
          if($outfit->rounds > 0) {
            //FIRE!!
            $damage = $outfit->value * $outfit->quantity;
            $return->tickResult[] = $atk->vessel->name." fired $outfit->name for $damage damage";
            //Subtract a round
            $outfit->rounds--;
          }
        } else {
          //FIRE!!
          $damage = $outfit->value * $outfit->quantity;
          $return->tickResult[] = $atk->vessel->name." fired $outfit->name for $damage damage";
        }
        //Start dealing damage
        if($def->vessel->shielddam >= $def->vessel->ship->shields){
          $def->vessel->armordam+= $damage;
        } else {
          $def->vessel->shielddam+= $damage;
        }
        $return->damage = $damage;
        if ($def->vessel->ship->armor - $def->vessel->armordam <= 0) {
          $def->status = 'D';
          $atk->status = 'V';
        } elseif (NUMBER_OF_TICKS - 1 <= $tick) {
          $def->status = 'T';
          $atk->status = 'T';
        }
      }
    }
  }

  //Return logic
  if (0 == $cointoss || 1 == $tick){
    $return->protag = $atk;
    $return->antag = $def;
  } else {
    $return->protag = $def;
    $return->antag = $atk;
  }
  return $return;
}


function prepForCombat($pilot) {
  $evasionModifier = 0;
  foreach($pilot->vessel->outfits as &$outfit) {
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
          $evasionModifier += $outfit->value;
          break;
        }
    }
    if (NULL === $outfit->reload) {
      $outfit->reload = false;
    } else {
      $outfit->charge = 0;
    }
    if (1 == $outfit->reload){
      $outfit->reload = false;
    }
    $outfit->rounds = $outfit->rounds * 1;
    $outfit->reload = $outfit->reload * 1;
  }
  $pilot->vessel->ship->baseEvasion = modifyEvasion($pilot->vessel->ship->baseEvasion,$evasionModifier);
  $pilot->stats = new stdclass;
  $pilot->stats->attack = 0;
  $pilot->stats->defend = 0;
  $pilot->stats->attacked = 0;
  $pilot->stats->evaded = 0;

  $pilot->status = 'C';

  $pilot->flee = 25;

  return $pilot;
}
