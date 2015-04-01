<?php

/**
 * @file
 * Implements a simple class for working with MODS data
 */
require_once('mods_origin_info.inc');

/**
 * Mods Class
 */
class MODS {

  /* This function takes a SimpleXML object as input and returns an array with all the pertinent elements/attributes */
  function as_formatted_array($mods_object) {
    $mods_array = array();
    $i = 0;
    $counter = 0;
    $label = NULL;

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
            $subTitle = addPeriod($titleInfo->subTitle);
            $title = addColon($title) . ' ' . $subTitle;
          }
          
          // If it has a NonSort element, add it before the title
          if(isset($titleInfo->nonSort))
          {
            $title = $titleInfo->nonSort . ' ' . $title;
          }
          
          // If it has a Part Number, add it
          if(isset($titleInfo->partNumber))
          {
            foreach ($titleInfo->partNumber as $partNumber) {
              $partNumber = addPeriod($partNumber);
              $title = $title . ' ' . $partNumber;
            } 
          }
          
          // If it has a Part Name, add it
          if(isset($titleInfo->partName))
          {
            foreach ($titleInfo->partName as $partName) {
              $partName = addPeriod($partName);
              $title = $title . ' ' . $partName;
            } 
          }
          
          if ($title != '')
          {
            $title = addPeriod($title);
          }
          
          // If it has a Display Label attribute change the label variable
          if(isset($titleInfo['displayLabel']))
          {
            $label = $titleInfo['displayLabel'];
          }          
          
