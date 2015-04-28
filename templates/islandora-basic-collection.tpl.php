<?php

/*
 * islandora-basic-collection.tpl.php
 * 
 *
 * 
 * This file is part of Islandora.
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

<div class="islandora islandora-basic-collection">
    <?php $row_field = 0; ?>
    <?php $description_length = 300; ?>
    <?php $number_of_collections = 0; ?>
    <?php foreach($associated_objects_mods_array as $associated_object): ?>
      <div class="islandora-basic-collection-object islandora-basic-collection-list-item clearfix"> 
        <dl class="<?php print $associated_object['class']; ?>">
            <dt>
              <?php if (isset($associated_object['thumb_link'])): ?>
                <?php print $associated_object['thumb_link']; ?>
              <?php endif; ?>
            </dt>
            <dd class="collection-value <?php print isset($associated_object['mods_array']['mods:title']['class']) ? $associated_object['mods_array']['mods:title']['class'] : ''; ?> <?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php if (isset($associated_object['thumb_link'])): ?>
                <strong>Title: </strong><?php print $associated_object['title_link']; ?>
              <?php endif; ?>
            </dd>
            <dd>
              <?php if (isset($associated_object['collection_description']) && strlen($associated_object['collection_description']) <= $description_length) : ?>
                <strong>Description: </strong><?php print $associated_object['collection_description']; ?>
              <?php elseif (isset($associated_object['collection_description'])): ?>
                <strong>Description: </strong><?php print strip_tags(substr($associated_object['collection_description'], 0, $description_length)) . '...'; ?>
              <?php endif; ?>
            </dd>
            <?php if (isset($associated_object['mods_array']['mods:description']['value']) && $associated_object['mods_array']['mods:description']['value'] != ''): ?>
              <dd class="collection-value <?php print $associated_object['mods_array']['mods:description']['class']; ?>">
                <strong><?php print $associated_object['mods_array']['mods:description']['label']; ?>: </strong><?php print strip_tags($associated_object['mods_array']['mods:description']['short_value']); ?>
              </dd>
            <?php endif; ?>
            <?php if (isset($associated_object['mods_array']['mods:date']['value']) && $associated_object['mods_array']['mods:date']['value'] != ''): ?>
              <dd class="collection-value <?php print $associated_object['mods_array']['mods:date']['class']; ?>">
                <strong><?php print $associated_object['mods_array']['mods:date']['label']; ?>: </strong><?php print $associated_object['mods_array']['mods:date']['value']; ?>
              </dd>
            <?php endif; ?>
            <?php if (isset($associated_object['mods_array']['mods:identifier']['value']) && $associated_object['mods_array']['mods:identifier']['value'] != ''): ?>
              <dd class="collection-value <?php print $associated_object['mods_array']['mods:identifier']['class']; ?>">
                <strong><?php print $associated_object['mods_array']['mods:identifier']['label']; ?>: </strong><?php print $associated_object['mods_array']['mods:identifier']['value']; ?>
              </dd>
            <?php endif; ?>            
        </dl>
      </div>
      <?php $collection_policy = $associated_object['object']->getDataStream('COLLECTION_POLICY'); ?>
      <?php if($collection_policy): ?>
      <?php $number_of_collections++; ?>
      <?php endif; ?>
    <?php $row_field++; ?>
    <?php endforeach; ?>

  <?php
    
    //This code sends drupal to the search display if this is the bottom collection
    if ($number_of_collections == 0 && !user_is_logged_in()) {
      $dc_array = DublinCore::importFromXMLString($islandora_object['DC']->content)->asArray();
      echo drupal_goto($base_url. '/islandora/search?type=edismax&collection=' . $dc_array['dc:identifier'][
'value']);
    }
  
  ?>

</div>
