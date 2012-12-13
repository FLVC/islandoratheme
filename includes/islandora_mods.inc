<?php

/**
 * @file
 * Implements a simple class for working with MODS data
 */

/**
 * Mods Class
 */
class MODS {

  /* This function takes a SimpleXML object as input and returns an array with all the pertinent elements/attributes */
  function as_formatted_array($mods_object) {
    $mods_array = array();
    $i = 0;
    $counter = 0;

    // Title Section
    if (isset($mods_object->titleInfo->title))
    {
      foreach ($mods_object->titleInfo->title as $title) {
        switch((string) $title['type']) { // Get attributes as element indices
          case 'alternative':
            $mods_array[$i]['value'] = $title;
            $mods_array[$i]['label'] = 'Alternative Title';
            $mods_array[$i]['class'] = $title->getName() . '_alternative';
            $i++;
            break;
          default:
            $mods_array['mods:title']['value'] = $title;
            $mods_array['mods:title']['label'] = 'Title';
            $mods_array['mods:title']['class'] = $title->getName();
            $i++;
        }
      }
    }
    
    // Creator/Contributor Section
    if (isset($mods_object->name->namePart))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Creator/Contributor';
      foreach ($mods_object->name->namePart as $contributor) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $contributor;
          $mods_array['mods:creator']['value'] = $contributor;
          $mods_array['mods:creator']['label'] = 'Creator/Contributor';
          $mods_array['mods:creator']['class'] = $contributor->getName();
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $contributor;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->name->namePart->getName();
      $i++;
    }
    
    // Type of Resource Section
    if (isset($mods_object->typeOfResource))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Type of Resource';
      foreach ($mods_object->typeOfResource as $type_of_resource) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $type_of_resource;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $type_of_resource;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->typeOfResource->getName();
      $i++;
    }
    
    // Genre Section
    if (isset($mods_object->genre))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Genre';
      foreach ($mods_object->genre as $genre) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $genre;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $genre;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->genre->getName();
      $i++;
    }
      
    // Issued As Section
    if(isset($mods_object->originInfo->issuance))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Issued As';
      foreach ($mods_object->originInfo->issuance as $issued_as) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $issued_as;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $issued_as;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->originInfo->issuance->getName();
      $i++;
    }
      
    // Date Section    
    if (isset($mods_object->originInfo->dateIssued))
    {
      $mods_array['mods:date']['value'] = $mods_object->originInfo->dateIssued;
      $mods_array['mods:date']['label'] = 'Date';
      $mods_array['mods:date']['class'] = $mods_object->originInfo->dateIssued->getName();
      $i++;
    }
    
    // Publisher Section
    if (isset($mods_object->originInfo->publisher))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Publisher';
      foreach ($mods_object->originInfo->publisher as $publisher) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $publisher;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $publisher;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->originInfo->publisher->getName();
      $i++;
    }
    
    // Place of Publication Section
    if (isset($mods_object->originInfo->place->placeTerm))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Place of Publication';
      foreach ($mods_object->originInfo->place->placeTerm as $place_of_publication) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $place_of_publication;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $place_of_publication;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->originInfo->place->placeTerm->getName();
      $i++;
    }
    
    // Physical Description Section
    if (isset($mods_object->physicalDescription))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Physical Description';
      foreach ($mods_object->physicalDescription as $physical_description) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $physical_description;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $physical_description;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->physicalDescription->getName();
      $i++;
    }
    
    // Language Section
    if (isset($mods_object->language->languageTerm))
    {
      foreach ($mods_object->language->languageTerm as $language) {
        switch((string) $language['type']) { // Get attributes as element indices
          case 'text':
            $mods_array[$i]['value'] = $language;
            $mods_array[$i]['label'] = 'Language';
            $mods_array[$i]['class'] = $language->getName() . '_text';
            $i++;
            break;
          default:
        }
      }
    }
    
    // Abstract/Description Section    
    if (isset($mods_object->abstract))
    {
      $mods_array['mods:description']['value'] = $mods_object->abstract;
      $mods_array['mods:description']['label'] = 'Abstract/Description';
      $mods_array['mods:description']['class'] = $mods_object->abstract->getName();
      $i++;
    }
    
    // Table of Contents Section    
    if (isset($mods_object->tableOfContents))
    {
      $mods_array[$i]['value'] = $mods_object->tableOfContents;
      $mods_array[$i]['label'] = 'Table of Contents';
      $mods_array[$i]['class'] = $mods_object->tableOfContents->getName();
      $i++;
    }

    // Identifier Section
    if (isset($mods_object->identifier))
    {
      foreach ($mods_object->identifier as $identifier) {
        switch((string) $identifier['type']) { // Get attributes as element indices
          case 'IID':
            $mods_array[$i]['value'] = trim($identifier);
            $mods_array[$i]['label'] = 'Identifier';
            $mods_array[$i]['class'] = $identifier->getName() . '_iid';
            $i++;
            break;
          default:
            $mods_array[$i]['value'] = trim($identifier);
            $mods_array[$i]['label'] = 'Identifier';
            $mods_array[$i]['class'] = $identifier->getName() . '_id';
            $i++;
        }
      }
    }
    
    // Notes Section
    if (isset($mods_object->note))
    {
      foreach ($mods_object->note as $note) {
        switch((string) $note['type']) { // Get attributes as element indices
          case 'rights':
            break;
          default:
            $mods_array[$i]['value'] = $note;
            $mods_array[$i]['label'] = 'Note';
            $mods_array[$i]['class'] = $note->getName() . '_text';
            $i++;
        }
      }
    }
    
    // Subject Section
    if (isset($mods_object->subject))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Subject';
      foreach ($mods_object->subject->children() as $subject) {
        if ($counter > 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . ' -- ' . $subject;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $subject;
        }
        $counter++;
      }   
      $mods_array[$i]['class'] = $mods_object->subject->getName();
      $i++;
    }
    
    // Rights Section
    if (isset($mods_object->note))
    {
      foreach ($mods_object->note as $note) {
        switch((string) $note['type']) { // Get attributes as element indices
          case 'rights':
            $mods_array[$i]['value'] = $note;
            $mods_array[$i]['label'] = 'Rights';
            $mods_array[$i]['class'] = $note->getName() . '_rights';
            $i++;          
            break;
          default:
        }
      }  
    }
    
    return $mods_array;
  }
}

