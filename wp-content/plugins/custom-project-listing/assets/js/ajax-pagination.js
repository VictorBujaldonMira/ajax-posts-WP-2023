jQuery(function ($) {

    function load_posts() {
        $.ajax({
            type: 'POST',
            url: pagination.ajax_url,
            data: {
                action: 'custom_project_pagination_posts',
                page: pagination.page,
                maxPage: pagination.max_page,
            },
            success: function (response) {
                $('#posts-container .row').html(response);
                if(pagination.page<=1){
                    jQuery('.pagination-container .prev').hide();
                }
            }
        });
    }

    // Cargar posts al cargar la página
    load_posts();

    // Cargar más posts al hacer clic en el botón "Siguiente"
    jQuery('.pagination-container .next').on('click', function () {
        pagination.page++;
        load_posts();

        if(pagination.page==jQuery("#posts-container .post__item").attr("max_pages")){
            jQuery('.pagination-container .next').hide();
        }

        jQuery('.pagination-container .prev').show();
    });

    // Cargar posts al hacer clic en el botón "Anterior"
    jQuery('.pagination-container .prev').on('click', function () {
        if (pagination.page > 1) {
            pagination.page--;
            load_posts();
        }

        if(pagination.page<jQuery("#posts-container .post__item").attr("max_pages")){
            jQuery('.pagination-container .next').show();
        }

        if(pagination.page<=1){
            jQuery('.pagination-container .prev').hide();
        }
    });

});