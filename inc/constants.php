<?php

/**
 * inc/constants.php
 * 
 * Allows the user to custmoize how Spacesim works. The current defaults are
 * the recommended settings.
 * 
 * Includes the following sections:
 *  Generic Game Constants
 *  Time and date constants
 *  Currency Constantds
 *  Fuel costs
 *  Commodity constants
 */

//Generic game constants
define('GAME_NAME','Space Sim');
define('GAME_VERSION','0.0.1-dev');

//Time and date constants
date_default_timezone_set('UTC');
define('YEAR_OFFSET', 178); //Years to offset date() displays by.
define('GAME_YEAR', date('Y') + YEAR_OFFSET);
define('SSIM_DATE',"H.i.s d.m.".GAME_YEAR); //Modeled after the terminal screens from the Marathon series.

//Currency related constants
define('CURRENCY_SINGLE','Credit');
define('CURRENCY_MULTIPLE','Credits');
define('CURRENCY_ICON','certificate');
define('CURRENCY_COLOR','gold');

//Fuel related constants
/**
 * The cost of one unit of fuel without modifications
 */
define('FUEL_BASE_COST',75);

//Commodity related constants
define('COMMOD_DISTRIBUTION',.3);
define('COMMOD_COST_MODIFIER',500);

//Game options

define('STARTING_CREDITS', 25000);//Number of credits players start with
define('STARTING_LEGAL', 1000);//Number of legal points players start with
define('FUEL_BASE', 100);//Cost of fuel, to be manipulated later on
define('CARGO_PENALTY', 50); //Multiplier for cargo sell violations (max)
define('PIRATE_PENALTY',10); //Multiplier for mission pirating (max)
define('PIRATE_THRESHHOLD', -100); //Legal score at which point the pilot is
//a pirate. Yarr.
