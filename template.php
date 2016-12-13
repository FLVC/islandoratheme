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
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/datatables.css', array('group' => CSS_THEME, 'type' => 'file'));
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
        $query_serial = <<<'EOQ'
SELECT ?parentObject ?collection
FROM <#ri>
WHERE {
  ?parentObject <fedora-rels-ext:isMemberOfCollection> ?collection .
  <info:fedora/!pid> <fedora-rels-ext:isMemberOf>+ ?parentObject .
}
EOQ;
        $formatted_query_serial = format_string($query_serial, array(
          '!pid' => $parent_pid,
        ));
        $results = $islandora_object->repository->ri->sparqlQuery($formatted_query_serial, 'unlimited');
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
        $query_siblings = <<<'EOQ'
SELECT ?object ?sequence_number
FROM <#ri>
WHERE {
  ?object <fedora-model:hasModel> <info:fedora/islandora:sp_pdf>;
          <http://islandora.ca/ontology/relsext#isComponentOf> <info:fedora/!pid>;
          <http://islandora.ca/ontology/relsext#sequence_position> ?sequence_number .
}
ORDER BY ?sequence_number
EOQ;
        $formatted_query_siblings = format_string($query_siblings, array(
          '!pid' => $parent_pid,
        ));
        $results = $islandora_object->repository->ri->sparqlQuery($formatted_query_siblings, 'unlimited');
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
              $query_siblings = <<<'EOQ'
SELECT ?object ?sequence_number
FROM <#ri>
WHERE {
  ?object <fedora-rels-ext:isMemberOf> <info:fedora/!pid>;
          <http://islandora.ca/ontology/relsext#sequence_position> ?sequence_number .
}
ORDER BY ?sequence_number
EOQ;
              $formatted_query_siblings = format_string($query_siblings, array(
                '!pid' => $grandparent_pid,
              ));
              $results = $islandora_object->repository->ri->sparqlQuery($formatted_query_siblings, 'unlimited');
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
  drupal_add_js(drupal_get_path('theme', 'islandoratheme') . '/js/entity_overlay.js');

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
  $doc = new DOMDocument();
  $doc->loadXML($variables['islandora_object']['MODS']->content);
  $xpath = new DOMXPath($doc);
  $xpath->registerNamespace('mods', 'http://www.loc.gov/mods/v3');
  $xpath_results = $xpath->query(variable_get('islandora_altmetrics_doi_xpath', '/mods:mods/mods:identifier[@type="doi"]'));
  $doi = @$xpath_results->item(0)->nodeValue;
  if (!empty($doi)) {
    $variables['altmetric_badge_html'] = "<div class='altmetric-embed' data-badge-popover='left' data-doi='" . $doi . "'></div>";
    // This uses the default public Scopus API key. We might want to change it later, and find a way not to store it in GitHub.
    $scopus_response = file_get_contents("http://api.elsevier.com:80/content/abstract/citation-count?doi=" . $doi . "&apiKey=b3a71de2bde04544495881ed9d2f9c5b&httpAccept=text%2Fhtml");
    if (!strpos($scopus_response, 'unavailable')) { 
      $variables['scopus_badge_html'] = $scopus_response;  
    }
  }


  global $base_url;
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/citation.css', 
    array('group' => CSS_THEME, 'type' => 'file'));  
  $islandora_object = $variables['islandora_object'];

  // Sharing buttons testing
  $variables['sharing_buttons'] = build_sharing_button_html($islandora_object);
  
  if (isset($islandora_object['PDF']) && 
    islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PDF'])) {
    $variables['islandora_view_link'] = 
      l(t('Full Screen View'), "islandora/object/$islandora_object->id/datastream/PDF/view/citation.pdf");
    $variables['islandora_download_link'] = "/islandora/object/$islandora_object->id/datastream/PDF/download/citation.pdf";
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

  $embargo_data = get_embargo_status($islandora_object);
  $variables['embargoed'] = $embargo_data['embargoed'];
  $variables['expiry_msg'] = $embargo_data['expiry_msg'];

  if (module_exists('islandora_usage_stats_callbacks') && !$variables['embargoed']) {
    $usage_data = get_usage_stats($islandora_object);
    $variables['usage_views'] = $usage_data['views'];
    $variables['usage_downloads'] = $usage_data['downloads'];
    $variables['usage_view_icon'] = $usage_data['view_icon_path'];
    $variables['usage_download_icon'] = $usage_data['download_icon_path'];
  }
}

/**
 * Implements hook_preprocess_HOOK for islandora_scholar_thesis
 */
