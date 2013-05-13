<?php

/**
 * @file
 * islandora-basic-collection-wrapper.tpl.php
 * 
 * @TODO: needs documentation about file and variables
 * @TODO: don't set drupal_set_title() here.
 */
?>
<?php global $base_url ?>
<?php drupal_set_title($islandora_object->label); ?>

<div class="islandora-basic-collection-wrapper">
  <?php
    if (isset($islandora_object['BANNER'])) {
      $banner_url = '/islandora/object/' . $islandora_object->id . '/datastream/BANNER/view';
      print '<p><img src="' . $banner_url . '"></p>';
    }
    else {
      print '<h1 id="page-title">' . $islandora_object->label . '</h1>';
    }
    
    if (isset($islandora_object['DESC-TEXT'])) {
      $description_text_url = '/islandora/object/' . $islandora_object->id . '/datastream/DESC-TEXT/view'; 
      $description_text = file_get_contents($base_url . $description_text_url);
      $search_url = '/islandora/object/' . $islandora_object->id; ?>
      <div id="local-collection-search">
      <div id="local-search-container">
      <h2 class="local-collection-search-title">Search this Collection</h2>
      <form id="local-collection-search-form" accept-charset="UTF-8" onsubmit="return makeURL('local-collection-search-form');" method="get" action="#">
        <div class="form-item">
          <input class="search-input-text" type="text" maxlength="128" name="islandora_simple_search_query" />
          <input type="submit" name="submit" value="search"  class="form-submit" />
        </div>
          <input type="hidden" name="islandora_simple_collection" value="<?php print $islandora_object->id ?>">
          <input type="hidden" name="base_url" value="<?php print $base_url ?>">
      </form>
      </div>

  <?php
      print '<p>' . $description_text . '</p></div>';
    }
  ?>
  
  <div class="islandora-basic-collection clearfix">
    <span class="islandora-basic-collection-display-switch">
     <?php print theme('links', array('links' => $view_links, 'attributes' => array('class' => array('links', 'inline'))));?>
     <?php print l('Go to detailed view', "islandora/search/", array('attributes' => array('class' => array('links', 'inline')), 'query' => array('type' => 'edismax', 'collection' => $islandora_object->id))); ?>
    </span>
    <?php print $collection_pager; ?>
    <?php print $collection_content; ?>
    <?php print $collection_pager; ?>
  </div>
</div>
