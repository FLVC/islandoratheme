<?php

/**
 * @file
 * Template file for default facets
 *
 * @TODO document available variables
 */
?>

<table class="<?php print $classes; ?>">
  <?php foreach($buckets as $key => $bucket): ?>
    <tr>
      <td>
      <?php print $bucket['link']; ?>
      <span class="count">(<?php print $bucket['count']; ?>)</span>
      </td>
      <td>
      <span class="plusminus">
        <?php print $bucket['link_plus']; ?>
        <?php print $bucket['link_minus']; ?>
      </span>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
