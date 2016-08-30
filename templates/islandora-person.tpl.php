<?php
/**
 * @file
 * This is the template file for the object page for person objects.
 */
$identifier = $variables['u1'];
$affiliation = $variables['u2'];
?>
<div class="scholar islandora-object islandora">

	<div class="islandora-object-image scholar-image">
	  <?php if (isset($variables['tn'])): ?>
		<img src="<?php print $variables['tn']; ?>"/>
	  <?php endif; ?>
	</div>
	<div class="islandora-object-metadata metadata">
	  <?php if (isset($variables['metadata'])): ?>
		<?php print $variables['metadata']; ?>
	  <?php endif; ?>
	</div>

	<div class="bio">
		<h3 ><?php print t('Biography'); ?></h3>
		<?php if (isset($variables['biography'])): ?>
		  <p><?php print $variables['biography']; ?></p>
		<?php endif; ?>
	</div>
	<div class="view--citations">
		<h3>Recent Publications</h3>
	<?php print views_embed_view('recent_citations', 'citations', $identifier); ?>
	</div>
	<div class="activities islandora-object-activities">
	  <?php if (isset($variables['activities'])): ?>
		<?php print $variables['activities']; ?>
	  <?php endif; ?>
	</div>




<?php foreach ($affiliation as $dept): ?>
<div class="other-scholars islandora-object-scholars">
	  <h3>
		  Current Scholars in <?php print "<a href='/islandora/search/MADS_organization_ms%3A(\"$dept\")?sort=MADS_family_s asc'>$dept</a>"; ?>
	  </h3>

<?php print views_embed_view('other_scholars_in_dept', 'dept_scholars', $dept); ?>


</div>
	<?php endforeach; ?>

<div class="rss">
	<?php if (isset($variables['rss_feed'])): ?>
	  <?php print $variables['rss_feed']; ?>
	<?php endif; ?>
</div>
</div>
