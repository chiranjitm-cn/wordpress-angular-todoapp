<?php
/*
Plugin Name: WP Rest API Demo
Plugin URI: https://crowdfavorite.com/
Version: 1.0.0
Description: Rest API Demo Test.
Author: Crowd Favorite
Author URI: https://crowdfavorite.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: cfwprapi
*/

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
function cfwprapi_enqueue_script() {

    wp_enqueue_script( 'angular', plugin_dir_url( __FILE__ ) . 'lib/angular/angular.js', array( 'jquery' ));
    wp_enqueue_script( 'angular-route', plugin_dir_url( __FILE__ )  . 'lib/angular-route/angular-route.js', array( 'angular' ) );
    wp_enqueue_script( 'angular-resource', plugin_dir_url( __FILE__ )  . 'lib/angular-resource/angular-resource.js', array( 'angular' ) );
    wp_enqueue_script( 'bootstrap-script', plugin_dir_url( __FILE__ )  . 'lib/bootstrap/bootstrap.min.js', array( 'angular' ) );
    wp_enqueue_script( 'application-script', plugin_dir_url( __FILE__ )  . 'js/app.js', array( 'angular' ) );

    $wnm_custom = array( 'site_url' => site_url() );
    wp_localize_script( 'application-script', 'wnm_custom', $wnm_custom );

    wp_register_style( 'bootstrap-style', 'http://netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' );
    wp_enqueue_style( 'bootstrap-style' );
}
add_action( 'wp_enqueue_scripts', 'cfwprapi_enqueue_script', 20 );

/**
 * Register Custom Post Type 'Todos'
 *
 * @since 1.0.0
 */
