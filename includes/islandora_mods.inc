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
    if (isset($mods_object->titleInfo))
    {
      foreach ($mods_object->titleInfo as $titleInfo) {
        if (isset($titleInfo->title))
        {
          $title = $titleInfo->title;
          
          // If it has a subtitle, add it
          if(isset($titleInfo->subTitle))
          {
            $subTitle = static::addPeriod($titleInfo->subTitle);
            $title = static::addColon($title) . ' ' . $subTitle;
          }
          
          // If it has a NonSort element, add it before the title
          if(isset($titleInfo->nonSort))
          {
            $title = $titleInfo->nonSort . ' ' . $title;
          }
          
          // If it has a Display Label attribute, add it before the NonSort and Title
          if(isset($titleInfo['displayLabel']))
          {
            $title = static::addColon($titleInfo['displayLabel']) . ' ' . $title;
          }
          
          // If it has a Part Number, add it
          if(isset($titleInfo->partNumber))
          {
            foreach ($titleInfo->partNumber as $partNumber) {
              $partNumber = static::addPeriod($partNumber);
              $title = $title . ' ' . $partNumber;
            } 
          }
          
          // If it has a Part Name, add it
          if(isset($titleInfo->partName))
          {
            foreach ($titleInfo->partName as $partName) {
              $partName = static::addPeriod($partName);
              $title = $title . ' ' . $partName;
            } 
          }
          
          if ($title != '')
          {
            $title = static::addPeriod($title);
          }
          
          switch((string) $titleInfo['type']) { // Get attributes as element indices
            case 'abbreviated':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Abbreviated Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_abbreviated';
              $i++;
              break;
            case 'alternative':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Alternative Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_alternative';
              $i++;
              break;
            case 'translated':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Translated Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_translated';
              $i++;
              break;
            case 'uniform':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Uniform Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_uniform';
              $i++;
              break;          
            default:
              $mods_array['mods:title']['value'] = $title;
              $mods_array['mods:title']['label'] = 'Title';
              $mods_array['mods:title']['class'] = $titleInfo->title->getName();
              $i++;
          }
        }
      }
    }
    
    // Name Section
    if (isset($mods_object->name))
    {
      foreach ($mods_object->name as $name) {
        static::addMODSName('Name', $name, $mods_array, $i);
        $i++;
      }
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
      
    // Issuance Section
    if(isset($mods_object->originInfo->issuance))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Issuance';
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
    
    // Frequency Section
    if(isset($mods_object->originInfo->frequency))
    {
      $counter = 0;
      $mods_array[$i]['value'] = '';
      $mods_array[$i]['label'] = 'Frequency';
      foreach ($mods_object->originInfo->frequency as $frequency) {
        if ($counter == 0) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $frequency;
        } else {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $frequency;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->originInfo->frequency->getName();
      $i++;
    }
      
    // Dates Section
    if (isset($mods_object->originInfo->copyrightDate))
    {
      foreach ($mods_object->originInfo->copyrightDate as $copyrightDate) {
        $mods_array[$i]['value'] = $copyrightDate;
        $mods_array[$i]['label'] = 'Copyright Date';
        $mods_array[$i]['class'] = $copyrightDate->getName();
        $i++;
      }
    }
    
    if (isset($mods_object->originInfo->dateCreated))
    {
      foreach ($mods_object->originInfo->dateCreated as $dateCreated) {
        $mods_array[$i]['value'] = $dateCreated;
        $mods_array[$i]['label'] = 'Date Created';
        $mods_array[$i]['class'] = $dateCreated->getName();
        $i++;
      }
    }
    
    if (isset($mods_object->originInfo->dateIssued))
    {
      $date_counter = 0;      

      foreach ($mods_object->originInfo->dateIssued as $dateIssued) {
        
        if ($date_counter == 0 && $dateIssued != '')
        {
          $mods_array['mods:date']['value'] = $dateIssued;
          $mods_array['mods:date']['label'] = 'Date Issued';
          $mods_array['mods:date']['class'] = $dateIssued->getName();
          $date_counter++;
        }       
        // The code below was removed to display only one 'Date Issued'.
        //If you would like to display all 'Date Issued' fields uncomment the code below.
        /*elseif ($dateIssued != '')
        {
          $mods_array[$i]['value'] = $dateIssued;
          $mods_array[$i]['label'] = 'Date Issued';
          $mods_array[$i]['class'] = $dateIssued->getName();
          $i++;
        }*/
      }
    }    
    
    if (isset($mods_object->originInfo->dateCaptured))
    {
      foreach ($mods_object->originInfo->dateCaptured as $dateCaptured) {
        $mods_array[$i]['value'] = $dateCaptured;
        $mods_array[$i]['label'] = 'Date Captured';
        $mods_array[$i]['class'] = $dateCaptured->getName();
        $i++;
      }
    }
    
    if (isset($mods_object->originInfo->dateValid))
    {
      foreach ($mods_object->originInfo->dateValid as $dateValid) {
        $mods_array[$i]['value'] = $dateValid;
        $mods_array[$i]['label'] = 'Date Valid';
        $mods_array[$i]['class'] = $dateValid->getName();
        $i++;
      }
    }
    
    if (isset($mods_object->originInfo->dateModified))
    {
      foreach ($mods_object->originInfo->dateModified as $dateModified) {
        $mods_array[$i]['value'] = $dateModified;
        $mods_array[$i]['label'] = 'Date Modified';
        $mods_array[$i]['class'] = $dateModified->getName();
        $i++;
      }
    }
    
    if (isset($mods_object->originInfo->dateOther))
    {
      foreach ($mods_object->originInfo->dateOther as $dateOther) {
        $mods_array[$i]['value'] = $dateOther;
        $mods_array[$i]['label'] = 'Other Date';
        $mods_array[$i]['class'] = $dateOther->getName();
        $i++;
      }
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
        if ($counter == 0 && ($place_of_publication['type'] != 'code')) {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $place_of_publication;
        } elseif($place_of_publication['type'] != 'code') {
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $place_of_publication;
        }
        $counter++;
      }
      $mods_array[$i]['class'] = $mods_object->originInfo->place->placeTerm->getName();
      $i++;
    }
    
    // Physical Description Section
    if (isset($mods_object->physicalDescription->form))
    {
      foreach ($mods_object->physicalDescription->form as $form) {
        $mods_array[$i]['value'] = $form;
        $mods_array[$i]['label'] = 'Physical Form';
        $mods_array[$i]['class'] = $form->getName();
        $i++;
      }
    }

    if (isset($mods_object->physicalDescription->extent))
    {
      foreach ($mods_object->physicalDescription->extent as $extent) {
        $mods_array[$i]['value'] = $extent;
        $mods_array[$i]['label'] = 'Extent';
        $mods_array[$i]['class'] = $extent->getName();
        $i++;
      }
    }
    
    if (isset($mods_object->physicalDescription->note))
    {
      foreach ($mods_object->physicalDescription->note as $note) {
        $mods_array[$i]['value'] = $note;
        $mods_array[$i]['label'] = 'Physical Description';
        $mods_array[$i]['class'] = $note->getName();
        $i++;
      }
    }
    
    // Language Section
    if (isset($mods_object->language))
    {
      foreach ($mods_object->language as $language) {
        switch((string) $language->languageTerm['type']) { // Get attributes as element indices
          case 'text':
            $mods_array[$i]['value'] = $language->languageTerm;
            $mods_array[$i]['label'] = 'Language';
            $mods_array[$i]['class'] = $language->languageTerm->getName() . '_text';
            $i++;
            break;
          default:
        }
      }
    }
    
    // Abstract/Description Section    
    if (isset($mods_object->abstract))
    {
      $description_counter = 0;

      foreach($mods_object->abstract as $abstract) {
        if ($description_counter == 0 && $abstract != '')
        {
          $mods_array['mods:description']['value'] = $abstract;
          
          if(strlen($abstract) > 300)
          {
            $mods_array['mods:description']['short_value'] = substr($abstract, 0, 300) . '...';
          }
          else
          {
            $mods_array['mods:description']['short_value'] = $abstract;
          }
          
          $mods_array['mods:description']['label'] = 'Abstract/Description';
          $mods_array['mods:description']['class'] = $abstract->getName();
          $description_counter++;
        }
        elseif ($abstract != '')
        {
          $mods_array[$i]['value'] = $abstract;
          $mods_array[$i]['label'] = 'Abstract/Description';
          $mods_array[$i]['class'] = $abstract->getName();
          $i++;
        }
      }
    }
    
    // Table of Contents Section    
    if (isset($mods_object->tableOfContents))
    {
      $mods_array[$i]['value'] = $mods_object->tableOfContents;
      $mods_array[$i]['label'] = 'Table of Contents';
      $mods_array[$i]['class'] = $mods_object->tableOfContents->getName();
      $i++;
    }
    
    // Target Audience Section    
    if (isset($mods_object->targetAudience))
    {
      foreach($mods_object->targetAudience as $targetAudience) {
        $mods_array[$i]['value'] = $targetAudience;
        $mods_array[$i]['label'] = 'Target Audience';
        $mods_array[$i]['class'] = $targetAudience->getName();
        $i++;
      }
    }    

    // Identifier Section
    if (isset($mods_object->identifier))
    {
      $count = 0;
      $mods_array['mods:identifier']['value'] = NULL;
      
      foreach ($mods_object->identifier as $identifier) {
        
        if (isset ($identifier['type']) && $identifier != '') {
          
          if($count > 0) {
            $mods_array['mods:identifier']['value'] .= ', ' . trim($identifier) . ' ' . static::addParentheses($identifier['type']);
          }
          else {
            $mods_array['mods:identifier']['value'] .= trim($identifier) . ' ' . static::addParentheses($identifier['type']);
          }  
          
          $mods_array['mods:identifier']['label'] = 'Identifier';
          $mods_array['mods:identifier']['class'] = $identifier->getName() . '_' . $identifier['type'];
        } elseif ($identifier != '') {
          
          if($count > 0) {
            $mods_array['mods:identifier']['value'] .= ', ' . trim($identifier);
          }
          else {
            $mods_array['mods:identifier']['value'] .= trim($identifier);      
          }

          $mods_array['mods:identifier']['label'] = 'Identifier';
          $mods_array['mods:identifier']['class'] = $identifier->getName() . '_id';
        }
        
        $count++;
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
      $total_counter = 0;
      
      $mods_array[$i]['label'] = 'Subject';
      $mods_array[$i]['value'] = '';
      
      foreach ($mods_object->subject as $subject) {
        $j = 0;
        $counter = 0;
        $name_flag = false;
        $title_flag = false;
        $hierarchical_geographic_flag = false;
        $subject_as_type = null;
        $cartographic_info_array = array();
        
        if($total_counter > 0)
        {
          $mods_array[$i]['value'] .= '<br />';
        }
        
        // Check to see if this is a specific type of Subject
        foreach ($subject->children() as $subject_child){
          switch($subject_child->getName()){
            case 'name':
              $name_flag = true;
              $subject_as_type = $subject_child;
              break;
            case 'title':
              $title_flag = true;
              $subject_as_type = $subject;
              break;
            case 'cartographics':
              $cartographic_info_array[$j] = $subject_child;
              $j++;
              break;
            case 'hierarchicalGeographic':
              $hierarchical_geographic_flag = true;
              $subject_as_type = $subject_child;
              break;
            default:
              break;
          }
        }
        
        $total_counter++;
        
        if($name_flag == true)
        {
          static::addMODSName('Subject', $subject_as_type, $mods_array, $i, $total_counter);
        }
        elseif ($title_flag == true && $subject->title != '')
        {
          $title = $subject->title;
          
          // If it has a subtitle, add it
          if(isset($subject->subTitle) && $subject->subTitle != '')
          {
            $title = $title . ': ' . $subject->subTitle;
          }
          
          // If it has a NonSort element, add it before the title
          if(isset($subject->nonSort) && $subject->nonSort != '')
          {
            $title = $subject->nonSort . ' ' . $title;
          }
        
          // If it has a Part Number, add it
          if(isset($subject->partNumber) && $subject->partNumber != '')
          {
            foreach ($subject->partNumber as $partNumber) {
              $title = $title . ' ' . $partNumber;
            } 
          }
          
          // If it has a Part Name, add it
          if(isset($subject->partName) && $subject->partName != '')
          {
            foreach ($subject->partName as $partName) {
              $title = $title . ' ' . $partName;
            } 
          }
          
          $mods_array[$i]['value'] .= '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . $title . ')">' . $title . '</a>';
        }
        elseif ($hierarchical_geographic_flag == true)
        {
          $temp_subject = NULL;

          foreach ($subject_as_type->children() as $subject_child) {
            if ($counter > 0 && $subject_child != '') {
              $temp_subject = $temp_subject . ' -- ' . $subject_child;
            } elseif ($subject_child != '') {
              $temp_subject = $temp_subject . $subject_child;
            }
            $counter++;
          }

          $temp_subject = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . $temp_subject . ')">' . $temp_subject . '</a>';
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $temp_subject;
        }
        else
        {
          $temp_subject = NULL;

          foreach ($subject->children() as $subject_child) {
            if ($counter > 0 && $subject_child != '') {
              $temp_subject = $temp_subject . ' -- ' . $subject_child;
            } elseif ($subject_child != '') {
              $temp_subject = $temp_subject . $subject_child;
            }
            $counter++;
          }
 
          $temp_subject = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . $temp_subject . ')">' . $temp_subject . '</a>';
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $temp_subject;
        }
        
        /*foreach($cartographic_info_array as $cartographic_info)
        {
          if (isset($cartographic_info->coordinates) && trim($cartographic_info->coordinates) != '')
          {
            $mods_array[$i]['value'] = $cartographic_info->coordinates;
            $mods_array[$i]['label'] = 'Coordinates';
            $mods_array[$i]['class'] = $cartographic_info->coordinates->getName();
            $i++;
          }
          
          if (isset($cartographic_info->scale) && $cartographic_info->scale != '')
          {
            $mods_array[$i]['value'] = $cartographic_info->scale;
            $mods_array[$i]['label'] = 'Scale';
            $mods_array[$i]['class'] = $cartographic_info->scale->getName();
            $i++;
          }
          
          if (isset($cartographic_info->projection) && $cartographic_info->projection != '')
          {
            $mods_array[$i]['value'] = $cartographic_info->projection;
            $mods_array[$i]['label'] = 'Projection';
            $mods_array[$i]['class'] = $cartographic_info->projection->getName();
            $i++;
          }
        }*/
      }
      
      $mods_array[$i]['class'] = $mods_object->subject->getName();
      $i++;
    }
    
    // Library Classification Section    
    if (isset($mods_object->classification))
    {
      foreach($mods_object->classification as $classification) {
        $mods_array[$i]['value'] = $classification;
        $mods_array[$i]['label'] = 'Library Classification';
        $mods_array[$i]['class'] = $classification->getName();
        $i++;
      }
    }    
    
    // Location Section    
    if (isset($mods_object->location))
    {
      foreach($mods_object->location as $location) {
        if(isset($location->physicalLocation))
        {
          $mods_array[$i]['value'] = $location->physicalLocation;
          $mods_array[$i]['label'] = 'Held by';
          $mods_array[$i]['class'] = $location->physicalLocation->getName();
          $i++;
        }
        
        if(isset($location->url) && $location->url != '')
        {
          if(isset($location->url['displayLabel']) && $location->url['displayLabel'] != '')
          { 
            $mods_array[$i]['value'] = '<a href="' . $location->url . '" target="_blank">' . $location->url['displayLabel'] . '</a>';
          }
          else
          {
            $mods_array[$i]['value'] = '<a href="' . $location->url . '" target="_blank">' . $location->url . '</a>';
          }

          if($location['displayLabel'] == 'purl')
          {
            $mods_array[$i]['label'] = 'Persistent Link to This Record';
          }
          else
          {
            $mods_array[$i]['label'] = 'Links';        
          }
    
          $mods_array[$i]['class'] = $location->url->getName();
          $i++;
        }
      }
    }   
    
    // Access Condition Section    
    if (isset($mods_object->accessCondition))
    {
      foreach($mods_object->accessCondition as $accessCondition) {
        $mods_array[$i]['value'] = $accessCondition;
        $mods_array[$i]['label'] = 'Restrictions on Access';
        $mods_array[$i]['class'] = $accessCondition->getName();
        $i++;
      }
    } 
    
    // Owning Institution Section    
    if (isset($mods_object->extension))
    {
      foreach ($mods_object->extension as $extension) 
      {
        $namespaces = $extension->getNameSpaces(true);
        $flvc = $extension->children($namespaces['flvc']);
        
        $mods_array['mods:owner_inst']['value'] = $flvc->flvc->owningInstitution;
        $mods_array['mods:owner_inst']['label'] = 'Owner Institution';
        $mods_array['mods:owner_inst']['class'] = $flvc->flvc->owningInstitution->getName();
        $i++;
      }
    }
   
    // Other Logo Section
    if (isset($mods_object->extension))
    {
      $local_counter = 0;
      
      foreach ($mods_object->extension as $extension)
      {
        $namespaces = $extension->getNameSpaces(true);
        $flvc = $extension->children($namespaces['flvc']);

        foreach($flvc->flvc->otherLogo as $otherLogo) {
          $mods_array['mods:other_logo_' . $local_counter]['value'] = $otherLogo;
          $mods_array['mods:other_logo_' . $local_counter]['label'] = 'Other Logo ' . $local_counter;
          $mods_array['mods:other_logo_' . $local_counter]['class'] = $otherLogo->getName();
          $local_counter++;
        }
      }
    }
    
   // Related Item Section
    if (isset($mods_object->relatedItem))
    {
      foreach ($mods_object->relatedItem as $relatedItem) {
        if (isset($relatedItem->titleInfo))
        {
          $title = static::formatTitleInfo($relatedItem->titleInfo);
          
          static::traverseRelatedItem($relatedItem, $title);
          
          switch((string) $relatedItem['type']) { // Get attributes as element indices
            case 'preceding':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Preceding Title';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_preceding';
              $i++;
              break;
            case 'succeeding':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Succeeding Title';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_succeeding';
              $i++;
              break;
            case 'original':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Original Title';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_original';
              $i++;
              break;
            case 'host':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Is Part Of';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_host';
              $i++;
              break;
            case 'constituent':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Has Part';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_constituent';
              $i++;
              break;
            case 'series':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Is Part of Series';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_series';
              $i++;
              break;
            case 'otherVersion':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Other Version';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_otherversion';
              $i++;
              break;
            case 'otherFormat':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Other Format';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_otherformat';
              $i++;
              break;
            case 'isReferencedBy':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Is Referenced By';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_isreferencedby';
              $i++;
              break;
            case 'references':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'References';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_references';
              $i++;
              break;
            case 'reviewOf':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Review Of';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName() . '_reviewof';
              $i++;
              break; 
            default:
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Related Title';
              $mods_array[$i]['class'] = $relatedItem->titleInfo->title->getName();
              $i++;
          }
        }
      }
    }    
 
    return $mods_array;
  }
  
  protected static function traverseRelatedItem($simple_xml_object, &$value)
  {
    foreach($simple_xml_object->children() as $child) {
      
      if($child->getName() == 'url')
      {
        if(isset($child['displayLabel']))
        {
          $string = '<a href="' . $child . '" target="_blank">' . $child['displayLabel'] . '</a>';
        }
        else
        {
          $string = '<a href="' . $child . '" target="_blank">' . $child . '</a>';
        }
        
        if(isset($child['note']))
        {
          $string .= ' ' . $child['note'];
        }
        
        static::spaceCheck($string, $value);
      } 

      if($child->getName() == 'physicalLocation')
      {
        $string = $child;
        static::spaceCheck($string, $value);
      }
      
      if($child->getName() == 'name')
      {
        $string = '';
        
        foreach ($child->namePart as $namePart) {
          if($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'family')
          {
            $string = $namePart . ', ' . $string;
          }
          elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'date')
          {
            $string = $name_value . ', ' . $string;
          }
          elseif ($namePart != '')
          {
            $string = $string . ' ' . $namePart;
          }
        }
        
        $string = '<a href="' . base_path() . 'islandora/search/catch_all_names_mt%3A(' . $string . ')">' . $string . '</a>';
        
        if(isset($child->role))
        {
          foreach ($child->role as $role) {
            if(isset($role->roleTerm) && ($role->roleTerm['type'] == 'text') && ($role->roleTerm != ''))
            {
              $string = $string . ', ' . $role->roleTerm;
            }
          } 
        }
        
        static::spaceCheck($string, $value);
      }
     
      if($child->getName() != 'titleInfo' && $child->getName != 'name')
      {
        static::traverseRelatedItem($child, $value);
      }
    }
  }

  //This function ensures there is a new line tag after each line
  protected static function spaceCheck($string, &$value)
  {
    $space_check = substr($value, -6);
    
    if ($space_check != '<br />') {
        $value .= '<br />' . $string;
    }
    else {
        $value .= $string;
    }
  }
  
  protected static function formatTitleInfo($titleInfo)
  {
    $title = $titleInfo->title;
    
    // If it has a subtitle, add it
    if(isset($titleInfo->subTitle))
    {
      $subTitle = static::addPeriod($titleInfo->subTitle);
      $title = static::addColon($title) . ' ' . $subTitle;
    }
          
    // If it has a NonSort element, add it before the title
    if(isset($titleInfo->nonSort))
    {
      $title = $titleInfo->nonSort . ' ' . $title;
    }
          
    // If it has a Display Label attribute, add it before the NonSort and Title
    if(isset($titleInfo['displayLabel']))
    {
      $title = static::addColon($titleInfo['displayLabel']) . ' ' . $title;
    }
          
    // If it has a Part Number, add it
    if(isset($titleInfo->partNumber))
    {
      foreach ($titleInfo->partNumber as $partNumber) {
        $partNumber = static::addPeriod($partNumber);
        $title = $title . ' ' . $partNumber;
      } 
    }
          
    // If it has a Part Name, add it
    if(isset($titleInfo->partName))
    {
      foreach ($titleInfo->partName as $partName) {
        $partName = static::addPeriod($partName);
        $title = $title . ' ' . $partName;
      } 
    }

    if ($title != '')
    {
      $title = static::addPeriod($title);
    }
    
    return $title;
  }
  
  protected static function addMODSName($label, $name, &$mods_array, &$i, $randomize_name = NULL)
  {
    $name_value = '';
    
    // If it has a Name Part, add it
    if(isset($name->namePart))
    {
      foreach ($name->namePart as $namePart) {
        if($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'family')
        {
          $name_value = $namePart . ', ' . $name_value;
        }
        elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'date')
        {
          $name_value = $name_value . ', ' . $namePart;
        }
        elseif ($namePart != '')
        {
          $name_value = $name_value . ' ' . $namePart;
        }
      } 
    }
    
    if($randomize_name == NULL) {
      $name_value = '<a href="' . base_path() . 'islandora/search/catch_all_names_mt%3A(' . $name_value . ')">' . $name_value . '</a>';
    }  
        
    // If it has a Role, add it
    if(isset($name->role))
    {
      foreach ($name->role as $role) {
        if(isset($role->roleTerm) && ($role->roleTerm['type'] == 'text') && ($role->roleTerm != ''))
        {
          $name_value = $name_value . ', ' . $role->roleTerm;
        }
      } 
    }
    
    if($randomize_name == NULL) {
      $mods_array[$i]['value'] = $name_value;
      $mods_array[$i]['label'] = $label;
      $mods_array[$i]['class'] = $name->getName();
    }
    else {
      $add_to_array = $i . '_subject_' . $randomize_name;
      $mods_array[$add_to_array]['value'] = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . $name_value . ')">' . $name_value . '</a>';
      $mods_array[$add_to_array]['label'] = $label;
      $mods_array[$add_to_array]['class'] = $add_to_array;
    }
  }
  
  // if there isn't a period at the end of $string, add one
  protected static function addPeriod($string)
  { 
    if (substr($string, -1) != '.')
    {
      $string.= '.';
    }
    
    return $string;
  }
  
  // if there isn't a colon at the end of $string, add one
  protected static function addColon($string)
  {
    if (substr($string, -1) != ':')
    {
      $string.= ':';
    }
    
    return $string;
  }

  // if there isn't a parentheses at the beginning and end of $string, add one
  protected static function addParentheses($string)
  {
    if($string == '')
    {
      return $string;
    }
    
    if (substr($string, 0, 1) != '(')
    {
      $string = '(' . $string;
    }

    if (substr($string, -1) != ')')
    {
      $string.= ')';
    }
    
    return $string;
  }
}



