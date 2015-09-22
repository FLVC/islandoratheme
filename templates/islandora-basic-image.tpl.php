<?php

/*
 * islandora-basic-image.tpl.php
 * 
 *
 * 
 * This file overrides the default template provided by the islandora basic image module.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with the program.  If not, see <http ://www.gnu.org/licenses/>.
 */
?>

<?php global $base_url; ?>

<?php if(isset($islandora_object_label)): ?>
  <?php drupal_set_title("$islandora_object_label"); ?>
<?php endif; ?>

<div class="islandora-title">
  <h3><?php print $islandora_object_label ?></h3>
  <p><?php print $islandora_view_link; ?> </p>
</div>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Summary</a></li>

  <?php if (isset($parent_mods_array)): ?>
    <li><a href="#tabs-2">Item Description</a></li>
    <li><a href="#tabs-3">Set Description</a></li>
  <?php else: ?>
    <li><a href="#tabs-2">Full Description</a></li>
  <?php endif; ?>
</ul>

<div id="tabs-1">

<div class="islandora-basic-image-object islandora">
  <div class="islandora-basic-image-content-wrapper clearfix">
    <?php if(isset($islandora_medium_img)): ?>
      <div class="islandora-basic-image-content">
      <?php if(isset($islandora_full_url)): ?>
        <?php print l($islandora_medium_img, $islandora_full_url, array('html' => TRUE)); ?>
      <?php elseif(isset($islandora_medium_img)): ?>
        <?php print $islandora_medium_img; ?>
      <?php endif; ?>
      </div>
    <?php endif; ?>
  <div class="islandora-basic-image-sidebar">
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
    <div class="islandora-basic-image-sidebar">
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
              <?php if(isset($islandora_medium_img)): ?>
                <td class="islandora-basic-image-thumbnail" rowspan="8"> 
                <?php if(isset($islandora_full_url)): ?>
                  <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
                <?php elseif(isset($islandora_thumbnail_img)): ?>
                  <?php print $islandora_thumbnail_img; ?>
                <?php endif; ?>
                </td>
              <?php endif; ?>
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

<div id="tabs-3">
    <div class="islandora-basic-image-sidebar">
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
              <?php if(isset($islandora_medium_img)): ?>
                <td class="islandora-basic-image-thumbnail" rowspan="8">
                <?php if(isset($parent_thumbnail_img)): ?>
                  <?php print $parent_thumbnail_img; ?>
                <?php elseif(isset($islandora_full_url)): ?>
                  <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>
                <?php elseif(isset($islandora_thumbnail_img)): ?>
                  <?php print $islandora_thumbnail_img; ?>
                <?php endif; ?>
                </td>
              <?php endif; ?>
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
