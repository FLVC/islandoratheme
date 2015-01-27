<?php

/**
 * @file
 * A collection of functions that process the Origin Info element and its sub-elements
 */

require_once('mods_helper.inc');

function processOriginInfo($mods_object, &$mods_array, &$i) {
  foreach ($mods_object->originInfo as $origin) {
    processIssuance($origin, $mods_array, $i);
    processFrequency($origin, $mods_array, $i);
    processDates($origin, $mods_array, $i);
    processPublisher($origin, $mods_array, $i);
    processPlace($origin, $mods_array, $i);
  }
}

// This function processes the Issuance sub-element of Origin Info
function processIssuance($origin, &$mods_array, &$i) {
  if (isset($origin->issuance)) {
    $counter = 0;
    $mods_array[$i]['value'] = '';
    $mods_array[$i]['label'] = 'Issuance';
    
    foreach ($origin->issuance as $issued_as) {
      if ($counter == 0) {
        $mods_array[$i]['value'] = $mods_array[$i]['value'] . $issued_as;
      }
      else {
        $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $issued_as;
      }
      $counter++;
    }

    $mods_array[$i]['class'] = $origin->issuance->getName();
    $i++;
  }
}

// This function processes the Frequency sub-element of Origin Info
function processFrequency($origin, &$mods_array, &$i) {
  if (isset($origin->frequency)) {
    $counter = 0;
    $mods_array[$i]['value'] = '';
    $mods_array[$i]['label'] = 'Frequency';
    foreach ($origin->frequency as $frequency) {
      if ($counter == 0) {
        $mods_array[$i]['value'] = $mods_array[$i]['value'] . $frequency;
      }
      else {
        $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $frequency;
      }
      $counter++;
    }
    $mods_array[$i]['class'] = $origin->frequency->getName();
    $i++;
  }
}

// This function processes the Publisher sub-element of Origin Info
function processPublisher($origin, &$mods_array, &$i) {
  if (isset($origin->publisher)) {
    $counter = 0;
    $mods_array[$i]['value'] = '';
    $mods_array[$i]['label'] = 'Publisher';

    foreach ($origin->publisher as $publisher) {
      if ($counter == 0) {
        $mods_array[$i]['value'] = $mods_array[$i]['value'] . $publisher;
      }
      else {
        $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $publisher;
      }
      $counter++;
    }

    $mods_array[$i]['class'] = $origin->publisher->getName();
    $i++;
  }
}

// This function processes the Place of Publication sub-element of Origin Info
function processPlace($origin, &$mods_array, &$i) {
  if (isset($origin->place)) {
    $mods_array[$i]['value'] = '';
    $mods_array[$i]['label'] = 'Place of Publication';

    foreach ($origin->place as $place) {
      foreach ($place->placeTerm as $place_of_publication) {
        if ($place_of_publication['type'] != 'code') {
          if (strlen($mods_array[$i]['value']) > 0) {
            $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />';
          }
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $place_of_publication;
        }
      }
    }
    
    $mods_array[$i]['class'] = $origin->place->placeTerm->getName();
    $i++;
  }
}

// This function processes the Dates sub-element of Origin Info
function processDates($origin, &$mods_array, &$i) {

  if (isset($origin->copyrightDate)) {
    $last_date_point = '';
    foreach ($origin->copyrightDate as $copyrightDate) {
      addDate($copyrightDate, 'Copyright Date', $last_date_point, $mods_array, $i);
    }
  }

  if (isset($origin->dateCreated)) {
    $last_date_point = '';
    foreach ($origin->dateCreated as $dateCreated) {
      addDate($dateCreated, 'Date Created', $last_date_point, $mods_array, $i);
    }
  }

  if (isset($origin->dateIssued)) {
    $last_date_point = '';
    foreach ($origin->dateIssued as $dateIssued) {
      addDate($dateIssued, 'Date Issued', $last_date_point, $mods_array, $i);
    }
  }

  if (isset($origin->dateCaptured)) {
    $last_date_point = '';
    foreach ($origin->dateCaptured as $dateCaptured) {
      addDate($dateCaptured, 'Date Captured', $last_date_point, $mods_array, $i);
    }
  }

  if (isset($origin->dateValid)) {
    $last_date_point = '';
    foreach ($origin->dateValid as $dateValid) {
      addDate($dateValid, 'Date Valid', $last_date_point, $mods_array, $i);
    }
  }

  if (isset($origin->dateModified)) {
    $last_date_point = '';
    foreach ($origin->dateModified as $dateModified) {
      addDate($dateModified, 'Date Modified', $last_date_point, $mods_array, $i);
    }
  }

  if (isset($origin->dateOther)) {
    $last_date_point = '';
    foreach ($origin->dateOther as $dateOther) {
      addDate($dateOther, 'Other Date', $last_date_point, $mods_array, $i);
    }
  }
}
