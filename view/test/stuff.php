<?php 

function inc($array) {$return = ''; foreach($array as $arr) {$return.=$arr++;} return $return;}

echo inc(array(1,2,3,4,5));