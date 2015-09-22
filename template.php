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
  drupal_add_library ('system', 'ui.accordion');
  //drupal_add_js('jQuery(document).ready(function(){jQuery("#tabs").tabs();});', 'inline');
  drupal_add_js('jQuery(document).ready(function(){jQuery("#tabs").tabs();if (typeof _currentContentModel !== "undefined" && _currentContentModel && _currentContentModel=="islandora:compoundCModel" && (window.location.href.indexOf("#tabs") < 0) &&(window.location.href.indexOf("tabload=true") < 0)) {jQuery("#tabs").tabs("select", jQuery("#tabs").tabs("length")-1);} });', 'inline');  
  drupal_add_js('jQuery(document).ready(function(){ jQuery("#islandora-solr-metadata-browse-form").submit(function () { jQuery("#edit-browse-field").removeAttr("autocomplete"); }); });', 'inline');
  drupal_add_js('jQuery(document).ready(function(){ jQuery("#islandora-solr-metadata-search-form").submit(function () { jQuery("input[name^=\'terms\']").removeAttr("autocomplete"); }); });', 'inline');
  drupal_add_js('jQuery(document).ready(function(){collectionBlankSearch();});', 'inline');
  drupal_add_js('jQuery(document).ready(function(){collectionAdvancedSearch();});', 'inline');

  module_load_include('inc', 'islandora', 'includes/utilities');
  
  //Retrieve the object
  $parsed_request = explode('/', $_SERVER['REQUEST_URI']);
  if (islandora_is_valid_pid(urldecode(end($parsed_request)))) {
    $islandora_object = islandora_object_load(urldecode(end($parsed_request)));
  
    //If it is a collection, add a class to body tag
    if(isset($islandora_object['COLLECTION_POLICY']))
    {
      $vars['classes_array'][] = 'collection-page';
    }
  }
}

function islandoratheme_process_html(&$vars) {
}
// */

/**
 * Override the Islandora Binary Object preprocess function
 */
function islandoratheme_preprocess_islandora_binary_object(&$variables) {

  // base url
  global $base_url;
  // base path
  global $base_path;

  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/binary-object.css', array('group' => CSS_THEME, 'type' => 'file'));

  $islandora_object = $variables['islandora_object'];
  
  // Create the download link
  $variables['islandora_download_link'] = '<a href="' . $base_url . '/islandora/object/' . $islandora_object . '/datastream/OBJ/download' . '">Download</a>';

  $variables['islandora_object_label'] = $islandora_object->label;

  if (isset($islandora_object['OBJ']) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['OBJ'])) {
    $mime_detect = new MimeDetect();
    $extension = $mime_detect->getExtension($islandora_object['OBJ']->mimetype);

    $variables['islandora_binary_object_info'] = t('This is a downloadable object of filetype @extension and size @size.', array(
      '@extension' => $extension,
      '@size' => format_size($islandora_object['OBJ']->size),
    ));
  }

  // Thumbnail.
  if (isset($islandora_object['TN']) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['TN'])) {
    $thumbnail_size_url = url("islandora/object/{$islandora_object->id}/datastream/TN/view");
    $params = array(
      'title' => $islandora_object->label,
      'path' => $thumbnail_size_url,
    );
    $variables['islandora_thumbnail_img'] = theme('image', $params);
  }
  $variables['parent_collections'] = islandora_get_parents_from_rels_ext($islandora_object);
  $variables['metadata'] = islandora_retrieve_metadata_markup($islandora_object);
  $variables['description'] = islandora_retrieve_description_markup($islandora_object);

  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }

  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);
}

/**
 * Override the Islandora Basic Image preprocess function
 */
