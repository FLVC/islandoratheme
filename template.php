<?php

/**
 * @file
 * Template.php - process theme data for your sub-theme.
 * 
 * Rename each function and instance of "footheme" to match
 * your subthemes name, e.g. if you name your theme "footheme" then the function
 * name will be "footheme_preprocess_hook". Tip - you can search/replace
 * on "footheme".
 */

require_once 'includes/islandora_mods.inc';

/*
 * Custom function to set some variables used throughout the theme
 */
function islandoratheme_variables(&$vars) {
  $vars['default_brand_logo'] = 'default_logo.png';
  $vars['default_brand_link'] = 'http://www.flvc.org';
  return $vars;
}

/**
 * Override or insert variables for the html template.
 */
function islandoratheme_preprocess_html(&$vars) {
  drupal_add_library ('system', 'ui.tabs');
  drupal_add_js('jQuery(document).ready(function(){jQuery("#tabs").tabs();});', 'inline');
  drupal_add_js('jQuery(document).ready(function(){collectionBlankSearch();});', 'inline');
}

function islandoratheme_process_html(&$vars) {
}
// */

/**
 * Override the Islandora Basic Image preprocess function
 */
function islandoratheme_preprocess_islandora_basic_image(&$variables) {

  // base url
  global $base_url;
  // base path
  global $base_path;
  
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/basic-image.css', array('group' => CSS_THEME, 'type' => 'file'));
  
  // Create the full view link
  $variables['islandora_view_link'] = '<a href="' . $base_url . request_uri() . '/datastream/OBJ/view' . '">Full Screen View</a>';

  $islandora_object = $variables['islandora_object'];
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
 
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array(); 

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);
}

/**
 * Override the Islandora Audio preprocess function
 */
function islandoratheme_preprocess_islandora_audio(&$variables) {
  
  // base url
  global $base_url;
  // base path
  global $base_path;

  // Create the full view link
  $variables['islandora_view_link'] = '<a href="' . $base_url . request_uri() . '/datastream/OBJ/view' . '">Full Screen View</a>';
  $variables['islandora_full_url'] = $base_url . request_uri() . '/datastream/OBJ/view';  

  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/audio.css', array('group' => CSS_THEME, 'type' => 'file'));

  $islandora_object = $variables['islandora_object'];
  $repository = $islandora_object->repository;
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
  
  $variables['islandora_object_label'] = $islandora_object->label;
  
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array(); 

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

 // Start getting parameters for the player...
  $audio_params = array(
    "pid" => $islandora_object->id,
  );
  // Thumbnail.
  if (isset($islandora_object['TN']) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['TN'])) {
    $tn_url = url("islandora/object/{$islandora_object->id}/datastream/TN/view");
    $params = array(
      'title' => $islandora_object->label,
      'path' => $tn_url,
    );
    $variables['islandora_thumbnail_img'] = theme('image', $params);

    $audio_params += array(
      'tn' => $tn_url,
    );
  }
  
  // Audio player.
  if (isset($islandora_object['PROXY_MP3']) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PROXY_MP3'])) {
    $audio_url = url("islandora/object/{$islandora_object->id}/datastream/PROXY_MP3/view", array('absolute' => TRUE));

    $audio_params += array(
      "url" => $audio_url,
      "mime" => 'audio/mpeg',
    );
  }

  module_load_include('inc', 'islandora', 'includes/solution_packs');
  $viewer = islandora_get_viewer($audio_params, 'islandora_audio_viewers', $islandora_object);

  if ($viewer) {
    $variables['islandora_content'] = $viewer;
  }
  elseif (isset($variables['islandora_thumbnail_img']) && isset($islandora_object['PROXY_MP3']) &&
    islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PROXY_MP3'])) {

    $variables['islandora_content'] = l($variables['islandora_thumbnail_img'], $audio_url, array('html' => TRUE));
  }
  elseif (isset($islandora_object['PROXY_MP3']) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PROXY_MP3'])) {
    $variables['islandora_content'] = l($islandora_object->label, $audio_url);
  }  
}

/**
 * Override the Islandora PDF preprocess function
 */
