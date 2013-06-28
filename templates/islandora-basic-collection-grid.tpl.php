<?php

/**
 * @file
 * islandora-basic-collection.tpl.php
 *
 * @TODO: needs documentation about file and variables
 */
?>

<?php global $base_url; ?>

<div class="islandora islandora-basic-collection">
  <div class="islandora-basic-collection-grid clearfix">

  <?php $number_of_collections = 0; ?>
                                   
  <?php foreach($associated_objects_array as $key => $value): ?>
    
  <?php $collection_policy = $value['object']->getDataStream('COLLECTION_POLICY'); ?>
 
    <dl class="islandora-basic-collection-object <?php print $value['class']; ?>">
        <dt class="islandora-basic-collection-thumb"><?php print $value['thumb_link']; ?></dt>
        <dd class="islandora-basic-collection-caption"><?php print $value['title_link']; ?></dd>
    </dl>
  
  <?php if($collection_policy): ?>
  <?php $number_of_collections++; ?>
  <?php endif; ?>

  <?php endforeach; ?>

  <?php
    
    //This code sends drupal to the search display if this is the bottom collection
    if ($number_of_collections == 0 && !user_is_logged_in()) {
      $dc_array = DublinCore::importFromXMLString($islandora_object['DC']->content)->asArray();
      echo drupal_goto($base_url. '/islandora/search?type=edismax&collection=' . $dc_array['dc:identifier']['value']);
    }
  
  ?>
</div>
</div>