function islandoratheme_preprocess_islandora_basic_image(&$variables) {

  // base url
  global $base_url;
  // base path
  global $base_path;
  
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/basic-image.css', array('group' => CSS_THEME, 'type' => 'file'));
  
  $islandora_object = $variables['islandora_object'];

  // Create the full view link
  $variables['islandora_view_link'] = '<a href="' . $base_url . '/islandora/object/' . $islandora_object . '/datastream/OBJ/view' . '">Full Screen View</a>';

  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
 
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array(); 
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);
}

/**
 * Override the Islandora Audio preprocess function
 */
function islandoratheme_preprocess_islandora_audio(&$variables) {
  
  // base url
  global $base_url;
  // base path
  global $base_path;

  $islandora_object = $variables['islandora_object'];

  // Create the full view link
  $variables['islandora_view_link'] = '<a href="' . $base_url . '/islandora/object/' . $islandora_object . '/datastream/OBJ/view' . '">Full Screen View</a>';
  $variables['islandora_full_url'] = $base_url . '/islandora/object/' . $islandora_object . '/datastream/OBJ/view';

  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/audio.css', array('group' => CSS_THEME, 'type' => 'file'));

  $repository = $islandora_object->repository;
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
  
  $variables['islandora_object_label'] = $islandora_object->label;
  
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array(); 
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);

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

  // base url
  global $base_url;
  // base path
  global $base_path;
 
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
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);

  // if serial article, set parent serial mods array
  $part_of = $islandora_object->relationships->get(ISLANDORA_RELS_EXT_URI, 'isComponentOf');
  if(!empty($part_of)) {
        //grab the metadata for the parent
        $parent_pid = $part_of[0]['object']['value'];

        // query for parent serial object
        $parent_serial_id = '';
        $query_serial = 'select $parentObject $collection from <#ri>
                        where (
                        $parentObject <fedora-rels-ext:isMemberOfCollection> $collection and
                        walk(<info:fedora/' . $parent_pid . '> <fedora-rels-ext:isMemberOf> $parentObject and $subject <fedora-rels-ext:isMemberOf> $parentObject)
                      )';
        $results = $islandora_object->repository->ri->itqlQuery($query_serial, 'unlimited');
        if (count($results) > 0) {
            $parent_serial_id = $results[0]['parentObject']['value'];
        }
        $parent_serial_object = islandora_object_load($parent_serial_id);

        $parent_mods = $parent_serial_object['MODS']->content;
        $variables['serial_mods_array'] = MODS::as_formatted_array(simplexml_load_string($parent_mods));
        $object_url = 'islandora/object/' . $parent_serial_id;
        $variables['serial_tn_html'] = '<img src="' . $base_path . $object_url . '/datastream/TN/view"' . '/>';
        $variables['serial_parent_collections'] = remove_non_public_sites_from_collections(islandora_get_parents_from_rels_ext($parent_serial_object));

        // build navigation links
        $links = array();
        $siblings = array();
        $query_siblings = 'select $object $sequence_number from <#ri>
                        where (
                        $object <fedora-model:hasModel> <info:fedora/islandora:sp_pdf> and
                        $object <http://islandora.ca/ontology/relsext#isComponentOf> <info:fedora/' . $parent_pid . '> and
                        $object <http://islandora.ca/ontology/relsext#sequence_position> $sequence_number
                      )
                      order by $sequence_number';
        $results = $islandora_object->repository->ri->itqlQuery($query_siblings, 'unlimited');
        foreach ($results as $result) {
          $siblings[] = str_replace('info:fedora/','',$result['object']['value']);
        }
        $index = array_search($islandora_object->id, $siblings);
        if (count($siblings) == 1) {
          // try again with parent object
          unset($siblings[0]);
          $parent_object = islandora_object_load($parent_pid);
          $parent_part_of = $parent_object->relationships->get('info:fedora/fedora-system:def/relations-external#', 'isMemberOf');
          if (!empty($parent_part_of)) {
              $grandparent_pid = $parent_part_of[0]['object']['value'];
              $query_siblings = 'select $object $sequence_number from <#ri>
                        where (
                        $object <fedora-rels-ext:isMemberOf> <info:fedora/' . $grandparent_pid . '> and
                        $object <http://islandora.ca/ontology/relsext#sequence_position> $sequence_number
                      )
                      order by $sequence_number';
              $results = $islandora_object->repository->ri->itqlQuery($query_siblings, 'unlimited');
              foreach ($results as $result) {
                $siblings[] = str_replace('info:fedora/','',$result['object']['value']);
              }
              $index = array_search($parent_object->id, $siblings);
          }
        }
        if (isset($siblings[$index - 1])) {
          $previous_sibling = $siblings[$index - 1];
          $links[] = array(
            'title' => t('Prev'),
            'href' => url("islandora/object/{$previous_sibling}", array('absolute' => TRUE)),
          );
        }
        if (isset($siblings[$index + 1])) {
          $next_sibling = $siblings[$index + 1];
          $links[] = array(
            'title' => t('Next'),
            'href' => url("islandora/object/{$next_sibling}", array('absolute' => TRUE)),
          );
        }

        $links[] = array(
          'title' => t('All Issues'),
          'href' => url("islandora/object/{$parent_serial_object->id}", array('absolute' => TRUE)),
        );
        $attributes = array('class' => array('links', 'inline'));
        $variables['serial_navigation_links'] = theme('links', array('links' => $links, 'attributes' => $attributes));
  }

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
  $variables['islandora_download_link'] = '<a href="' . $base_url . '/islandora/object/' . $islandora_object . '/datastream/JPG/view' . '" download="view">Download File</a>';
  
  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
 
  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);
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
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // if newspaper issue, set parent newspaper mods array
  if (in_array('islandora:newspaperIssueCModel', $islandora_object->models)) {
    $part_of = $islandora_object->relationships->get('info:fedora/fedora-system:def/relations-external#', 'isMemberOf');
    if(!empty($part_of)) {
      //grab the metadata for the parent
      foreach ($part_of as $part) {
        $parent_pid = $part['object']['value'];
        $parent_object = islandora_object_load($parent_pid);
        $parent_mods = $parent_object['MODS']->content;
        $variables['newspaper_mods_array'] = MODS::as_formatted_array(simplexml_load_string($parent_mods));
        $object_url = 'islandora/object/' . $parent_pid;
        $variables['newspaper_tn_html'] = '<img src="' . $base_path . $object_url . '/datastream/TN/view"' . '/>';
        $variables['newspaper_parent_collections'] = remove_non_public_sites_from_collections(islandora_get_parents_from_rels_ext($parent_object));
      }
    }
  }
 
  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);
}

