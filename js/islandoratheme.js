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
  var query = document.forms[the_form]["base_url"].value + "/islandora/search/" + document.forms[the_form]["islandora_simple_search_query"].value + "?type=dismax&collection=" + collection;
  window.location = query;
  return false;
}

function allItemsMakeURL(the_form, the_second_form) {
  var collection = document.forms[the_form]["islandora_simple_collection"].value;
  collection = collection.replace(":", "\%3A");
  var query = document.forms[the_second_form]["base_url"].value + "/islandora/search/?type=dismax";

  if (collection !== "") {
    query += "&collection=" + collection;
  }

  window.location = query;
  return false;
}  
