// Make embargoed thumbnails obvious.
jQuery(document).ready(function($) {
  $('.flvc_content_model_organization_tn').wrap('<div style="position:relative;"></div>');
  $('.flvc_content_model_organization_tn').after('<span id="embargo_overlay">Organization Entity</span>');
  $('.flvc_content_model_person_tn').wrap('<div style="position:relative;"></div>');
  $('.flvc_content_model_person_tn').after('<span id="embargo_overlay">Person Entity</span>');
});