function cfwprapi_register_cpt_todos() {

    register_post_type( 'todo', array(
        'labels'              => array(
            'name'                => _x( 'Todos', 'Post Type General Name', 'cfwprapi' ),
            'singular_name'       => _x( 'Todo', 'Post Type Singular Name', 'cfwprapi' ),
            'menu_name'           => __( 'Todos', 'cfwprapi' ),
            'name_admin_bar'      => __( 'Todo', 'cfwprapi' ),
            'parent_item_colon'   => __( 'Parent Todos:', 'cfwprapi' ),
            'all_items'           => __( 'All Todos', 'cfwprapi' ),
            'add_new_item'        => __( 'Add New Todo', 'cfwprapi' ),
            'add_new'             => __( 'Add New', 'cfwprapi' ),
            'new_item'            => __( 'New Todo', 'cfwprapi' ),
            'edit_item'           => __( 'Edit Todo', 'cfwprapi' ),
            'update_item'         => __( 'Update Todo', 'cfwprapi' ),
            'view_item'           => __( 'View Todo', 'cfwprapi' ),
            'search_items'        => __( 'Search Todos', 'cfwprapi' ),
            'not_found'           => __( 'Not found', 'cfwprapi' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'cfwprapi' ),
        ),
        'supports'            => array(
            'title',
            'editor',
        ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-media-text',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'has_archive' => 'todo',
        'rewrite' => array(
            'with_front' => false,
            'slug' => 'Todos',
        ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'cfwprapi_register_cpt_todos', 0 );

/**
 * Custom endpoint for todo post-type.
 *
 * This registeres a route to accept POST/GET requests from authenticated users.
 *
 * The permission_callback lets us restrict access to this callback
 * based on any arbitrary rules we choose to define.
 *
 * @since 1.0.0
 */
function cfwprapi_register_custom_route() {

    register_rest_field( 'todo',
        'is_done',
        array(
            'get_callback'    => 'cfwprapi_get_is_done',
            'update_callback' => 'cfwprapi_update_is_done',
            'schema'          => null,
        )
    );

    register_rest_route( 'wp/v2', '/posts/todos/add', array(
            'methods' => 'POST',
            'callback' => 'cfwprapi_add_todos',
        )
    );

    register_rest_route( 'wp/v2', '/posts/todos/delete/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => 'cfwprapi_delete_todos',
        )
    );

    register_rest_route( 'wp/v2', '/posts/todos/update', array(
            'methods' => 'PUT',
            'callback' => 'cfwprapi_update_todos',
            'args' => $param,
        )
    );
}
add_action( 'rest_api_init', 'cfwprapi_register_custom_route');

/**
 * Get the value of the added field( i.e is_done)
 *
 * @since 1.0.0
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function cfwprapi_get_is_done( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name, true );
}

/**
 * Updating custom field value( i.e is_done )
 *
 * @since 1.0.0
 *
 * @param mixed $value The value of the field
 * @param object $object The object from the response
 * @param string $field_name Name of field
 *
 * @return bool|int
 */
function cfwprapi_update_is_done( $value, $object, $field_name ) {
    if ( ! $value || ! is_string( $value ) ) {
        return;
    }
    return update_post_meta( $object->ID, $field_name, strip_tags( $value ) );
}

/**
 * Callback to get all todo items.
 *
 * @since  1.0.0
 * @return WP_REST_Response
 */
function cfwprapi_get_todos( $data ) {

    $posts = get_posts( array(
        'post_type' => 'todo',
        'numberposts' => 10
    ) );

    // If array is empty, throw an error
    if ( empty( $posts ) ) {
        return new WP_REST_Response(
            array(
                'code'    => 'no_data_found',
                'message' => __( 'Todo item is missing.', 'cfwprapi' ),
            ),
            400
        );
    }

    // Return post data when succeeded!
    return new WP_REST_Response(
        array(
            'code'    => 'success',
            'message' => sprintf( __( 'Successfully fetch all todo items.', 'cfwprapi' ) ),
            'data'    => array(
                'response' => $posts,
            ),
        ),
        200
    );
}

/**
 * Callback to insert todo data.
 *
 * @since  1.0.0
 *
 * @param  object $request REST request object.
 * @return WP_REST_Response
 */
function cfwprapi_add_todos( WP_REST_Request $request ){
    $post_ids = array();
    $params = $request->get_params();
    $todos = $params['todos']; print_r($todos);

    // If array is empty, throw an error
    if ( empty( $todos ) || ! is_array( $todos ) ) {
        return new WP_REST_Response(
            array(
                'code'    => 'import_data_error',
                'message' => __( 'Todo item is missing.', 'cfwprapi' ),
                'data'    => array(
                    'request' => $params,
                ),
            ),
            400
        );
    }

    // Insert data
    foreach ($todos as $todo) {
        $due_date = ! empty( $todo['duedate'] ) ? $todo['duedate'] : '';
        $post_id = wp_insert_post( array( 'post_title' => $todo['itemname'], 'post_type'=>'todo', 'post_status'=>'publish' ) );
        update_post_meta( $post_id, 'is_done', 'false' );
        update_post_meta( $post_id, 'due_date', $due_date );
        $post_ids[] = $post_id;
    }

     // Return post-id when succeeded!
    return new WP_REST_Response(
        array(
            'code'    => 'success',
            'message' => sprintf( __( 'Successfully inserted, id is %1$d .', 'cfwprapi' ), $post_ids ),
            'data'    => array(
                'response' => $post_ids,
            ),
        ),
        200
    );
}

/**
 * Callback to delete todo data.
 *
 * @since  1.0.0
 *
 * @param  object $request REST request object.
 * @return WP_REST_Response
 */
function cfwprapi_delete_todos( WP_REST_Request $request ) {
    $params = $request->get_params();
    return wp_delete_post( $params['id'] );
}

/**
 * Callback to update todo data.
 *
 * @since  1.0.0
 *
 * @param  object $request REST request object.
 * @return WP_REST_Response
 */
function cfwprapi_update_todos( WP_REST_Request $request ){
    $params = $request->get_params();
    return update_post_meta( $params['todo_id'], 'is_done', $params['todo_is_done'] );
}