          switch((string) $titleInfo['type']) { // Get attributes as element indices
            case 'abbreviated':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = isset($label) ? $label : 'Abbreviated Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_abbreviated';
              $i++;
              break;
            case 'alternative':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = isset($label) ? $label : 'Alternative Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_alternative';
              $i++;
              break;
            case 'translated':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = isset($label) ? $label : 'Translated Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_translated';
              $i++;
              break;
            case 'uniform':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = isset($label) ? $label : 'Uniform Title';
              $mods_array[$i]['class'] = $titleInfo->title->getName() . '_uniform';
              $i++;
              break;          
            default:
              $mods_array['mods:title']['value'] = $title;
              $mods_array['mods:title']['label'] = isset($label) ? $label : 'Title';
              $mods_array['mods:title']['class'] = $titleInfo->title->getName();
              $i++;
          }
        }
      }
    }
    
    // Name Section
    if (isset($mods_object->name))
    {
      $mods_array[$i]['label'] = 'Name(s)';
      $mods_array[$i]['value'] = '';
      $total_counter = 0;
      foreach ($mods_object->name as $name) {
        if ($total_counter == 0) {
          $mods_array[$i]['class'] = $name->getName();
        }
        if ($total_counter > 0)
        {
          $mods_array[$i]['value'] .= '<br />';
        }
        static::addMODSName('Name', $name, $mods_array, $i);
        //$i++;
        $total_counter++;
      }
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
      
    // Origin Info Section
    processOriginInfo($mods_object, $mods_array, $i);
    
    // Physical Description Section
    if (isset($mods_object->physicalDescription->form))
    {
      $form_marccategory = '';
      $form_no_authority = '';
      foreach ($mods_object->physicalDescription->form as $form) {
        if (!isset($form['authority'])) {
          // only display the first one
          if (strlen($form_no_authority) == 0) $form_no_authority = $form;
        }
        else if ($form['authority'] == 'marccategory') {
          // only display the first one
          if (strlen($form_marccategory) == 0) $form_marccategory = $form;
        }
      }
      if (strlen($form_marccategory) > 0) {
        $mods_array[$i]['value'] = $form_marccategory;
        $mods_array[$i]['label'] = 'Physical Form';
        $mods_array[$i]['class'] = $form->getName();
        $i++;
      }
      else if (strlen($form_no_authority) > 0) {
        $mods_array[$i]['value'] = $form_no_authority;
        $mods_array[$i]['label'] = 'Physical Form';
        $mods_array[$i]['class'] = $form->getName();
        $i++;
      }
      else {
        foreach ($mods_object->physicalDescription->form as $form) {
          $mods_array[$i]['value'] = $form;
          $mods_array[$i]['label'] = 'Physical Form';
          $mods_array[$i]['class'] = $form->getName();
          $i++;
        }
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
      $lang_display = '';
      foreach ($mods_object->language as $language) {
        $lang_code = '';
        $lang_text = '';
        foreach ($language->languageTerm as $languageterm) {
          switch((string) $languageterm['type']) { // Get attributes as element indices
            case 'text':
              //$mods_array[$i]['value'] = $language->languageTerm;
              //$mods_array[$i]['label'] = 'Language';
              //$mods_array[$i]['class'] = $language->languageTerm->getName() . '_text';
              //$i++;
              $lang_text = $languageterm;
              break;
            case 'code':
              $lang_code = $languageterm;
              break;
            default:
          }
        }
        if ((strlen($lang_display) > 0)&&((strlen($lang_text) > 0)||(strlen($lang_code) > 0)))
          $lang_display .= '<br>';
        if (strlen($lang_text) > 0)
          $lang_display .= $lang_text;
        else if (strlen($lang_code) > 0)
          $lang_display .= $lang_code;
      }
      $mods_array[$i]['value'] = $lang_display;
      $mods_array[$i]['label'] = 'Language(s)';
      $mods_array[$i]['class'] = $language->languageTerm->getName() . '_text';
      $i++;
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
          
          if(isset($abstract['displayLabel']))
          {
            $mods_array['mods:description']['label'] = $abstract['displayLabel'];
          }
          else
          {
            $mods_array['mods:description']['label'] = 'Abstract/Description';  
          }
 
          $mods_array['mods:description']['class'] = $abstract->getName();
          $description_counter++;
        }
        elseif ($abstract != '')
        {
          $mods_array[$i]['value'] = $abstract;

          if(isset($abstract['displayLabel']))
       	  {
            $mods_array[$i]['label'] = $abstract['displayLabel'];
          }
       	  else
       	  {
            $mods_array[$i]['label'] = 'Abstract/Description';
       	  }

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
            $mods_array['mods:identifier']['value'] .= ', ' . trim($identifier) . ' ' . addParentheses($identifier['type']);
          }
          else {
            $mods_array['mods:identifier']['value'] .= trim($identifier) . ' ' . addParentheses($identifier['type']);
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
      // display notes with displayLabel first
      foreach ($mods_object->note as $note) {
        
        $found = false;
        
        switch((string) $note['type']) { // Get attributes as element indices
          case 'rights':
            break;
          case 'statementOfResponsibility':
            break;
          default:
            if(isset($note['displayLabel'])) {     
              foreach($mods_array as &$array_element) {
                if((string)$array_element['label'] == (string)$note['displayLabel']) {
                  $array_element['value'] = $array_element['value'] . '<br />' . $note;
                  $found = true;
                }
                
                echo var_dump($array_element);
              }
              
              if(!$found) {
                $mods_array[$i]['value'] = $note;
                $mods_array[$i]['label'] = $note['displayLabel'];
                $mods_array[$i]['class'] = $note->getName() . '_text';
                $i++;
              }
            }
        }
      }

      // group other notes under single label
      $note_value = '';
      $total_counter = 0;
      foreach ($mods_object->note as $note) {
        switch((string) $note['type']) { // Get attributes as element indices
          case 'rights':
            break;
          case 'statementOfResponsibility':
            break;
          default:
            if(!isset($note['displayLabel'])) {
              if ($total_counter > 0) {
                $note_value .= '<br />';
              }
              $note_value .= $note;
              $total_counter++;
              //$mods_array[$i]['value'] = $note;
              //$mods_array[$i]['label'] = 'Note';
              $mods_array[$i]['class'] = $note->getName() . '_text';
              //$i++;
            }
        }
      }
      if (strlen($note_value) > 0) {
        $mods_array[$i]['label'] = 'Note(s)';
        $mods_array[$i]['value'] = $note_value;
        $i++;
      }
    }
    
    // Subject Section
    if (isset($mods_object->subject))
    {
      $total_counter = 0;

      $mods_array[$i]['label'] = 'Subject(s)';
      $mods_array[$i]['value'] = '';
      
      foreach ($mods_object->subject as $subject) {
        $j = 0;
        $counter = 0;
        $name_flag = false;
        $title_flag = false;
        $hierarchical_geographic_flag = false;
        $geographic_code_flag = false;
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
            case 'titleInfo':
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
            case 'geographicCode':
              $geographic_code_flag = true;
              $subject_as_type = $subject_child;
              break;
            default:
              break;
          }
        }
        
        //$total_counter++;
        
        if($name_flag == true)
        {
          $name_value = '';
          $name_date = '';
          $name_termsofaddress = '';

          // If it has a Name Part, add it
          if(isset($subject->name->namePart))
          {
            foreach ($subject->name->namePart as $namePart) {
              if($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'family')
              {
                $name_value = $namePart . ', ' . $name_value;
              }
	      elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'date')
              {
                $name_date = $name_date . ', ' . $namePart;
              }
              elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'termsOfAddress')
              {
                $name_termsofaddress = $name_termsofaddress . ', ' . $namePart;
              }
	      elseif ($namePart != '')
              {
                if (strlen($name_value) > 0) {
                  $name_value = $name_value . ' ';
                }
                $name_value = $name_value . $namePart;
              }
            }
            if (strlen($name_termsofaddress) > 0) {
              $name_value = $name_value . $name_termsofaddress;
            }
            if (strlen($name_date) > 0) {
              $name_value = $name_value . $name_date;
            }
          }
          if (isset($subject->name->titleInfo->title)) {
            $name_value = $name_value . ' -- ' . $subject->name->titleInfo->title;
          }

          $name_value = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . static::stripSolrValue($name_value) . ')">' . $name_value . '</a>';

          // If it has a Role, add it
          if(isset($subject->name->role))
          {
            foreach ($subject->name->role as $role) {
              if(isset($role->roleTerm) && ($role->roleTerm['type'] == 'text') && ($role->roleTerm != ''))
              {
                $name_value = $name_value . ', ' . $role->roleTerm;
              }
            }
          }

          $mods_array[$i]['value'] .= $name_value;
        }
        elseif ($title_flag == true && $subject->titleInfo->title != '')
        {
          $title = $subject->titleInfo->title;
          
          // If it has a subtitle, add it
          if(isset($subject->titleInfo->subTitle) && $subject->titleInfo->subTitle != '')
          {
            $title = $title . ': ' . $subject->titleInfo->subTitle;
          }
          
          // If it has a NonSort element, add it before the title
          if(isset($subject->titleInfo->nonSort) && $subject->titleInfo->nonSort != '')
          {
            $title = $subject->titleInfo->nonSort . ' ' . $title;
          }
        
          // If it has a Part Number, add it
          if(isset($subject->titleInfo->partNumber) && $subject->titleInfo->partNumber != '')
          {
            foreach ($subject->titleInfo->partNumber as $partNumber) {
              $title = $title . ' ' . $partNumber;
            } 
          }
          
          // If it has a Part Name, add it
          if(isset($subject->titleInfo->partName) && $subject->titleInfo->partName != '')
          {
            foreach ($subject->titleInfo->partName as $partName) {
              $title = $title . ' ' . $partName;
            } 
          }
          
          $mods_array[$i]['value'] .= '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . static::stripSolrValue($title) . ')">' . $title . '</a>';
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

          $temp_subject = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . static::stripSolrValue($temp_subject) . ')">' . $temp_subject . '</a>';
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $temp_subject;
        }
        else if ($geographic_code_flag == true)
        {
          // do nothing
          continue;
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
 
          $temp_subject = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . static::stripSolrValue($temp_subject) . ')">' . $temp_subject . '</a>';
          $mods_array[$i]['value'] = $mods_array[$i]['value'] . $temp_subject;

        }
