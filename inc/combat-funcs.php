<?php

function evasionChance($accel, $turn, $mass) {
  return floor(($accel * $turn) / $mass);
}

function modifyEvasion($base, $modifier) {
  return $base + ($base * $modifier);
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


function parsePilot($pilot) {
  $pilot->stats = new stdclass;
  $pilot->stats->attack = 0;
  $pilot->stats->defend = 0;
  $pilot->stats->attacked = 0;
  $pilot->stats->evaded = 0;
  $pilot->status = 'C';
  $pilot->flee = 25;
  return $pilot;
}
