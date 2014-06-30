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
  var query = document.forms[the_form]["base_url"].value + "/islandora/search/" + document.forms[the_form]["islandora_simple_search_query"].value + "?type=edismax&collection=" + collection;
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

