jQuery(function ($) {
    $(".button-search").on('click',function(e) {
        e.preventDefault();
        e.stopPropagation();

        var searcher = jQuery('.input-search').val();

        jQuery.ajax({
            type : 'POST',
            url : search.ajax_url,
            data : {
                action : 'custom_project_search_results',
                search : searcher
            },
            success : function( response ) {
                console.log(response);
                jQuery('#posts-container .row').html( response );
            }
        });

        return false;
    })
});