function islandoratheme_preprocess_islandora_scholar_thesis(&$variables) {
  global $base_url;
  drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/thesis.css', 
    array('group' => CSS_THEME, 'type' => 'file'));
  $islandora_object = $variables['islandora_object'];
  
  if (isset($islandora_object['PDF']) &&
    islandora_datastream_access(ISLANDORA_VIEW_OBJECTS, $islandora_object['PDF'])) {
    $variables['islandora_view_link'] =
      l(t('Full Screen View'), "islandora/object/$islandora_object->id/datastream/PDF/view/citation.pdf");
    $variables['islandora_download_link'] = "/islandora/object/$islandora_object->id/datastream/PDF/download/citation.pdf";
    $variables['thesis_view'] =
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

  $embargo_data = get_embargo_status($islandora_object);
  $variables['embargoed'] = $embargo_data['embargoed'];
  $variables['expiry_msg'] = $embargo_data['expiry_msg'];

  if (module_exists('islandora_usage_stats_callbacks') && !$variables['embargoed']) {
    $usage_data = get_usage_stats($islandora_object);
    $variables['usage_views'] = $usage_data['views'];
    $variables['usage_downloads'] = $usage_data['downloads'];
  }
}

/**
 * Implements template_preprocess_HOOK().
 */
function islandoratheme_preprocess_islandora_basic_collection_grid(&$variables) {

  //$associated_objects = $variables['associated_objects_array'];

  foreach ($variables['associated_objects_array'] as $key => &$value) {
    $pid = $value['pid'];
    $fc_object = islandora_object_load($pid);

    // adding class for content model specific display
    if (in_array('islandora:organizationCModel', $fc_object->models)) {
      $value['thumb_link'] = str_replace('class="','class="flvc_content_model_organization_tn ',$value['thumb_link']);
    }
    else if (in_array('islandora:personCModel', $fc_object->models)) {
      $value['thumb_link'] = str_replace('class="','class="flvc_content_model_person_tn ',$value['thumb_link']);
    }
    
  }
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

  for ($i=0; $i < $total_count; $i++) {
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
      $thumbnail_img = '<img src="' . $base_path . $image_path . '/images/folder.png"/>';
    }
    $associated_objects_mods_array[$pid]['thumbnail'] = $thumbnail_img;
    $associated_objects_mods_array[$pid]['title_link'] = l($title, $object_url, array('html' => TRUE, 'attributes' => array('title' => $title)));
    $attributes = array('title' => $title);
    // adding class for content model specific display
    if (in_array('islandora:organizationCModel', $fc_object->models)) {
      $attributes['class'] = 'flvc_content_model_organization_tn';
    }
    else if (in_array('islandora:personCModel', $fc_object->models)) {
      $attributes['class'] = 'flvc_content_model_person_tn';
    }
    $associated_objects_mods_array[$pid]['thumb_link'] = l($thumbnail_img, $object_url, array('html' => TRUE, 'attributes' => $attributes));
    
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
  $newspaper_output .= $variables['islandora_content'];
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
      else if (strpos($base_url, 'palmm') !== false) {
        $parsed_pid = explode(':',$collection->id);
        $full_description .= '<li>';
        $full_description .=  l($collection->label, str_replace('palmm', $parsed_pid[0], $base_url) . "/islandora/object/{$collection->id}");
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
      else if (strpos($base_url, 'palmm') !== false) {
        $parsed_pid = explode(':',$collection->id);
        $full_description .= '<li>';
        $full_description .=  l($collection->label, str_replace('palmm', $parsed_pid[0], $base_url) . "/islandora/object/{$collection->id}");
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
    $query_siblings = <<<'EOQ'
SELECT ?object ?sequence_number
FROM <#ri>
WHERE {
  ?object <fedora-rels-ext:isMemberOf> <info:fedora/!pid>;
          <http://islandora.ca/ontology/relsext#sequence_position> ?sequence_number .
}
ORDER BY ?sequence_number
EOQ;
    $formatted_query_siblings = format_string($query_siblings, array(
       '!pid' => $parent_pid,
    ));
    $results = $islandora_object->repository->ri->sparqlQuery($formatted_query_siblings, 'unlimited');
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
  $query_serial = <<<'EOQ'
SELECT ?parentObject ?collection
FROM <#ri>
WHERE {
  ?parentObject <fedora-rels-ext:isMemberOfCollection> ?collection .
  <info:fedora/!pid> <fedora-rels-ext:isMemberOf>+ ?parentObject .
}
EOQ;
  $formatted_query_serial = format_string($query_serial, array(
    '!pid' => $pid,
  ));
  $results = $islandora_object->repository->ri->sparqlQuery($formatted_query_serial, 'unlimited');
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
      else if (strpos($base_url, 'palmm') !== false) {
        $parsed_pid = explode(':',$collection->id);
        $full_description .= '<li>';
        $full_description .=  l($collection->label, str_replace('palmm', $parsed_pid[0], $base_url) . "/islandora/object/{$collection->id}");
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

    $non_public_sites = array("fiu", "uf", "unf", "usf");

    if (strpos($base_url, 'palmm') === false)
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

// Test to see if the object is embargoed, and if so, when does it expire? 
function get_embargo_status($islandora_object) {
  if (!islandora_datastream_load('RELS-INT', $islandora_object)) {
    $embargo_data['embargoed'] = FALSE;
    $embargo_data['expiry_msg'] = "";
  } 
  else {
    // This is not the right way to get the embargo date, replace with tuque relationships call later 
    $rels_int_xml = $islandora_object['RELS-INT']->content;
    $xml_obj = simplexml_load_string($rels_int_xml);
    $xml_obj->registerXPathNamespace('islandora-embargo', 'info:islandora/islandora-system:def/scholar#');
    $expiry_array = $xml_obj->xpath('//islandora-embargo:embargo-until');
    if (!empty($expiry_array)) {
      $embargo_data['embargoed'] = TRUE;
      $expiry = $expiry_array[0][0];
      if ($expiry == "indefinite") {
        $embargo_data['expiry_msg'] = "Embargoed indefinitely";
      }
      else {
        $expiry_date = date("M j, Y", strtotime($expiry));
        $embargo_data['expiry_msg'] = "Embargoed until {$expiry_date}";
      }
    } 
    else {
      $embargo_data['embargoed'] = FALSE;
      $embargo_data['expiry_msg'] = "";
    }
  }
  return $embargo_data;
}

function get_usage_stats($islandora_object) {
  global $base_url;
  $usage_stats_json = file_get_contents("{$base_url}/islandora_usage_stats_callbacks/object_stats/{$islandora_object->id}");
  $usage_stats_array = json_decode($usage_stats_json, TRUE);
  $views = count($usage_stats_array['views']) + $usage_stats_array['legacy-views'];
  $downloads = count($usage_stats_array['downloads']) + $usage_stats_array['legacy-downloads'];
  $view_icon_path = $base_url . "/sites/all/themes/islandoratheme/images/view_icon.png";
  $download_icon_path = $base_url . "/sites/all/themes/islandoratheme/images/download_icon.png";
  
  $usage_data = array('views' => $views, 'view_icon_path' => $view_icon_path, 'downloads' => $downloads, 'download_icon_path' => $download_icon_path);
  return $usage_data;
}

function build_sharing_button_html($islandora_object) {
  global $base_url;
  $pid = $islandora_object->id;
  $title = urlencode($islandora_object->label);
  $sharing_button_html = <<<EOS
                    <!-- Sharingbutton Facebook -->
                    <a class="resp-sharing-button__link" href="https://facebook.com/sharer/sharer.php?u=$base_url/islandora/object/$pid" target="_blank" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--facebook resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <g>
                                <path d="M18.768,7.465H14.5V5.56c0-0.896,0.594-1.105,1.012-1.105s2.988,0,2.988,0V0.513L14.171,0.5C10.244,0.5,9.5,3.438,9.5,5.32 v2.145h-3v4h3c0,5.212,0,12,0,12h5c0,0,0-6.85,0-12h3.851L18.768,7.465z"/>
                            </g>
                        </svg>
                        </div>
                      </div>
                    </a>

                    <!-- Sharingbutton Twitter -->
                    <a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text=%22$title%22&amp;url=$base_url/islandora/object/$pid" target="_blank" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--twitter resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <g>
                                <path d="M23.444,4.834c-0.814,0.363-1.5,0.375-2.228,0.016c0.938-0.562,0.981-0.957,1.32-2.019c-0.878,0.521-1.851,0.9-2.886,1.104 C18.823,3.053,17.642,2.5,16.335,2.5c-2.51,0-4.544,2.036-4.544,4.544c0,0.356,0.04,0.703,0.117,1.036 C8.132,7.891,4.783,6.082,2.542,3.332C2.151,4.003,1.927,4.784,1.927,5.617c0,1.577,0.803,2.967,2.021,3.782 C3.203,9.375,2.503,9.171,1.891,8.831C1.89,8.85,1.89,8.868,1.89,8.888c0,2.202,1.566,4.038,3.646,4.456 c-0.666,0.181-1.368,0.209-2.053,0.079c0.579,1.804,2.257,3.118,4.245,3.155C5.783,18.102,3.372,18.737,1,18.459 C3.012,19.748,5.399,20.5,7.966,20.5c8.358,0,12.928-6.924,12.928-12.929c0-0.198-0.003-0.393-0.012-0.588 C21.769,6.343,22.835,5.746,23.444,4.834z"/>
                            </g>
                        </svg>
                        </div>
                      </div>
                    </a>

                    <!-- Sharingbutton Google+ -->
                    <a class="resp-sharing-button__link" href="https://plus.google.com/share?url=$base_url/islandora/object/$pid" target="_blank" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--google resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <g>
                                <path d="M11.366,12.928c-0.729-0.516-1.393-1.273-1.404-1.505c0-0.425,0.038-0.627,0.988-1.368 c1.229-0.962,1.906-2.228,1.906-3.564c0-1.212-0.37-2.289-1.001-3.044h0.488c0.102,0,0.2-0.033,0.282-0.091l1.364-0.989 c0.169-0.121,0.24-0.338,0.176-0.536C14.102,1.635,13.918,1.5,13.709,1.5H7.608c-0.667,0-1.345,0.118-2.011,0.347 c-2.225,0.766-3.778,2.66-3.778,4.605c0,2.755,2.134,4.845,4.987,4.91c-0.056,0.22-0.084,0.434-0.084,0.645 c0,0.425,0.108,0.827,0.33,1.216c-0.026,0-0.051,0-0.079,0c-2.72,0-5.175,1.334-6.107,3.32C0.623,17.06,0.5,17.582,0.5,18.098 c0,0.501,0.129,0.984,0.382,1.438c0.585,1.046,1.843,1.861,3.544,2.289c0.877,0.223,1.82,0.335,2.8,0.335 c0.88,0,1.718-0.114,2.494-0.338c2.419-0.702,3.981-2.482,3.981-4.538C13.701,15.312,13.068,14.132,11.366,12.928z M3.66,17.443 c0-1.435,1.823-2.693,3.899-2.693h0.057c0.451,0.005,0.892,0.072,1.309,0.2c0.142,0.098,0.28,0.192,0.412,0.282 c0.962,0.656,1.597,1.088,1.774,1.783c0.041,0.175,0.063,0.35,0.063,0.519c0,1.787-1.333,2.693-3.961,2.693 C5.221,20.225,3.66,19.002,3.66,17.443z M5.551,3.89c0.324-0.371,0.75-0.566,1.227-0.566l0.055,0 c1.349,0.041,2.639,1.543,2.876,3.349c0.133,1.013-0.092,1.964-0.601,2.544C8.782,9.589,8.363,9.783,7.866,9.783H7.865H7.844 c-1.321-0.04-2.639-1.6-2.875-3.405C4.836,5.37,5.049,4.462,5.551,3.89z"/>
                                <polygon points="23.5,9.5 20.5,9.5 20.5,6.5 18.5,6.5 18.5,9.5 15.5,9.5 15.5,11.5 18.5,11.5 18.5,14.5 20.5,14.5 20.5,11.5  23.5,11.5 	"/>
                            </g>
                        </svg>
                        </div>
                      </div>
                    </a>

                    <!-- Sharingbutton E-Mail -->
                    <a class="resp-sharing-button__link" href="mailto:?subject=%22$title%22&amp;body=$base_url/islandora/object/$pid" target="_self" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <path d="M22,4H2C0.897,4,0,4.897,0,6v12c0,1.103,0.897,2,2,2h20c1.103,0,2-0.897,2-2V6C24,4.897,23.103,4,22,4z M7.248,14.434 l-3.5,2C3.67,16.479,3.584,16.5,3.5,16.5c-0.174,0-0.342-0.09-0.435-0.252c-0.137-0.239-0.054-0.545,0.186-0.682l3.5-2 c0.24-0.137,0.545-0.054,0.682,0.186C7.571,13.992,7.488,14.297,7.248,14.434z M12,14.5c-0.094,0-0.189-0.026-0.271-0.08l-8.5-5.5 C2.997,8.77,2.93,8.46,3.081,8.229c0.15-0.23,0.459-0.298,0.691-0.147L12,13.405l8.229-5.324c0.232-0.15,0.542-0.084,0.691,0.147 c0.15,0.232,0.083,0.542-0.148,0.691l-8.5,5.5C12.189,14.474,12.095,14.5,12,14.5z M20.934,16.248 C20.842,16.41,20.673,16.5,20.5,16.5c-0.084,0-0.169-0.021-0.248-0.065l-3.5-2c-0.24-0.137-0.323-0.442-0.186-0.682 s0.443-0.322,0.682-0.186l3.5,2C20.988,15.703,21.071,16.009,20.934,16.248z"/>
                        </svg>
                        </div>
                      </div>
                    </a>
EOS;
  return $sharing_button_html;
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
 * Override or insert variables for the view templates.
 */
/*
function islandoratheme_preprocess_views_view(&$variables) {
  //drupal_add_css(drupal_get_path('theme', 'islandoratheme') . '/css/datatables.css', array('group' => CSS_THEME, 'type' => 'file'));
}

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
