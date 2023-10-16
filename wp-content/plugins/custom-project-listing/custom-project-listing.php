<?php
/*
Plugin Name: Custom Project Listing
Description: Plugin para mostrar un listado personalizado de entradas tipo project.
Version: 1.0
Author: Víctor Buajaldón
*/

// Creación del custom post-type: project
add_action( 'init', 'custom_project_create_type' );

function custom_project_create_type() {

    $args=array(
        'label' => 'Projects',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'project',
            ),
        'query_var' => true,
    );

    register_post_type( 'project', $args );

}

// Acción de activación del plugin
function plugin_on_install() {
    custom_project_create_type();
    custom_project_listing_activate();
}

register_activation_hook( __FILE__, 'plugin_on_install' );

function custom_project_listing_unactivate(){

    $allposts = get_posts( array('post_type'=>'project','numberposts'=>-1) );

    foreach ($allposts as $eachpost) {
        wp_delete_post( $eachpost->ID, true );
    }
}

// Acción de desactivación del plugin
function plugin_on_uninstall() {
    custom_project_listing_unactivate();
    unregister_post_type( 'project' );
}

register_deactivation_hook( __FILE__, 'plugin_on_uninstall' );

// Función para generar las 30 entradas al activar el plugin
function custom_project_listing_activate() {

    for ($i = 1; $i <= 30; $i++) {
        $post_args = array(
            'post_title'   => 'Proyecto ' . $i,
            'post_content' => 'Descripción del proyecto ' . $i . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'post_status'  => 'publish',
            'post_type'    => 'project',
        );

        wp_insert_post($post_args);
    }
}

// Función para incluir scripts y estilos
function custom_project_listing_enqueue_scripts() {
    wp_enqueue_style('bootstrap-style', plugin_dir_url( __FILE__ ) . '/assets/css/bootstrap.min.css', array(), false);
    wp_enqueue_style('bootstrap-style', plugin_dir_url( __FILE__ ) . '/assets/css/bootstrap-utilities.min.css', array(), false);

    wp_enqueue_script( 'bootstrap-script', plugin_dir_url( __FILE__ ) . '/assets/js/bootstrap.min.js', array( 'jquery' ), '1.0', true);
    wp_enqueue_script( 'ajax-search', plugin_dir_url( __FILE__ ) . '/assets/js/ajax-search.js', array( 'jquery' ), '1.0', true);

    wp_localize_script( 'ajax-search', 'search',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );

    wp_enqueue_script( 'ajax-pagination', plugin_dir_url( __FILE__ ) . '/assets/js/ajax-pagination.js', array( 'jquery' ), '1.0', true);

    wp_localize_script( 'ajax-pagination', 'pagination',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'page' => 1,
        )
    );
}

add_action('wp_enqueue_scripts', 'custom_project_listing_enqueue_scripts');

// Búsqueda del post y crear estructura de estos
function custom_project_search_results() {
    $html = "";

    $search_query = sanitize_text_field($_POST['search']);

    $args = array(
        'post_type' => 'project',
        's' => $search_query,
    );

    $query = new WP_Query( $args );

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $html .= "<div class='post__item col-lg-4 col-md-6 col-sm-12 my-5'>
                <h2>".get_the_title()."</h2>
                <p>".get_the_content()."</p>
            </div>";
        endwhile;
        wp_reset_postdata();
    echo $html;
    else :
        echo 'No hay resultados encontrados';
    endif;

	wp_die();
}

add_action( 'wp_ajax_custom_project_search_results', 'custom_project_search_results' );
add_action( 'wp_ajax_nopriv_custom_project_search_results', 'custom_project_search_results' );


// Llamar a los posts y crear estructura de estos
function custom_project_pagination_posts() {
    $page = $_POST['page'];

    $html = "";

    $args = array(
        'post_type' => 'project',
        'post_status' => 'publish',
        'paged' => $page,
        'posts_per_page' => 6,
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $html .= "<div class='post__item col-lg-4 col-md-6 col-sm-12 my-5' max_pages='".$query->max_num_pages."'>
                <h2>".get_the_title()."</h2>
                <p>".get_the_content()."</p>
            </div>";
        endwhile;
        wp_reset_postdata();
    echo $html;
    endif;

    wp_die();
}

add_action('wp_ajax_custom_project_pagination_posts', 'custom_project_pagination_posts');
add_action('wp_ajax_nopriv_custom_project_pagination_posts', 'custom_project_pagination_posts');

// Crear contenedores de los posts
function custom_project_print_content(){

    $seachContainer = '
    <div class="search-container container my-5">
        <div class="search row">
            <div class="input-group mb-3">
                <input class="input-search form-control" type="text" name="" placeholder="Buscar ...">
                <button class="btn btn-outline-secondary button-search" type="button">Buscar</button>
            </div>
        </div>
    </div>';

    $paginationContainer =
    '<div class="pagination-container container">
        <div class="pagination row">
            <div class="col">
                <span role="button" class="prev btn btn-outline-primary">Anterior</span>
            </div>
            <div class="col text-end">
                <span role="button" class="next btn btn-outline-primary">Siguiente</span>
            </div>
        </div>
    </div>';

    $html = $seachContainer . $paginationContainer . '<div id="posts-container" class="container my-5">
        <div class="row my-5">
        </div>
    </div>' . $paginationContainer;

    return $html;
}

add_shortcode('custom_project_listing', 'custom_project_print_content');