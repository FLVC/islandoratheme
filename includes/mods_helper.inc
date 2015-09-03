<?php

/**
 * @file
 * A collection of helper functions that help process the MODS metadata for display
 */

// if there isn't a colon at the end of $string, add one
function addColon($string) {
  if (substr($string, -1) != ':') {
    $string.= ':';
  }

  return $string;
}

// if there isn't a period at the end of $string, add one
function addPeriod($string) {
  if (substr($string, -1) != '.') {
    $string.= '.';
  }

  return $string;
}

//if there are quotes, remove them
function removeQuotes($string) {
  $no_sq_string = str_replace("'", "", $string);
  $final_string = str_replace('"', '', $no_sq_string);
  return $final_string;
}

// if there isn't a parentheses at the beginning and end of $string, add one
function addParentheses($string) {
  if ($string == '') {
    return $string;
  }

  if (substr($string, 0, 1) != '(') {
    $string = '(' . $string;
  }

  if (substr($string, -1) != ')') {
    $string.= ')';
  }

  return $string;
}

function addDate($newdate, $label, &$last_date_point, &$mods_array, &$i) {
  if (isset($newdate['point']) && $newdate['point'] == 'end' && $i > 0 && $last_date_point == 'start') {
    $mods_array[$i - 1]['value'] .= $newdate;
  }
  else {
    $mods_array[$i]['value'] = '';
    if (isset($newdate['point']) && $newdate['point'] == 'end') {
      $mods_array[$i]['value'] = '- ';
    }
    $mods_array[$i]['value'] .= $newdate;
    if (isset($newdate['point']) && $newdate['point'] == 'start') {
      $mods_array[$i]['value'] .= ' - ';
    }
    $mods_array[$i]['label'] = $label;
    $mods_array[$i]['class'] = $newdate->getName();
    $i++;
  }
  if (isset($newdate['point'])) {
    $last_date_point = $newdate['point'];
  }
  else {
    $last_date_point = '';
  }
}