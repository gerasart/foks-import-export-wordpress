<?php

namespace Theme;

use function App\template;
use function App\render_template;

class RegisterPostType {


	public static function init() {
		add_action('init', array(__CLASS__, 'add_admin_menu_separator'));
//		add_action('init', array(__CLASS__, 'register_post_types'));
//		add_action('init', array(__CLASS__, 'register_taxonomies'));
	}


	/**
	 * Get in admin Array about page
	 */
	public static function adminArr() {
		echo "<pre>";
		var_dump(get_current_screen());
		echo "</pre>";
	}

	/**
	 * Register custom post type
	 */
	public static function register_post_types() {

		register_post_type('companies', array(
			'label'  => null,
			'labels' => array(
				'name'          => 'Компании',
				'singular_name' => __('Компания'),
				'menu_name'     => __('Компании'),
			),
			'public'            => true,
			'has_archive'       => true,
			'show_in_rest'      => true,
			'menu_position'     => 30,
			'menu_icon'         => 'dashicons-welcome-add-page',
			'supports'          => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
			'rewrite'           => array('with_front' => false, 'slug' => false),
		) );
	}

	/**
	 * Register custom taxonomies
	 */
	public static function register_taxonomies() {
		register_taxonomy( 'companies-list', 'companies', array(
			'labels'            => array(
				'name'          => __('Категориии'),
				'singular_name' => __('Банки'),
				'menu_name'     => __('Банки'),
			),
			'show_in_nav_menus' => true,
			'hierarchical'      => true,
			'rewrite'           => array('with_front'=> false, 'slug'=> false)
		));
		register_taxonomy(
			'tags', //your tags taxonomy
			'companies',  // Your post type
			array(
				'hierarchical'  => false,
				'label'         => __( 'Tags' ),
				'singular_name' => __( 'Tag' ),
				'rewrite'       => true,
				'query_var'     => true
			)
		);
	}

	/**
	 * Add menu separator
	 */
	public static function add_admin_menu_separator() {
		global $menu;
		$positions = array(26, 28); // After Comments

		foreach ($positions as $position) {
			$menu[$position] = array(
				0 => '',
				1 => 'read',
				2 => 'separator' . $position,
				3 => '',
				4 => 'wp-menu-separator'
			);
		}

		ksort($menu);

	}



}
