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

<div class="islandora-title">
  <h3><?php print $islandora_object_label ?></h3>
</div>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Summary</a></li>
  <li><a href="#tabs-2">Full Description</a></li>
</ul>

<div id="tabs-1">

<div class="islandora-basic-image-object islandora">
  <div class="islandora-large-image-content-wrapper clearfix">
    <?php if (isset($islandora_content)): ?>
      <div class="islandora-large-image-content">
        <p><?php print $islandora_content; ?></p>
      </div>
    <?php endif; ?>

  <div class="islandora-large-image-sidebar clearfix">
    <dl>
      <?php if(isset($mods_array['mods:date']['value'])): ?>
        <div class="islandora-definition-row">
        <dt><?php print $mods_array['mods:date']['label']; ?>:</dt>
        <dd><?php print $mods_array['mods:date']['value']; ?></dd>
        </div>
      <?php endif; ?>
      <?php if(isset($mods_array['mods:description']['value'])): ?>
        <div class="islandora-definition-row">
        <dt><?php print $mods_array['mods:description']['label']; ?>:</dt>
        <dd><?php print $mods_array['mods:description']['value']; ?></dd>
        </div>
      <?php endif; ?>
    </dl>
  </div>

</div>
</div>
</div>
<div id="tabs-2">
    <div class="islandora-large-image-sidebar clearfix">
        <div class="islandora-large-image-thumbnail">
        <?php if(isset($islandora_full_url)): ?>
          <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
        <?php elseif(isset($islandora_thumbnail_img)): ?>
          <?php print $islandora_thumbnail_img; ?>
        <?php endif; ?>
        </div>
      <div>
	<dl class="islandora-table-display">
        <?php $row_field = 0; ?>
        <?php foreach($mods_array as $key => $value): ?>
          
          <?php if(trim($value['value']) != ''): ?>
            
            <div class="islandora-definition-row">
            <dt class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['label']; ?>:
            </dt>
            <dd class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['value']; ?>
            </dd>
            </div>
      
          <?php endif; ?>
          <?php $row_field++; ?>
        <?php endforeach; ?>
        </dl>
      </div>
      <?php if($parent_collections): ?>
        <div>
          <h2>In Collections</h2>
          <ul>
	    <?php foreach ($parent_collections as $collection): ?>
               <li><?php print l($collection->label, "islandora/object/{$collection->id}"); ?></li>
	    <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
</div>
</div>
<div class="islandora-object-branding"><p><img src="<?php print $branding_info ?>"/></p></div>