function islandoratheme_preprocess_islandora_pdf(&$variables) {
  
  // Add css file for PDF presentation
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/pdf.css', array('group' => CSS_THEME, 'type' => 'file'));
  
  // Create full screen view
  $variables['islandora_view_link'] = str_replace("download", "view", $variables['islandora_download_link']);
  $variables['islandora_view_link'] = str_replace("Download pdf", "Full Screen View", $variables['islandora_view_link']);
  
  $islandora_object = $variables['islandora_object'];
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
 
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array(); 

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);
}

/**
 * Override the Islandora Large Image preprocess function
 */
function islandoratheme_preprocess_islandora_large_image(&$variables) {

  // base url
  global $base_url;
  // base path
  global $base_path;

  drupal_add_js('jQuery(document).ready(function(){tabLinkReload();});', 'inline');
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/large-image.css', array('group' => CSS_THEME, 'type' => 'file'));

  $islandora_object = $variables['islandora_object'];

  // Create the full view link
  $variables['islandora_download_link'] = '<a href="' . $base_url . request_uri() . '/datastream/OBJ/view' . '">Download File</a>';
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
 
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
  
  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);
}

/**
 * Override the Islandora Internet Archive Bookreader module process function
 */
function islandoratheme_process_islandora_internet_archive_bookreader(&$variables) {

  // base url
  global $base_url;
  // base path
  global $base_path;

  $islandora_object = $variables['object'];

  //Create thumbnail HTML
  $variables['islandora_tn_html'] = '<img src="' . $base_url . request_uri() . '/datastream/TN/view' . '">';

  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/book.css', array('group' => CSS_THEME, 'type' => 'file'));
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
 
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
 
  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);
}

/**
 * Override the Islandora Collection preprocess function
 */
