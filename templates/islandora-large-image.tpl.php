<?php

/**
 * @file
 * This file overrides the default template provided by the islandora large image module.
 *
 * Available variables:
 * - $islandora_object: The Islandora object rendered in this template file
 * - $islandora_dublin_core: The DC datastream object
 * - $dc_array: The DC datastream object values as a sanitized array. This 
 *   includes label, value and class name.
 * - $islandora_object_label: The sanitized object label.
 * - $parent_collections: An array containing parent collection(s) info.
 *   Includes collection object, label, url and rendered link.
 * - $islandora_thumbnail_img: A rendered thumbnail image.
 * - $islandora_content: A rendered image. By default this is the JPG datastream
 *   which is a medium sized image. Alternatively this could be a rendered
 *   viewer which displays the JP2 datastream image.
 *
 * @see template_preprocess_islandora_large_image()
 * @see theme_islandora_large_image()
 */
?>

<?php

if (isset($islandora_object_label))
{
  drupal_set_title("$islandora_object_label");
}

?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Summary</a></li>
  <li><a href="#tabs-2">Full Description</a></li>
</ul>

<div id="tabs-1">

<div class="islandora-basic-image-object islandora">
  <div class="islandora-pdf-content-wrapper clearfix">
    <?php if (isset($islandora_content)): ?>
      <div class="islandora-pdf-content">
        <p><?php print $islandora_content; ?></p>
      </div>
    <?php endif; ?>

  <div class="islandora-pdf-sidebar">
    <dl>
      <?php if(isset($mods_array['mods:date']['value'])): ?>
        <dt><?php print $mods_array['mods:date']['label']; ?>:</dt>
        <dd><?php print $mods_array['mods:date']['value']; ?></dd>
      <?php endif; ?>
      <?php if(isset($mods_array['mods:description']['value'])): ?>
        <dt><?php print $mods_array['mods:description']['label']; ?>:</dt>
        <dd><?php print $mods_array['mods:description']['value']; ?></dd>
      <?php endif; ?>
    </dl>
  </div>

</div>
</div>
</div>
<div id="tabs-2">
    <div class="islandora-basic-image-sidebar">
      <?php if(isset($islandora_medium_img)): ?>
        <div class="islandora-basic-image-thumbnail">
        <?php if(isset($islandora_full_url)): ?>
          <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
        <?php elseif(isset($islandora_thumbnail_img)): ?>
          <?php print $islandora_thumbnail_img; ?>
        <?php endif; ?>
        </div>
      <?php endif; ?>
      <div>
	<dl class="islandora-table-display">
        <?php $row_field = 0; ?>
        <?php foreach($mods_array as $key => $value): ?>
          
          <?php if($value['value'] != ''): ?>

            <dt class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['label']; ?>:
            </dt>
            <dd class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['value']; ?>
            </dd>
      
          <?php endif; ?>
          <?php $row_field++; ?>
        <?php endforeach; ?>
        </dl>
      </div>
      <?php if($parent_collections): ?>
        <div>
          <h2>In Collections</h2>
          <ul>
            <?php foreach($parent_collections as $key => $value): ?>
              <li><?php print $value['label_link'] ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
</div>
</div>
