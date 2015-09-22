<?php

/*
 * islandora-internet-archive-bookreader.tpl.php
 
 *
 * 
 * This file overrides the default template provided by the islandora internet archive bookreader module.
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

<?php if(isset($mods_array['mods:title']['value'])): ?>
<div class="islandora-title">
  <h4><?php print $mods_array['mods:title']['value'] ?></h4>
</div>
<?php endif; ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Summary</a></li>
  <?php if (isset($newspaper_mods_array)): ?>
    <li><a href="#tabs-2">Issue Details</a></li>
    <li><a href="#tabs-3">Newspaper Details</a></li>
  <?php else: ?>
    <li><a href="#tabs-2">Full Description</a></li>
  <?php endif; ?>
</ul>

<div id="tabs-1">

<div class="islandora-book-object islandora">
  <div class="islandora-book-content-wrapper clearfix">
    <div class="islandora-book-content">
      <div id="BookReader" class="islandora-internet-archive-bookreader">
        Loading the Internet Archive Bookreader, please wait...
      </div>
    </div>
  <div class="islandora-book-sidebar">
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
    <div class="islandora-book-sidebar">
      
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
            <td class="islandora-book-thumbnail" rowspan="8">
              <?php if(isset($islandora_tn_html)): ?>
                <a href="javascript:document.location.reload();"><?php print $islandora_tn_html; ?></a>
              <?php endif; ?>
            </td>
          <?php endif; ?>
          
          </tr>

          <?php $row_field++; ?>

        <?php endif; ?>

      <?php endforeach; ?>
      </tbody>
      </table>
      
      <?php if(isset($parent_collections)): ?>
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

<?php if (isset($newspaper_mods_array)): ?>
<div id="tabs-3">
    <div class="islandora-book-sidebar">
      
      <table class="islandora-table-display">
      <tbody>
      <?php $row_field = 0; ?>
      <?php foreach($newspaper_mods_array as $key => $value): ?>

        <?php if(trim($value['value']) != ''): ?>

          <tr class="islandora-definition-row">
          <th class="full-description-heading<?php print $row_field == 0 ? ' first' : ''; ?>">
            <?php print $value['label']; ?>:
          </th>
          <td class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
            <?php print $value['value']; ?>
          </td>

          <?php if($row_field == 0): ?>             
            <td class="islandora-book-thumbnail" rowspan="8">
              <?php if(isset($islandora_tn_html)): ?>
                <a href="javascript:document.location.reload();"><?php print $newspaper_tn_html; ?></a>
              <?php endif; ?>
            </td>
          <?php endif; ?>          
          </tr>

          <?php $row_field++; ?>

        <?php endif; ?>

      <?php endforeach; ?>
      </tbody>
      </table>
      
      <?php if(isset($newspaper_parent_collections)): ?>
        <div>
          <h2>In Collections</h2>
          <ul>
            <?php foreach ($newspaper_parent_collections as $collection): ?>
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