/**
 * Override the Islandora Video preprocess function
 */
function islandoratheme_preprocess_islandora_video(&$variables) {

  // base url
  global $base_url;
  // base path
  global $base_path;

  $viewer_dsid = 'MP4';

  $islandora_object = $variables['object'];

  // Create the full view link
  $variables['islandora_view_link'] = '<a href="' . $base_url . '/islandora/object/' . $islandora_object . '/datastream/OBJ/view' . '">Full Screen View</a>';
  $variables['islandora_full_url'] = $base_url . '/islandora/object/' . $islandora_object . '/datastream/OBJ/view';

  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/video.css', array('group' => CSS_THEME, 'type' => 'file'));

  $repository = $islandora_object->repository;

  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }

  $variables['islandora_object_label'] = $islandora_object->label;

  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);

  // Check if the object is part of a Compound Object
  compound_object_check($islandora_object, $variables);

  // remove non-public sites from collection links
  $variables['parent_collections'] = remove_non_public_sites_from_collections($variables['parent_collections']);

  // Get parameters for the player...
  $video_params = array(
    "pid" => $islandora_object->id,
  );

  // Video player.
  if (isset($islandora_object[$viewer_dsid]) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object[$viewer_dsid])) {
    $video_url = url("islandora/object/{$islandora_object->id}/datastream/$viewer_dsid/view");
    $video_params += array(
      'mime' => 'video/mp4',
      'url' => $video_url,
    );
  }

  // Thumbnail.
  if (isset($islandora_object['TN']) && islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['TN'])) {
    $tn_url = url("islandora/object/{$islandora_object->id}/datastream/TN/view");
    $params = array(
      'title' => $islandora_object->label,
      'path' => $tn_url,
    );
    $variables['islandora_thumbnail_img'] = theme('image', $params);

    $video_params += array(
      'tn' => $tn_url,
    );
  }

  $viewer = islandora_get_viewer($video_params, 'islandora_video_viewers', $islandora_object);
  $variables['islandora_content'] = '';
  if ($viewer) {
    $variables['islandora_content'] = $viewer;
  }
  else {
    $variables['islandora_content'] = NULL;
  }
  return array('' => $viewer);
}

