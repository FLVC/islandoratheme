<?php
/**
 * @file
 * This is the template file for the object page for organization objects.
 */
?>

<div class="islandora-object islandora">
<?php if (isset($variables['tn'])): ?>
  <dl class="islandora-object-tn islandora-department">
    <dt>
      <img src="<?php print $variables['tn']; ?>"/>
    </dt>
  </dl>
<?php endif; ?>
<?php if (isset($variables['metadata'])): ?>
  <div class="departmental_metadata">
    <?php print $variables['metadata']; ?>
  </div>
<?php endif; ?>
<?php if (isset($parent_organization)): ?>
  <div class="view--parent-department">
      <?php print views_embed_view('parent_department', 'parent_organization', $parent_organization); ?>
  </div>
<?php endif; ?>
<div class="view--child-departments">
  <?php print views_embed_view('child_departments', 'child_organizations', $object->label); ?>
</div>
<div class="other-scholars islandora-object-scholars">
  <!--<?php print views_embed_view('other_scholars_in_dept', 'dept_scholars', $object->label); ?>-->
  <?php print views_embed_view('browse_scholars_by_dept', 'block', $object->label); ?>
</div>

<br><br>
<h3>Department Hierarchy View</h3>
<?php print $organization_tree; ?>

</div>