/* removing for now       
        // If the Subject has a genre, add it
        if(isset($subject->genre) && $subject->genre != '')
        {
          foreach ($subject->genre as $genre) {
            $mods_array[$i]['value'] = $mods_array[$i]['value'] . ' -- ' . $genre;
          }
        }
*/
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

        $total_counter++;
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
        
        if(isset($location->holdingSimple->copyInformation->subLocation) && $location->holdingSimple->copyInformation->subLocation != '')
        {
          $sublocation_counter = 0;

          foreach($location->holdingSimple->copyInformation->subLocation as $sublocation)
          {
            if($sublocation_counter == 0)
            {
              $mods_array[$i]['value'] = $sublocation;
              $mods_array[$i]['label'] = 'Sublocation';
              $mods_array[$i]['class'] = 'sublocation';
              $sublocation_counter++;
            }
            else
            {
              $mods_array[$i]['value'] = $mods_array[$i]['value'] . '<br />' . $sublocation;
            }
          }

          $i++;
        }
      }
    }   
    
    // Access Condition Section    
    if (isset($mods_object->accessCondition))
    {
      foreach($mods_object->accessCondition as $accessCondition) {

        if(isset($mods_object->accessCondition['type']))
        {
          switch((string) $accessCondition['type']) { // Get attributes as element indices
            case 'useAndReproduction':
              $mods_array[$i]['value'] = $accessCondition;
              $mods_array[$i]['label'] = 'Use and Reproduction';
              $mods_array[$i]['class'] = $accessCondition->getName() . '_reproduction';
              $i++;
              break;
            case 'use and reproduction':
              $mods_array[$i]['value'] = $accessCondition;
              $mods_array[$i]['label'] = 'Use and Reproduction';
              $mods_array[$i]['class'] = $accessCondition->getName() . '_reproduction';
              $i++;
              break;
            default:
              $mods_array[$i]['value'] = $accessCondition;
              $mods_array[$i]['label'] = 'Restrictions on Access';
              $mods_array[$i]['class'] = $accessCondition->getName();
              $i++;
          }
        }
        else
        {
          $mods_array[$i]['value'] = $accessCondition;
          $mods_array[$i]['label'] = 'Restrictions on Access';
          $mods_array[$i]['class'] = $accessCondition->getName();
          $i++;
        }
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
   
    // Related Item Section
    if (isset($mods_object->relatedItem))
    {
      foreach ($mods_object->relatedItem as $relatedItem) {
        //if (isset($relatedItem->titleInfo))
        //{
          $title = '';
          $class_name = $relatedItem->getName();
          if (isset($relatedItem->titleInfo))
          {
            $title = static::formatTitleInfo($relatedItem->titleInfo);
            $class_name = $relatedItem->titleInfo->title->getName();
          }

          if (isset($relatedItem['displayLabel']))
          {
            $title = $title . " (" . $relatedItem['displayLabel'] . ")";
          }
 
          static::traverseRelatedItem($relatedItem, $title);

          if (isset($relatedItem->identifier))
          {
            $title = $title . "<br />" . $relatedItem->identifier;
          }
          
          switch((string) $relatedItem['type']) { // Get attributes as element indices
            case 'preceding':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Preceding Title';
              $mods_array[$i]['class'] = $class_name . '_preceding';
              $i++;
              break;
            case 'succeeding':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Succeeding Title';
              $mods_array[$i]['class'] = $class_name . '_succeeding';
              $i++;
              break;
            case 'original':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Original Title';
              $mods_array[$i]['class'] = $class_name . '_original';
              $i++;
              break;
            case 'host':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Is Part Of';
              $mods_array[$i]['class'] = $class_name . '_host';
              $i++;
              break;
            case 'constituent':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Has Part';
              $mods_array[$i]['class'] = $class_name . '_constituent';
              $i++;
              break;
            case 'series':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Is Part of Series';
              $mods_array[$i]['class'] = $class_name . '_series';
              $i++;
              break;
            case 'otherVersion':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Other Version';
              $mods_array[$i]['class'] = $class_name . '_otherversion';
              $i++;
              break;
            case 'otherFormat':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Other Format';
              $mods_array[$i]['class'] = $class_name . '_otherformat';
              $i++;
              break;
            case 'isReferencedBy':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Is Referenced By';
              $mods_array[$i]['class'] = $class_name . '_isreferencedby';
              $i++;
              break;
            case 'references':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'References';
              $mods_array[$i]['class'] = $class_name . '_references';
              $i++;
              break;
            case 'reviewOf':
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Review Of';
              $mods_array[$i]['class'] = $class_name . '_reviewof';
              $i++;
              break; 
            default:
              $mods_array[$i]['value'] = $title;
              $mods_array[$i]['label'] = 'Related Title';
              $mods_array[$i]['class'] = $class_name;
              $i++;
          }
        //}
        if ((isset($relatedItem->part))&&(isset($relatedItem->part->detail)))
        {
          $issue_display = '';
          $issue_title = '';
          $detail_count = count($relatedItem->part->detail);
          for ($d=$detail_count-1; $d>=0; $d--)
          {
              $detail = $relatedItem->part->detail[$d];
              if ((strlen($issue_display) > 0)&&((isset($detail->caption))||(isset($detail->number))))
                $issue_display .= ', ';
              if (isset($detail->caption))
                $issue_display .= $detail->caption;
              if (isset($detail->number))
                $issue_display .= ' ' . $detail->number;
              if (isset($detail->title))
              {
                  $issue_title = $detail->title;
              }
          }
          if (strlen($issue_title) > 0)
          {
              $issue_display = $issue_title . '. ' . $issue_display;
          }
          if (strlen($issue_display) > 0)
          {
              $mods_array[$i]['value'] = $issue_display;
              $mods_array[$i]['label'] = 'Issue';
              $mods_array[$i]['class'] = $detail->getName();
              $i++;
          }
        }
      }
    }    
 
    return $mods_array;
  }

  /* This function takes a SimpleXML object as input and returns an array with the other logo information */
  function other_logo_array($mods_object) {
    $other_logo_array = array();

    // Other Logo Section
    if (isset($mods_object->extension))
    {
      $local_counter = 0;

      foreach ($mods_object->extension as $extension)
      {
        $namespaces = $extension->getNameSpaces(true);
        $flvc = $extension->children($namespaces['flvc']);

        foreach($flvc->flvc->otherLogo as $otherLogo) {
          $other_logo_array['mods:other_logo_' . $local_counter]['value'] = $otherLogo;
          $other_logo_array['mods:other_logo_' . $local_counter]['label'] = 'Other Logo ' . $local_counter;
          $other_logo_array['mods:other_logo_' . $local_counter]['class'] = $otherLogo->getName();
          $local_counter++;
        }
      }
    }

    return $other_logo_array;
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
        $name_date = '';
        $name_termsofaddress = '';
 
        foreach ($child->namePart as $namePart) {
          if($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'family')
          {
            $string = $namePart . ', ' . $string;
          }
          elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'date')
          {
            $name_date = $name_date . ', ' . $namePart;
          }
          elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'termsOfAddress')
          {
            $name_termsofaddress = $name_termsofaddress . ', ' . $namePart;
          }
          elseif ($namePart != '')
          {
            $string = $string . ' ' . $namePart;
          }
        }
        if (strlen($name_termsofaddress) > 0) {
          $string = $string . $name_termsofaddress;
        }
        if (strlen($name_date) > 0) {
          $string = $string . $name_date;
        }
        
        $string = '<a href="' . base_path() . 'islandora/search/catch_all_names_mt%3A(' . static::stripSolrValue($string) . ')">' . $string . '</a>';
        
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
    
    if ((strlen($value) > 0)&&($space_check != '<br />')) {
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
      $subTitle = addPeriod($titleInfo->subTitle);
      $title = addColon($title) . ' ' . $subTitle;
    }
          
    // If it has a NonSort element, add it before the title
    if(isset($titleInfo->nonSort))
    {
      $title = $titleInfo->nonSort . ' ' . $title;
    }
          
    // If it has a Display Label attribute, add it before the NonSort and Title
    if(isset($titleInfo['displayLabel']))
    {
      $title = addColon($titleInfo['displayLabel']) . ' ' . $title;
    }
          
    // If it has a Part Number, add it
    if(isset($titleInfo->partNumber))
    {
      foreach ($titleInfo->partNumber as $partNumber) {
        $partNumber = addPeriod($partNumber);
        $title = $title . ' ' . $partNumber;
      } 
    }
          
    // If it has a Part Name, add it
    if(isset($titleInfo->partName))
    {
      foreach ($titleInfo->partName as $partName) {
        $partName = addPeriod($partName);
        $title = $title . ' ' . $partName;
      } 
    }

    if ($title != '')
    {
      $title = addPeriod($title);
    }
    
    return $title;
  }
  
  protected static function addMODSName($label, $name, &$mods_array, &$i, $randomize_name = NULL)
  {
    $name_value = '';
    $name_date = '';
    $name_termsofaddress = '';

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
          $name_date = $name_date . ', ' . $namePart;
        }
        elseif ($namePart != '' && isset($namePart['type']) && $namePart['type'] == 'termsOfAddress')
        {
          $name_termsofaddress = $name_termsofaddress . ', ' . $namePart;
        }
        elseif ($namePart != '')
        {
          $name_value = $name_value . ' ' . $namePart;
        }
      } 
      if (strlen($name_termsofaddress) > 0) {
        $name_value = $name_value . $name_termsofaddress;
      }
      if (strlen($name_date) > 0) {
        $name_value = $name_value . $name_date;
      }
    }
    
    if($randomize_name == NULL) {
      $name_value = '<a href="' . base_path() . 'islandora/search/catch_all_names_mt%3A(' . static::stripSolrValue($name_value) . ')">' . $name_value . '</a>';
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
      $mods_array[$i]['value'] .= $name_value;
      //$mods_array[$i]['label'] = $label;
      //$mods_array[$i]['class'] = $name->getName();
    }
    else {
      $add_to_array = $i . '_subject_' . $randomize_name;
      $mods_array[$add_to_array]['value'] = '<a href="' . base_path() . 'islandora/search/catch_all_subjects_mt%3A(' . static::stripSolrValue($name_value) . ')">' . $name_value . '</a>';
      $mods_array[$add_to_array]['label'] = $label;
      $mods_array[$add_to_array]['class'] = $add_to_array;
    }
  }

  protected static function stripSolrValue($string)
  {
    $match = array('&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';');
    $replace = array(' ', '', '', '', '', '', '', '', '', '', '', '', '', ' ', '', ' ');
    $string = str_replace($match, $replace, $string);

    return $string;
  }
}