/**
 * Override the Islandora Collection Wrapper preprocess function
 */
function islandoratheme_preprocess_islandora_basic_collection_wrapper(&$variables) { 
  $islandora_object = $variables['islandora_object'];

  //If the object has a DESC-TEXT datastream, get description information.
  $variables['description_text'] = false;
  if (isset($islandora_object['DESC-TEXT']))
  {
    $variables['description_text'] = $islandora_object['DESC-TEXT']->content;
  }
  
  //If the collection contains a RELATED-LINKS datastream, process it
  $variables['related_links_html'] = false;
  if (isset($islandora_object['RELATED-LINKS']))
  {
    $variables['related_links_html'] = create_related_links_html($islandora_object); 
  }
}

/**
 * Implements hook_preprocess_HOOK for islandora_scholar_citation
 */
function islandoratheme_preprocess_islandora_scholar_citation(&$variables) {
  global $base_url;
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . 'css/citation.css', 
    array('group' => CSS_THEME, 'type' => 'file'));  
  $islandora_object = $variables['islandora_object'];
  
  if (isset($islandora_object['PDF']) && 
    islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PDF'])) {
    $variables['islandora_view_link'] = 
      l(t('Full Screen View'), "islandora/object/$islandora_object->id/datastream/PDF/view/citation.pdf");
    $variables['islandora_download_link'] =
      l(t('Download pdf'), "islandora/object/$islandora_object->id/datastream/PDF/download/citation.pdf");
    $variables['citation_view'] = 
      $base_url . '/islandora/object/' . $islandora_object->id . '/datastream/PDF/view';
  }

  if (isset($islandora_object['TN'])) {
    $variables['islandora_thumbnail_img'] =
      $base_url . '/islandora/object/' . $islandora_object->id . '/datastream/TN/view';
  }

  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', 
      array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }

  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array(); 
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);
}

/**
 * Implements hook_preprocess_HOOK for islandora_scholar_thesis
 */
function islandoratheme_preprocess_islandora_scholar_thesis(&$variables) {
  global $base_url;
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . 'css/thesis.css', 
    array('group' => CSS_THEME, 'type' => 'file'));
  $islandora_object = $variables['islandora_object'];
  
  if (isset($islandora_object['PDF']) &&
    islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PDF'])) {
    $variables['islandora_view_link'] =
      l(t('Full Screen View'), "islandora/object/$islandora_object->id/datastream/PDF/view/citation.pdf");
    $variables['islandora_download_link'] =
      l(t('Download pdf'), "islandora/object/$islandora_object->id/datastream/PDF/download/citation.pdf");
    $variables['citation_view'] =
      $base_url . '/islandora/object/' . $islandora_object->id . '/datastream/PDF/view';
  }

  if (isset($islandora_object['TN'])) {
    $variables['islandora_thumbnail_img'] =
      $base_url . '/islandora/object/' . $islandora_object->id . '/datastream/TN/view';
  }

  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', 
      array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }

  $variables['mods_array'] = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();
  $variables['other_logo_array'] = isset($mods_object) ? MODS::other_logo_array($mods_object) : array();

  // Grab the branding information
  $variables['branding_info'] = get_branding_info($variables);
}

