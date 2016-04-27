<?php
/*
Plugin Name: Rest API Demo
Plugin URI: 
Description: Rest API Demo.
Version: 1.0.0
Author: Chiranjit Makur
License: GPLv2
Text Domain: rest-api-demo
*/

function rad_enqueue_script(){
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

add_action( 'wp_enqueue_scripts', 'rad_enqueue_script', 20 );

function rad_register_todos(){
	$labels = array(
		'name'               => _x( 'Todos', 'post type general name', 'rest-api-demo' ),
		'singular_name'      => _x( 'Todo', 'post type singular name', 'rest-api-demo' ),
		'menu_name'          => _x( 'Todos', 'admin menu', 'rest-api-demo' ),
		'name_admin_bar'     => _x( 'Todo', 'add new on admin bar', 'rest-api-demo' ),
		'add_new'            => _x( 'Add New', 'book', 'rest-api-demo' ),
		'add_new_item'       => __( 'Add New Todo', 'rest-api-demo' ),
		'new_item'           => __( 'New Todo', 'rest-api-demo' ),
		'edit_item'          => __( 'Edit Todo', 'rest-api-demo' ),
		'view_item'          => __( 'View Todo', 'rest-api-demo' ),
		'all_items'          => __( 'All Todos', 'rest-api-demo' ),
		'search_items'       => __( 'Search Todos', 'rest-api-demo' ),
		'parent_item_colon'  => __( 'Parent Todos:', 'rest-api-demo' ),
		'not_found'          => __( 'No Todos found.', 'rest-api-demo' ),
		'not_found_in_trash' => __( 'No Todos found in Trash.', 'rest-api-demo' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'with_front'         => false,
		'supports'           => array( 'title', 'editor' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'todo', $args );
}

add_action( 'init', 'rad_register_todos' );


function slug_get_is_done( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name, true );
}

function slug_update_is_done( $value, $object, $field_name ) {
	if ( ! $value || ! is_string( $value ) ) {
		return;
	}
	return update_post_meta( $object->ID, $field_name, strip_tags( $value ) );
}


add_action( 'rest_api_init', 'register_rest_api_route');

function register_rest_api_route() {
	register_rest_field( 'todo',
        'is_done',
        array(
            'get_callback'    => 'slug_get_is_done',
            'update_callback' => 'slug_update_is_done',
            'schema'          => null,
        )
    );

    register_rest_route( 'rest-api-demo/v2', '/posts/todos/add', array(
			'methods' => 'POST',
			'callback' => 'add_todos',
		)
	);

	register_rest_route( 'rest-api-demo/v2', '/posts/todos/delete/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => 'delete_todos',
		)
	);

	register_rest_route( 'rest-api-demo/v2', '/posts/todos/update', array(
			'methods' => 'PUT',
			'callback' => 'update_todos',
			'args' => $param,
		)
	);
}

function get_todos( $data ) {
	$args = array(
		'numberposts' => 10,
		'post_type'   => 'todo'
	);
	$todos = get_posts($args);
	return( $todos );
}

function add_todos( WP_REST_Request $request ){
	$post_ids = array();
	$params = $request->get_params();
	$todos = $params['todos'];
	foreach ($todos as $todo) {
		$post_id = wp_insert_post( array( 'post_title' => $todo['itemname'], 'post_type'=>'todo', 'post_status'=>'publish' ) );
		update_post_meta( $post_id, 'is_done', 'false' );
		$post_ids[] = $post_id;
	}
	return $post_ids;
}

function delete_todos( WP_REST_Request $request ) {
	$params = $request->get_params();
	return wp_delete_post( $params['id'] );
}

function update_todos( WP_REST_Request $request ){
	$params = $request->get_params();
	return update_post_meta( $params['todo_id'], 'is_done', $params['todo_is_done'] );
}