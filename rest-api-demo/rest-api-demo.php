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
	wp_enqueue_script( 'application-script', plugin_dir_url( __FILE__ )  . 'js/app.js', array( 'angular' ) );
}

add_action( 'wp_enqueue_scripts', 'rad_enqueue_script' );

function rad_register_todos() {
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
		'with_front' => false,
		'supports'           => array( 'title', 'editor' ),
	);

	register_post_type( 'todo', $args );
}
add_action( 'init', 'rad_register_todos' );


add_action( 'rest_api_init', 'register_rest_api_route');

function register_rest_api_route() {

	register_rest_route( 'rest-api-demo/v1', '/posts/todos/', array(
				'methods' => 'GET',
				'callback' => 'get_todos',
				'args' => array(
					'id' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
				),
			),
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