function islandoratheme_preprocess_islandora_basic_collection(&$variables) {  
  // base url
  global $base_url;
  // base path
  global $base_path;
  $islandora_object = $variables['islandora_object'];
  
  $page_number = (empty($_GET['page'])) ? 0 : $_GET['page'];
  $page_size = (empty($_GET['pagesize'])) ? variable_get('islandora_basic_collection_page_size', '10') : $_GET['pagesize'];
  $results = $variables['collection_results']; //islandora_basic_collection_get_objects($islandora_object, $page_number, $page_size); 
  $total_count = count($results);

  $associated_objects_mods_array = array(); 
  $start = $page_size * ($page_number);
  $end = min($start + $page_size, $total_count);

  for ($i = $start; $i < $end; $i++) {
    $pid = $results[$i]['object']['value'];
    $fc_object = islandora_object_load($pid);
    if (!isset($fc_object)) {
      continue; //null object so don't show in collection view;
    }
    $associated_objects_mods_array[$pid]['object'] = $fc_object;

    if (isset($fc_object['MODS']))
    {
      try {
        $mods = $fc_object['MODS']->content;
        $mods_object = simplexml_load_string($mods);
        $associated_objects_mods_array[$pid]['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
      } catch (Exception $e) {
        drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
      }
    }
      
    $object_url = 'islandora/object/' . $pid;
    $thumbnail_img = '<img src="' . $base_path . $object_url . '/datastream/TN/view"' . '/>';
    $title = $results[$i]['title']['value'];
    
    //If the object is a collection, get description information.
    $collection_description = false;
    if (isset($fc_object['DESC-TEXT']))
    {
        $collection_description = $fc_object['DESC-TEXT']->content;
    }
    //end of obtaining collection description
    
    $associated_objects_mods_array[$pid]['pid'] = $pid;
    $associated_objects_mods_array[$pid]['path'] = $object_url;
    $associated_objects_mods_array[$pid]['title'] = $title;
    $associated_objects_mods_array[$pid]['class'] = drupal_strtolower(preg_replace('/[^A-Za-z0-9]/', '-', $pid));
    if (isset($fc_object['TN'])) {
      $thumbnail_img = '<img src="' . $base_path . $object_url . '/datastream/TN/view"' . '/>';
    }
    else {
      $image_path = drupal_get_path('module', 'islandora');
      $thumbnail_img = '<img src="' . $base_path . $image_path . '/images/Crystal_Clear_action_filenew.png"/>';
    }
    $associated_objects_mods_array[$pid]['thumbnail'] = $thumbnail_img;
    $associated_objects_mods_array[$pid]['title_link'] = l($title, $object_url, array('html' => TRUE, 'attributes' => array('title' => $title)));
    $associated_objects_mods_array[$pid]['thumb_link'] = l($thumbnail_img, $object_url, array('html' => TRUE, 'attributes' => array('title' => $title)));
    
    if($collection_description)
    {
      $associated_objects_mods_array[$pid]['collection_description'] = $collection_description;
    }
  }
  $variables['associated_objects_mods_array'] = $associated_objects_mods_array;
}

// This function makes customizations to the breadcrumb region
function islandoratheme_breadcrumb($variables) {
  if (!empty($variables['breadcrumb'][1])) {
    //Append title if it's a search
    if (strpos($variables['breadcrumb'][1], 'islandora-solr-breadcrumb-super') !== false)
    {
      unset($variables['breadcrumb'][0]);
      return '<p><strong>Current Search: </strong>&nbsp;' . implode(' &raquo; ', $variables['breadcrumb']) . '</p>';
    }
    else
    {
      unset($variables['breadcrumb'][0]);
      return theme_breadcrumb($variables);
    }
  }
}

// Custom function that retrieves the path for branding logo
function get_branding_info(&$variables)
{
  // Get custom islandoratheme variables
  $variables = islandoratheme_variables($variables);
  
  //Create local variables
  $branding_info = array();
  $branding_info['institution_logo']['image_filename'] = $variables['default_brand_logo'];
  $branding_info['institution_logo']['institution_link'] = $variables['default_brand_link'];
  
  
  //if the MODS metadata has an owner institution, grab the logo information
  if (isset($variables['mods_array']['mods:owner_inst']) && ($variables['mods_array']['mods:owner_inst']['value'] != ''))
  {
    $owner_institution = $variables['mods_array']['mods:owner_inst']['value'];
    
    $query = new EntityFieldQuery();
    $results = $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', 'object_branding')
      ->propertyCondition('title', strtoupper($owner_institution))
      ->execute();
      
    if($results)
    {
      $object_branding_array = entity_load('node', array_keys($results['node']));
     
      foreach($object_branding_array as $object_branding)
      {
        $branding_info['institution_logo']['image_filename'] = $object_branding->field_institution_logo_upload['und'][0]['filename'];
        $branding_info['institution_logo']['institution_link'] = $object_branding->field_institution_link['und'][0]['value'];
      }
    }
  }

  //while the MODS metadata has an other logo information, grab the logos and links to display
  $local_counter = 0;

  while (isset($variables['mods_array']['mods:other_logo_' . $local_counter]) && 
    ($variables['mods_array']['mods:other_logo_' . $local_counter]['value'] != ''))
  {
    $other_logo = $variables['mods_array']['mods:other_logo_' . $local_counter]['value'];

    $query = new EntityFieldQuery();
    $results = $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', 'object_branding')
      ->propertyCondition('title', strtoupper($other_logo))
      ->execute();

    if($results)
    {
      $object_branding_array = entity_load('node', array_keys($results['node']));

      foreach($object_branding_array as $object_branding)
      {
        $branding_info['other_logo_' . $local_counter]['image_filename'] = $object_branding->field_institution_logo_upload['und'][0]['filename'];
        $branding_info['other_logo_' . $local_counter]['institution_link'] = $object_branding->field_institution_link['und'][0]['value'];
      }
    }

    $local_counter++;
  }
  
  return $branding_info;
}

/**
 * Override or insert variables for the page templates.
 */
/*
function islandoratheme_preprocess_page(&$variables) {
}
 
function islandoratheme_process_page(&$variables) {
}
*/

/**
 * Override or insert variables into the node templates.
 */
/* -- Delete this line if you want to use these functions
function islandoratheme_preprocess_node(&$vars) {
}
function islandoratheme_process_node(&$vars) {
}
// */


/**
 * Override or insert variables into the comment templates.
 */
/* -- Delete this line if you want to use these functions
function islandoratheme_preprocess_comment(&$vars) {
}
function islandoratheme_process_comment(&$vars) {
}
// */


/**
 * Override or insert variables into the block templates.
 */
/* -- Delete this line if you want to use these functions
function islandoratheme_preprocess_block(&$vars) {
}
function islandoratheme_process_block(&$vars) {
}
// */
