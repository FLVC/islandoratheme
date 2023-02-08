/**
 * Islandoratheme islandoratheme.js
 *
 * Use this file to add your custom javascript functions
 *
 * You can change the name of this file, just
 * remember to update the name in the info file
 * as well.
 */

function makeURL(the_form) {
  var collection = document.forms[the_form]["islandora_simple_collection"].value;
  collection = collection.replace(":", "\%3A");
  var search_query = document.forms[the_form]["islandora_simple_search_query"].value;
  var query = document.forms[the_form]["base_url"].value + "/islandora/search/" + search_query + "?type=edismax&collection=" + collection + "&search=" + search_query;
  window.location = query;
  return false;
}

function allItemsMakeURL(the_form, the_second_form) {
  var collection = document.forms[the_form]["islandora_simple_collection"].value;
  collection = collection.replace(":", "\%3A");
  var query = document.forms[the_second_form]["base_url"].value + "/islandora/search/?type=edismax";

  if (collection !== "") {
    query += "&collection=" + collection;
  }

  window.location = query;
  return false;
}

function tabLinkReload() {
 if (typeof _currentContentModel !== "undefined" && _currentContentModel && _currentContentModel=="islandora:compoundCModel")
 {
  jQuery("#tabs a:first").click(function() {
    if (window.location.href.indexOf("tabload=true") < 0) {
      window.location.replace(window.location.href + "?tabload=true");
    }
  });
 }
}

function collectionBlankSearch() {
  jQuery('#collection-specific-menu-item').click(function(e) {
    e.preventDefault();
    var query = location.protocol + '//' + location.hostname + '/islandora/search/?type=edismax';
    var collection = jQuery('#edit-islandora-simple-collection option:selected').val();
    collection = collection.replace(":", "\%3A");

    if(collection !== "") {
      query += '&collection=' + collection;
    }
    
    window.location = query;
    return false;
  });

}

function collectionAdvancedSearch() {
  jQuery('#advanced-search-menu-item').click(function(e) {
    e.preventDefault();
    var query = this.href;
    var collection = jQuery('#edit-islandora-simple-collection option:selected').val();
    collection = collection.replace(":", "\%3A");

    if(collection !== "") {
      query += '?collection=' + collection;
    }

    window.location = query;
    return false;
  });

}

(function ($) {
	/*
	 *adds placeholder text in the search box for the jQuery DataTables
	 */
	Drupal.behaviors.placeholder_text = {
		attach: function (context, settings) {

			var target = ".dataTables_filter input";
			var placeholder_text = "Filter Results:";

			$(target).attr("placeholder", placeholder_text).val("").focus().blur();
		}

        };
})(jQuery);


// For newspaper display
function islandora_newspaper_display_get_selected_year(){ 
  return jQuery('.selected a strong').text();
}

function islandora_newspaper_display_update_selected_year(){
  year = islandora_newspaper_display_get_selected_year();
  jQuery('h3#dynamic-year-display').text(year);
  jQuery("html, body").animate({ scrollTop: 0 }, "slow");
}

jQuery(document).ready(function($) {
  year = islandora_newspaper_display_get_selected_year();
  jQuery('div.vertical-tabs-panes').before('<h3 id="dynamic-year-display">' + year + '</h3>');
  jQuery('li.vertical-tab-button a').click(function() {
    islandora_newspaper_display_update_selected_year();
  });
});
