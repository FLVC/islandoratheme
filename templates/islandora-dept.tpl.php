<?php
/**
 * @file
 * This is the template file for the object page for organization objects.
 */
?>

<div class="islandora-object islandora">

<?php if (!empty($organization_tree)): ?>
  <div class="department-tree-link">
  <a href="#DEPT_TREE"><h4>Show Department Hierarchy</h4></a></li><div id="DEPT_TREE" class="modalCSSPage"><div><a href="#close" title="Close" class="closeModal">X</a>
  <h4>Department Hierarchy View</h4>
  <?php print $organization_tree; ?>
  </div></div></div><br>
<?php endif; ?>

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

<div class="section-label"><h3>Recent Publications</h3><a href="/islandora/search/mods_parent_organization_ms%3A(%22<?php print $object->label ?>%22)"><h4>View All Publications (search link)</h4></a></div>
<div class="view--recent-publications-by-dept">
  <?php print views_embed_view('recent_publications_by_dept', 'publications_by_dept', $object->label); ?>
</div>

<div class="section-label"><h3>Scholars</h3><a href="/islandora/search/MADS_parent_organization_ms%3A(%22<?php print $object->label ?>%22)"><h4>View All Scholars (search link)</h4></a></div>
<div class="other-scholars islandora-object-scholars">
  <!--<?php print views_embed_view('other_scholars_in_dept', 'dept_scholars', $object->label); ?>-->
  <?php print views_embed_view('browse_scholars_by_dept', 'block', $object->label); ?>
</div>

<br><br>
<?php if (isset($parent_organization)): ?>
  <div class="view--parent-department">
      <?php print views_embed_view('parent_department', 'parent_organization', $parent_organization); ?>
  </div>
<?php endif; ?>

<div class="view--browse-child-depts-by-dept">
  <?php print views_embed_view('browse_child_depts_by_dept', 'child_depts_by_dept', $object->label); ?>
</div>

<div class="view--child-departments">
  <!--<?php print views_embed_view('child_departments', 'child_organizations', $object->label); ?>-->
</div>

</div>