/**
 * Override the Islandora Collection preprocess function
 */
function islandoratheme_preprocess_islandora_basic_collection(&$variables) {  
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
    $description_text = false;
    if (isset($fc_object['DESC-TEXT']))
    {
      $description_text = $fc_object['DESC-TEXT']->content;
    }
    
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
    
    if($description_text)
    {
      $associated_objects_mods_array[$pid]['collection_description'] = $description_text;
    }
  }
  $variables['associated_objects_mods_array'] = $associated_objects_mods_array;
}

/**
 * Implements theme from newspaper module.
 */
/*
function islandoratheme_islandora_newspaper(array $variables) {
  drupal_add_js('misc/collapse.js');
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/newspaper.css', array('group' => CSS_THEME, 'type' => 'file'));
  $object = $variables['object'];
  $issues = islandora_newspaper_get_issues($object);
  $grouped_issues = islandora_newspaper_group_issues($issues);
  $output = array(
    'pagetitle' => array(
      '#markup' => '<h3>' . $object->label . '</h3>',
    ),
    'controls' => array(
      '#theme' => 'links',
      '#attributes' => array(
        'class' => array('links', 'inline'),
        'id' => array('newspaper-controls'),
      ),
      '#links' => array(
        array(
          'title' => t('Expand all months'),
          'href' => "javascript://void(0)",
          'html' => TRUE,
          'external' => TRUE,
          'attributes' => array(
            'onclick' => "Drupal.toggleFieldset(jQuery('fieldset.month.collapsed'));",
          ),
	),
	array(
          'title' => t('Collapse all months'),
          'href' => "javascript://void(0)",
          'html' => TRUE,
          'external' => TRUE,
          'attributes' => array(
            'onclick' => "Drupal.toggleFieldset(jQuery('fieldset.month:not(.collapsed)'));",
          ),
	),
      ),
    ),
    'tabs' => array(
      '#type' => 'vertical_tabs',
    ),
  );
  $tabs = &$output['tabs'];
  foreach ($grouped_issues as $year => $months) {
    $tabs[$year] = array(
      '#title' => $year,
      '#type' => 'fieldset',
    );
    foreach ($months as $month => $days) {
      $month_name = t("@date", array(
        "@date" => date("F", mktime(0, 0, 0, $month, 1, 2000)),
      ));
      $tabs[$year][$month] = array(
        '#title' => $month_name,
        '#type' => 'fieldset',
        '#attributes' => array(
          'class' => array('collapsible', 'collapsed', 'month'),
        ),
      );
      foreach ($days as $day => $issues) {
        foreach ($issues as $issue) {
          $tabs[$year][$month][$day][] = array(
            '#theme' => 'link',
            '#prefix' => '<div>',
            '#suffix' => '</div>',
            '#text' => t("@month @day, @year", array(
                '@year' => $year,
                '@month' => $month_name,
                '@day' => $day,
                )),
            '#path' => "islandora/object/{$issue['pid']}",
            '#options' => array(
              'attributes' => array(),
              'html' => FALSE,
            ),
          );
	}
      }
      ksort($tabs[$year][$month]);
    }
    ksort($tabs[$year]);
  }
  ksort($tabs);
  return drupal_render($output);
}
*/
/**
 * Implements theme from newspaper module.
 */
