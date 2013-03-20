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
      print '<p>' . $description_text . '</p>';
    }
  ?>
  <div class="islandora-basic-collection clearfix">
    <span class="islandora-basic-collection-display-switch">
     <?php print theme('links', array('links' => $view_links, 'attributes' => array('class' => array('links', 'inline'))));?>
    </span>
    <?php print $collection_pager; ?>
    <?php print $collection_content; ?>
    <?php print $collection_pager; ?>
  </div>
</div>
