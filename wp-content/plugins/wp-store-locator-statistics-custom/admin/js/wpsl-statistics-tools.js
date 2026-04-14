jQuery( document ).ready( function( $ ) {
    $( "#wpsl-stats-tools-form" ).submit( function ( e ) {
        if ( $( "#wpsl-bulk-reset" ).is( ":checked" ) ) {
            e.preventDefault();

            $( "#dialog" ).dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                        $( "#wpsl-stats-tools-form" ).unbind( "submit" ).submit();
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        }
    });
});