function islandoratheme_islandora_newspaper(array $variables) {
  // base url
  global $base_url;
  // base path
  global $base_path;

  $islandora_object = $variables['object'];

  $newspaper_output = '<h3>' . $islandora_object->label . '</h3>';
  $newspaper_output .= '<div id="tabs"><ul><li><a href="#tabs-1">Summary</a></li><li><a href="#tabs-2">Newspaper Details</a></li></ul><div id="tabs-1">';
  $newspaper_output .= theme_islandora_newspaper($variables);
  $newspaper_output .= '</div><div id="tabs-2">';
  $newspaper_output .= islandoratheme_create_mods_table($islandora_object, 'islandora-newspaper-thumbnail');

  $parent_collections = remove_non_public_sites_from_collections(islandora_get_parents_from_rels_ext($islandora_object));
  if (count($parent_collections) > 0) {
    $newspaper_output .= '<div><h2>In Collections</h2><ul>';
    foreach ($parent_collections as $collection) {
      if (substr($collection->id, 0, 5) == 'palmm') {
        $full_description .= '<li>';
        $full_description .=  l($collection->label . " (PALMM)", "http://palmm.digital.flvc.org/islandora/object/{$collection->id}");
        $full_description .= '</li>';
      }
      else {
        $newspaper_output .= '<li>';
        $newspaper_output .=  l($collection->label, "islandora/object/{$collection->id}");
        $newspaper_output .= '</li>';
      }
    }
    $newspaper_output .= '</ul></div>';
  }

  $newspaper_output .= '</div></div>';
  return $newspaper_output;
}

function islandoratheme_islandora_serial_object(array $variables) {
  // base url
  global $base_url;
  // base path
  global $base_path;

  $islandora_object = $variables['object'];

  $serial_output = '<h3>' . $islandora_object->label . '</h3>';
  $serial_output .= '<div id="tabs"><ul><li><a href="#tabs-1">Summary</a></li><li><a href="#tabs-2">Serial Details</a></li></ul><div id="tabs-1">';
  $tree_block = islandora_serial_object_block_view('islandora_serial_object_tree');
  $serial_output .= drupal_render($tree_block['content']);
  $serial_output .= '</div><div id="tabs-2">';
  $serial_output .= islandoratheme_create_mods_table($islandora_object, 'islandora-serial-thumbnail');

  $parent_collections = remove_non_public_sites_from_collections(islandora_get_parents_from_rels_ext($islandora_object));
  if (count($parent_collections) > 0) {
    $serial_output .= '<div><h2>In Collections</h2><ul>';
    foreach ($parent_collections as $collection) {
      if (substr($collection->id, 0, 5) == 'palmm') {
        $full_description .= '<li>';
        $full_description .=  l($collection->label . " (PALMM)", "http://palmm.digital.flvc.org/islandora/object/{$collection->id}");
        $full_description .= '</li>';
      }
      else {
        $serial_output .= '<li>';
        $serial_output .=  l($collection->label, "islandora/object/{$collection->id}");
        $serial_output .= '</li>';
      }
    }
    $serial_output .= '</ul></div>';
  }

  $serial_output .= '</div></div>';
  return $serial_output;
}

