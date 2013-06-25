<?php

/**
 * @file
 * islandora-basic-collection.tpl.php
 *
 * @TODO: needs documentation about file and variables
 */
?>

<div class="islandora islandora-basic-collection">
  <div class="islandora-basic-collection-grid clearfix">
  <?php foreach($associated_objects_array as $key => $value): ?>
    
  <?php $COLLECTION_POLICY = $value['object']->getDataStream('COLLECTION_POLICY'); ?>

  <?php if($COLLECTION_POLICY): ?>
    <dl class="islandora-basic-collection-object <?php print $value['class']; ?>">
        <dt class="islandora-basic-collection-thumb"><?php print $value['thumb_link']; ?></dt>
        <dd class="islandora-basic-collection-caption"><?php print $value['title_link']; ?></dd>
    </dl>
  <?php endif; ?>

  <?php endforeach; ?>
</div>
</div>
