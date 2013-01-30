<?php

/*
 * islandora-pdf.tpl.php
 * 
 *
 * 
 * This file overrides the default template provided by the islandora PDF module.
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

<?php

if (isset($islandora_object_label))
{
  drupal_set_title("$islandora_object_label");
}

if (isset($islandora_content))
{
  $regex = '$\b(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i';
  preg_match($regex, $islandora_content, $pdf_url);
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
        <p><embed height="600" src="<?php print $pdf_url[0]; ?>" width="100%"></embed></p>
      </div>
      <p><?php print $islandora_download_link; ?></p>
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
        <div class="islandora-basic-image-thumbnail">
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
	    <?php foreach ($parent_collections as $collection): ?>
               <li><?php print l($collection->label, "islandora/object/{$collection->id}"); ?></li>
	    <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
</div>
</div>

