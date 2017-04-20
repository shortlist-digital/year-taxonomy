
<?php
/**
* @wordpress-plugin
* Plugin Name: Year Taxonomy
* Plugin URI: http://github.com/shortlist-digital/year-taxonomy
* Description: Add a year tag to a post
* Version: 1.0.0
* Author: Shortlist Studio
* Author URI: http://shortlist.studio
* License: MIT
*/
class YearTaxonomy
{
	public function __construct()
	{
		add_action('init', array($this, 'register_custom_taxonomy'));
		add_filter('timber_context', array($this, 'add_year_to_context'), 10, 3);
		add_filter('admin_menu', array($this, 'remove_year_box'), 10, 1);
		add_filter('init', array($this, 'add_nice_year_selector'), 10, 2);
		add_action('wp_head', array($this, 'create_year_reference'));
	}
	private function get_year() {
		global $post;
		if (!empty($post)) {
	   		return get_the_terms($post->ID, 'year')[0];
		} else {
			return null;
		}
	}
	public function remove_year_box()
	{
		remove_meta_box('tagsdiv-year', 'product', 'normal');
	}
	public function add_nice_year_selector()
	{
		acf_add_local_field_group(array (
			'key' => 'group_year',
			'title' => 'Year',
			'fields' => array (
				array (
					'key' => 'product_year',
					'label' => 'Year',
					'name' => 'year',
					'type' => 'taxonomy',
					'instructions' => 'Select a year for this content',
					'required' => 1,
					'taxonomy' => 'year',
					'field_type' => 'select',
					'allow_null' => 0,
					'add_term' => 0,
					'save_terms' => 1,
					'load_terms' => 1,
					'return_format' => 'object'
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'product'
					),
				),
			),
			'hide_on_screen' => array (
				0 => 'the_content',
			),
			'description' => 'Select a year for this content'
		));
	}

	public function apply_acf_to_year($acf_fields)
	{
		array_push($acf_fields['year'], array(
			array(
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => 'year',
			),
		));
		return $acf_fields;
	}
	public function add_year_to_context($context)
	{
		global $post;
		if ($post) {
			$context['years'] = $this->get_year();
		}
		return $context;
	}

	// Register Custom Taxonomy
	public function register_custom_taxonomy()
	{
		$labels = array(
			'name'                       => _x( 'Year', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Year', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Year', 'text_domain' ),
			'all_items'                  => __( 'All years', 'text_domain' ),
			'parent_item'                => __( 'Parent year', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent year:', 'text_domain' ),
			'new_item_name'              => __( 'New year', 'text_domain' ),
			'add_new_item'               => __( 'Add year', 'text_domain' ),
			'edit_item'                  => __( 'Edit year', 'text_domain' ),
			'update_item'                => __( 'Update year', 'text_domain' ),
			'view_item'                  => __( 'View year', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate years with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove years', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular years', 'text_domain' ),
			'search_items'               => __( 'Search years', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No years', 'text_domain' ),
			'items_list'                 => __( 'Years list', 'text_domain' ),
			'items_list_navigation'      => __( 'Years list navigation', 'text_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'rewrite'                      => array(
				// 'slug' => '/',
				'with_front' => false
			),
			'show_in_rest'       => true,
			'rest_base'          => 'years',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		);
		register_taxonomy('year', array( 'product' ), $args);
	}
	public function create_year_reference() {
		$year_object = json_encode($this->get_year());
		echo "<script>window.agreableYear = " . $year_object . "</script>";
	}
}
new YearTaxonomy();
