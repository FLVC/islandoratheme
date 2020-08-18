jQuery(document).ready( function($) {
  lazyload();
  jQuery('#datatable-1').dataTable().fnSettings().aoDrawCallback.push( {
    "fn": function(oSettings) {
              lazyload();
          },
    "sName": "user"
  } );
});
