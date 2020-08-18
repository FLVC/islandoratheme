jQuery(document).ready( function($) {

  lazyload();
  $('#datatable-1 img').error(function () {
      $(this).addClass('missing-scholar');
  });

  jQuery('#datatable-1').dataTable().fnSettings().aoDrawCallback.push( {
    "fn": function(oSettings) {
              lazyload();
              $('#datatable-1 img').error(function () {
                 $(this).addClass('missing-scholar');
              });

          },
    "sName": "user"
  } );

});