function islandoratheme_islandora_serial_intermediate_object(array $variables) {
  // base url
  global $base_url;
  // base path
  global $base_path;

  $serial_output = '';
  $islandora_object = $variables['object'];
  $pid = $islandora_object->id;

  $siblings = array();
  $links = array();
  $part_of = $islandora_object->relationships->get('info:fedora/fedora-system:def/relations-external#', 'isMemberOf');
  if(!empty($part_of)) {
    $parent_pid = $part_of[0]['object']['value'];
    $query_siblings = 'select $object $sequence_number from <#ri>
                        where (
                        $object <fedora-rels-ext:isMemberOf> <info:fedora/' . $parent_pid . '> and
                        $object <http://islandora.ca/ontology/relsext#sequence_position> $sequence_number
                      )
                      order by $sequence_number';
    $results = $islandora_object->repository->ri->itqlQuery($query_siblings, 'unlimited');
    foreach ($results as $result) {
      $siblings[] = str_replace('info:fedora/','',$result['object']['value']);
    }
    $index = array_search($pid, $siblings);
    //$previous_sibling = isset($siblings[$index - 1]) ? $siblings[$index - 1] : NULL;
    //$next_sibling = isset($siblings[$index + 1]) ? $siblings[$index + 1] : NULL;
    if (isset($siblings[$index - 1])) {
      $previous_sibling = $siblings[$index - 1];
      $links[] = array(
        'title' => t('Prev'),
        'href' => url("islandora/object/{$previous_sibling}", array('absolute' => TRUE)),
      );
    }
    if (isset($siblings[$index + 1])) {
      $next_sibling = $siblings[$index + 1];
      $links[] = array(
        'title' => t('Next'),
        'href' => url("islandora/object/{$next_sibling}", array('absolute' => TRUE)),
      );
    }
  }

  $parent_serial = '';
  $query_serial = 'select $parentObject $collection from <#ri>
                        where (
                        $parentObject <fedora-rels-ext:isMemberOfCollection> $collection and
                        walk(<info:fedora/' . $pid . '> <fedora-rels-ext:isMemberOf> $parentObject and $subject <fedora-rels-ext:isMemberOf> $parentObject)
                      )';
  $results = $islandora_object->repository->ri->itqlQuery($query_serial, 'unlimited');
  if (count($results) > 0) {
      $parent_serial = $results[0]['parentObject']['value'];
  }
  $parent_serial_object = islandora_object_load($parent_serial);

  $links[] = array(
    'title' => t('All Issues'),
    'href' => url("islandora/object/{$parent_serial_object->id}", array('absolute' => TRUE)),
  );
  $attributes = array('class' => array('links', 'inline'));
  $serial_output = theme('links', array('links' => $links, 'attributes' => $attributes));

  $serial_output .= '<h3>' . $islandora_object->label . '</h3>';
  $serial_output .= '<div id="tabs"><ul><li><a href="#tabs-1">Summary</a></li><li><a href="#tabs-2">Full Description</a></li><li><a href="#tabs-3">Serial Details</a></li></ul><div id="tabs-1">';
  $pdf_table = views_embed_view('islandora_serial_object_intermediate_pdf_view');
  if (strlen($pdf_table) > 0) {
    $serial_output .= $pdf_table;
  }
  else {
    $serial_output .= views_embed_view('islandora_serial_object_intermediate_objects_view');
  }
  //$serial_output .= views_embed_view('islandora_serial_object_intermediate_pdf_view');
  $serial_output .= '</div><div id="tabs-2">';
  $serial_output .= islandoratheme_create_mods_table($islandora_object, 'islandora-serial-thumbnail');

/*
  $parent_collections = islandora_get_parents_from_rels_ext($islandora_object);
  if (count($parent_collections) > 0) {
    $full_description .= '<div><h2>In</h2><ul>';
    foreach ($parent_collections as $collection) {
      if (substr($collection->id, 0, 5) != 'palmm') {
        $full_description .= '<li>';
        $full_description .=  l($collection->label, "islandora/object/{$collection->id}");
        $full_description .= '</li>';
      }
    }
    $full_description .= '</ul></div>';
  }
*/

  // add parent serial
  $serial_output .= '</div><div id="tabs-3">';

  $serial_output .= islandoratheme_create_mods_table($parent_serial_object, 'islandora-serial-thumbnail');

  $parent_collections = remove_non_public_sites_from_collections(islandora_get_parents_from_rels_ext($parent_serial_object));
  if (count($parent_collections) > 0) {
    $serial_output .= '<div><h2>In Collections</h2><ul>';
    foreach ($parent_collections as $collection) {
      if (substr($collection->id, 0, 5) == 'palmm') {
        $full_description .= '<li>';
        $full_description .=  l($collection->label . " (PALMM)", "http://palmm.digital.flvc.org/islandora/object/{$collection->id}");
        $full_description .= '</li>';
      }
      else {
        $serial_output .= '<li>';
        $serial_output .=  l($collection->label, "islandora/object/{$collection->id}");
        $serial_output .= '</li>';
      }
    }
    $serial_output .= '</ul></div>';
  }

  $serial_output .= '</div></div>';
  return $serial_output;
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

  while (isset($variables['other_logo_array']['mods:other_logo_' . $local_counter]) && 
    ($variables['other_logo_array']['mods:other_logo_' . $local_counter]['value'] != ''))
  {
    $other_logo = $variables['other_logo_array']['mods:other_logo_' . $local_counter]['value'];

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
 * This function checks if an Islandora Object is part of a Compound Object. If it is, it sets a variable.
 */
function compound_object_check($islandora_object, &$variables) {
  // base url
  global $base_url;
  // base path
  global $base_path;

  $rels_predicate = variable_get('islandora_compound_object_relationship', 'isConstituentOf');
  $part_of = $islandora_object->relationships->get('info:fedora/fedora-system:def/relations-external#', $rels_predicate);

  if(!empty($part_of)) {
    //grab the metadata for the parent
    foreach ($part_of as $part) {
      $parent_pid = $part['object']['value'];
      $parent_object = islandora_object_load($parent_pid);
      $parent_mods = $parent_object['MODS']->content;
      $variables['parent_mods_array'] = MODS::as_formatted_array(simplexml_load_string($parent_mods));
      $variables['parent_thumbnail_img'] = '<img src="' . $base_path . 'islandora/object/' . $parent_pid . '/datastream/TN/view"' . '/>';

    }
  }
}

function islandoratheme_create_mods_table($islandora_object, $thumbclass) {
  // base url
  global $base_url;
  // base path
  global $base_path;

  $pid = $islandora_object->id;

  try {
    $mods = $islandora_object['MODS']->content;
    $mods_object = simplexml_load_string($mods);
  } catch (Exception $e) {
    drupal_set_message(t('Error retrieving object %s %t', array('%s' => $islandora_object->id, '%t' => $e->getMessage())), 'error', FALSE);
  }
  $mods_array = isset($mods_object) ? MODS::as_formatted_array($mods_object) : array();

  $full_description = '<div>';
  $full_description .= '<table class="islandora-table-display" width="100%">';
  $full_description .= '<tbody>';

  $row_field = 0;
  foreach ($mods_array as $key => $value) {

    if (trim($value['value']) != '') {

      $full_description .= '<tr class="islandora-definition-row">';
      $full_description .= '<th class="full-description-heading';
      if ($row_field == 0) $full_description .= ' first';
      $full_description .= '">';
      $full_description .= $value['label'] . ':</th><td class="' . $value['class'];
      if ($row_field == 0) $full_description .= ' first';
      $full_description .= '">';
      $full_description .= $value['value'];
      $full_description .= '</td>';

      if (($row_field == 0)&&(isset($islandora_object['TN']))) {
        $object_url = 'islandora/object/' . $pid;
        $thumbnail_img = '<img src="' . $base_path . $object_url . '/datastream/TN/view"' . '/>';
        $full_description .= '<td class="';
        $full_description .= $thumbclass;
        $full_description .= '" rowspan="8">';
        $full_description .= $thumbnail_img;
        $full_description .= '</td>';
      }

      $full_description .= '</tr>';
      $row_field++;

    }
  }
  $full_description .= '</tbody></table></div>';

  return $full_description;
}

function remove_non_public_sites_from_collections($orig_collections)
{
    global $base_url;

    $non_public_sites = array("fiu", "uf", "unf", "usf", "uwf");

    if (substr($base_url, 7, 5) != 'palmm')
      return $orig_collections;

    $new_collections = array();
    if (count($orig_collections) > 0) {
      foreach ($orig_collections as $collection) {
        $matches = array();
        preg_match('/^([^:]*)/', $collection->id, $matches);
        if (!in_array($matches[0],$non_public_sites)) {
          $new_collections[] = $collection;
        }
      }
      return $new_collections;
    }
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
