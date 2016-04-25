<?php
/*
 * islandora-scholar-citation.tpl.php
 * 
 *
 * 
 * This file overrides the default template provided by the islandora scholar module.
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

if (isset($islandora_object->label))
{
  drupal_set_title($islandora_object->label);
}

?>

<div>
  <h3><?php print $islandora_object->label ?></h3>
</div>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Full Description</a></li>
  
  <?php if (isset($parent_mods_array)): ?>
    <li><a href="#tabs-2">Item Description</a></li>
    <li><a href="#tabs-3">Set Description</a></li>
  <?php elseif (isset($serial_mods_array)): ?>
    <li><a href="#tabs-2">Full Description</a></li>
    <li><a href="#tabs-3">Serial Details</a></li>
  <?php else: ?>
    <?php if (!$embargoed) { ?>
    <li><a href="#tabs-2">View Document</a></li>
    <?php } ?>
  <?php endif; ?>
</ul>

<div id="tabs-1">
    <div class="islandora-citation-image-sidebar">
      <div>
        <table class="islandora-table-display" width="100%">
        <tbody>
        <?php $row_field = 0; ?>
        <?php foreach($mods_array as $key => $value): ?>
          <?php if(trim($value['value']) != ''): ?>
            <tr class="islandora-definition-row <?php print $value['class']; ?>">
            <th class="full-description-heading<?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['label']; ?>:
            </th>
            <td class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['value']; ?>
            </td>

            <?php if($row_field == 0): ?>
              <td class="islandora-basic-image-thumbnail" rowspan="8">

                  <?php if(isset($usage_views) && isset($usage_downloads)): ?>
                    <?php print "<span class=\"usage-stats-views\">Views: $usage_views</span>"; ?>
                    <?php print "<span class=\"usage-stats-downloads\">Downloads: $usage_downloads</span>"; ?>
                  <?php endif; ?>

                  <?php if(isset($islandora_full_url)): ?>
                    <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>

                  <?php elseif(isset($islandora_thumbnail_img)): ?>
                    <?php print '<img src="' . $islandora_thumbnail_img . '">'; ?>

                  <?php endif; ?>

                  <?php if (!$embargoed) { ?>
                    <?php if(isset($islandora_download_link)): ?>
                      <a href="<?php print $islandora_download_link; ?>">
                        <button class="download">Download PDF</button>
                      </a>
                    <?php endif; ?>

                  <?php } else { ?>
                  <button disabled class="download">Download PDF</button>
                  <p style="margin-top:10px;" class="error"><?php print $expiry_msg ?></p>

                  <?php } ?>

              </td>

            <?php endif; ?>
            </tr>
            <?php $row_field++; ?>
          <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
        </table>
      </div>
    </div>
</div>

<div id="tabs-2">
  <div class="islandora-citation-object islandora">
    <div class="islandora-citation-content-wrapper clearfix">
      <?php if(isset($citation_view)): ?>
        <div class="islandora-citation-content">
          <p><embed height="600" src="<?php print $citation_view ?>" width="100%"></embed></p>
        </div>
      <?php endif; ?>
    </div>
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
