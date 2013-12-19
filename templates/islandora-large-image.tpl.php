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
  <p><?php print $islandora_download_link; ?> </p>
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
    <div class="clearfix">
      <div>
        <table class="islandora-table-display">
        <tbody>
        <?php $row_field = 0; ?>
        <?php foreach($mods_array as $key => $value): ?>

          <?php if(trim($value['value']) != ''): ?>

            <tr class="islandora-definition-row">
            <th class="full-description-heading<?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['label']; ?>:
            </th>
            <td class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['value']; ?>
            </td>

            <?php if($row_field == 0): ?>             
              <td class="islandora-large-image-thumbnail" rowspan="8">
                <?php if(isset($islandora_full_url)): ?>
                  <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
                <?php elseif(isset($islandora_thumbnail_img)): ?>
                  <a href="javascript:document.location.reload();"><?php print $islandora_thumbnail_img; ?></a>
                <?php endif; ?>
              </td>
            <?php endif; ?>
          
            </tr>

            <?php $row_field++; ?>

          <?php endif; ?>

        <?php endforeach; ?>
        </tbody>
        </table>
      </div>
      <?php if($parent_collections): ?>
        <div>
          <h2>In Collections</h2>
          <ul>
	    <?php foreach ($parent_collections as $collection): ?>
               <?php if(substr($collection->id, 0, 5) == 'palmm'): ?>
                 <li><?php print l($collection->label, "http://palmm.digital.flvc.org/islandora/object/{$collection->id}"); ?></li>
               <?php else: ?>
                 <li><?php print l($collection->label, "islandora/object/{$collection->id}"); ?></li>
       	       <?php endif; ?>
	    <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
</div>
</div>
<div class="islandora-object-branding">
  <ul>
    <!--- START OTHERLOGO DISPLAY -->
    <?php $local_counter = 0; ?>
    <?php while (isset($branding_info['other_logo_' . $local_counter]) && $local_counter < 3): ?>
      <li><a href="<?php print $branding_info['other_logo_' . $local_counter]['institution_link'] ?>" target="_blank">  	
      <img src="<?php print base_path() . variable_get('file_public_path', conf_path() . '/files') . '/custom_logos/' . $branding_info['other_logo_' . $local_counter]['image_filename'] ?>"></a>
      </li>
      <?php $local_counter++; ?>
    <?php endwhile; ?>
    <!--- END OF OTHERLOGO DISPLAY -->      
    
    <li><a href="<?php print $branding_info['institution_logo']['institution_link'] ?>" target="_blank">
    <img src="<?php print base_path() . variable_get('file_public_path', conf_path() . '/files') . '/custom_logos/' . $branding_info['institution_logo']['image_filename'] ?>"></a>
    </li>
  </ul>
</div>
