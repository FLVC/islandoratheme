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
        <dd class="islandora-basic-collection-caption"><a href="<?php print $base_url . '/' . $value['path']; ?>"><?php print substr($value['title'], 0, 100); ?></a></dd>
    </dl>
  
  <?php if($collection_policy): ?>
  <?php $number_of_collections++; ?>
  <?php endif; ?>

  <?php endforeach; ?>

  <?php
    
    //This code sends drupal to the search display if this is the bottom collection
    if ($number_of_collections == 0 && !user_is_logged_in()) {
      echo drupal_goto($base_url. '/islandora/search?type=edismax&collection=' . $islandora_object->id);
    }

    // For PALMM, all non-administrator users go to search display if at bottom collection
    if ($number_of_collections == 0 && substr($islandora_object->id, 0, 5) == 'palmm' && !user_has_role(user_role_load_by_name('administrator')->rid)) {
      echo drupal_goto($base_url. '/islandora/search?type=edismax&collection=' . $islandora_object->id);
    }
  
  ?>
</div>
</div>
