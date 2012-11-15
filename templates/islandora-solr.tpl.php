<?php

/*
 * islandora-solr.tpl.php
 * 
 *
 * 
 * This file overrides the template provided by the islandora module.
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

<?php if (empty($results)): ?>
  <p class="no-results"><?php print t('Sorry, but your search returned no results.'); ?></p>
<?php else: ?>
  <div class="islandora islandora-basic-collection">
    
    <?php
    /* Initialize variables */
      $link = '';
      $title_label = '';
      $title_value = '';
      $description_label = '';
      $description_value = '';
      $date_label = '';
      $date_value = '';
      $creator_label = '';
      $creator_value = '';
    ?>
    
    <?php $row_result = 0; ?>
    <?php foreach($results as $result): ?>
    
    <?php     
    /* Extract search results. This is necessary in order to standardize the order of the fields being displayed  */
    foreach($result as $key => $value)
    {
      switch($key) {
        case 'PID':
            $link = $value['value'];
            break;
        case 'dc.title':
            $title_label = $value['label'];
            $title_value = $value['value'];
            break;
        case 'dc.description':
            $description_label = $value['label'];
            $description_value = $value['value'];
            break;
        case 'dc.date':
            $date_label = $value['label'];
            $date_value = $value['value'];
            break;
        case 'dc.creator':
            $creator_label = $value['label'];
            $creator_value = $value['value'];
            break;
        default:
            break;
      }
    }      
    ?>
    
      <div class="islandora-basic-collection-object islandora-basic-collection-list-item clearfix"> 
        <dl>
            <dt>
              <?php $image = '<img src="' . $thumbnail_path[$row_result] . '" />'; ?>
              <?php print l($image, 'islandora/object/' . $result['PID']['value'], array('html' => TRUE)); ?>
            </dt>
            <?php $row_field = 0; ?>
            <?php $max_rows = count($results[$row_result]) - 1; ?>
              <?php if ($title_value != ''): ?>
                <?php $title_value = l($title_value, 'islandora/object/' . htmlspecialchars($link, ENT_QUOTES, 'utf-8')); ?>
                <dd class="solr-value <?php print $row_field == 0 ? ' first' : ''; ?><?php print $row_field == $max_rows ? ' last' : ''; ?>">
                  <strong><?php print $title_label; ?>: </strong><?php print $title_value; ?>
                </dd>
              <?php endif; ?>
              <?php if ($description_value != ''): ?>
                <dd class="solr-value <?php print $row_field == 0 ? ' first' : ''; ?><?php print $row_field == $max_rows ? ' last' : ''; ?>">
                  <strong><?php print $description_label; ?>: </strong><?php print $description_value; ?>
                </dd>
              <?php endif; ?>
              <?php if ($date_value != ''): ?>
                <dd class="solr-value <?php print $row_field == 0 ? ' first' : ''; ?><?php print $row_field == $max_rows ? ' last' : ''; ?>">
                  <strong><?php print $date_label; ?>:</strong> <?php print $date_value; ?>
                </dd>
              <?php endif; ?>                
              <?php if ($creator_value != ''): ?>
                <dd class="solr-value <?php print $row_field == 0 ? ' first' : ''; ?><?php print $row_field == $max_rows ? ' last' : ''; ?>">
                  <strong><?php print $creator_label; ?>:</strong> <?php print $creator_value; ?>
                </dd>
              <?php endif; ?> 
              <?php $row_field++; ?>          
        </dl>
      </div>
    <?php $row_result++; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>