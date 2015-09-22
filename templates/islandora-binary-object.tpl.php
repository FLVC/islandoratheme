<?php

/**
 * @file
 * This is the template file for the object page for binary objects.
 *
 * Available variables:
 * - $islandora_object: The Islandora object rendered in this template file.
 * - $islandora_binary_object_info: Information about the binary object.
 * - $islandora_thumbnail_img: The thumbnail image of the binary object.
 * - $islandora_File_storage_download: Download link for the object.
 * - $description: Defined metadata descripton for the object.
 * - $parent_collections: Parent collections of the object if applicable.
 * - $metadata: Rendered metadata display for the binary object.
 *
 * @see template_preprocess_islandora_binary_object()
 * @see theme_islandora_binary_object()
 */
?>

<?php global $base_url; ?>

<?php if(isset($islandora_object_label)): ?>
  <?php drupal_set_title("$islandora_object_label"); ?>
<?php endif; ?>

<div class="islandora-title">
  <h3><?php print $islandora_object_label ?></h3>
  <?php if (isset($islandora_binary_object_info)): ?>
    <p><?php print $islandora_binary_object_info; ?> -> <?php print $islandora_download_link; ?></p>
  <?php endif; ?>
</div>

<div id="tabs">

<ul>

  <?php if (isset($parent_mods_array)): ?>
    <li><a href="#tabs-1">Item Description</a></li>
    <li><a href="#tabs-2">Set Description</a></li>
  <?php else: ?>
    <li><a href="#tabs-1">Full Description</a></li>
  <?php endif; ?>

</ul>

<div id="tabs-1">
    <div class="islandora-binary-object-sidebar">
      <div>
	<table class="islandora-table-display" width="100%">
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
              <td class="islandora-binary-object-thumbnail" rowspan="8"> 
              <?php if(isset($islandora_full_url)): ?>
                <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
              <?php elseif(isset($islandora_thumbnail_img)): ?>
                <?php print $islandora_thumbnail_img; ?>
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
                  <li><?php print l($collection->label . " (PALMM)", "http://palmm.digital.flvc.org/islandora/object/{$collection->id}"); ?></li>
               <?php elseif (strpos($base_url, 'palmm') !== false): ?>
                  <?php $parsed_pid = explode(':',$collection->id); ?>
                  <li><?php print l($collection->label, str_replace('palmm', $parsed_pid[0], $base_url) . "/islandora/object/{$collection->id}"); ?></li>
               <?php else: ?>
                 <li><?php print l($collection->label, "islandora/object/{$collection->id}"); ?></li>
               <?php endif; ?>
	    <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
</div>

<!--- Parent Object Metadata Display (if Compound Object) -->
<?php if (isset($parent_mods_array)): ?>

<div id="tabs-2">
    <div class="islandora-binary-object-sidebar">
      <div>
	<table class="islandora-table-display" width="100%">
        <tbody>
        <?php $row_field = 0; ?>
        <?php foreach($parent_mods_array as $key => $value): ?>

          <?php if(trim($value['value']) != ''): ?>

            <tr class="islandora-definition-row">
            <th class="full-description-heading<?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['label']; ?>:
            </th>
            <td class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['value']; ?>
            </td>

            <?php if($row_field == 0): ?>
              <td class="islandora-binary-object-thumbnail" rowspan="8">
              <?php if(isset($parent_thumbnail_img)): ?>
                <?php print $parent_thumbnail_img; ?>
              <?php elseif(isset($islandora_full_url)): ?>
                <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
              <?php elseif(isset($islandora_thumbnail_img)): ?>
                <?php print $islandora_thumbnail_img; ?>
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
                  <li><?php print l($collection->label . " (PALMM)", "http://palmm.digital.flvc.org/islandora/object/{$collection->id}"); ?></li>
               <?php elseif (strpos($base_url, 'palmm') !== false): ?>
                  <?php $parsed_pid = explode(':',$collection->id); ?>
                  <li><?php print l($collection->label, str_replace('palmm', $parsed_pid[0], $base_url) . "/islandora/object/{$collection->id}"); ?></li>
               <?php else: ?>
                 <li><?php print l($collection->label, "islandora/object/{$collection->id}"); ?></li>
               <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
</div>

<?php endif; ?>

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
