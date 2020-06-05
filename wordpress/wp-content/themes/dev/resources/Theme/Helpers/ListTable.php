<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 28.11.18
 * Time: 11:41
 */

namespace Theme\Helpers;


if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ListTable extends \WP_List_Table {

	private static $default = [
		'title' => '',
		'ajax' => true,
		'screen' => null,
		'columns' => [],
		'hidden' => [],
		'items' => [],
		'total' => false,
		'per_page' => 10,
        'default_key' => false,
        'bulk_actions' => false,
	];
	private static $params = [];

	public function __construct($atts = []) {
		self::setParams($atts);

		parent::__construct([
			'singular' => self::$params['title'], //singular name of the listed records
			'plural' => self::$params['title'], //plural name of the listed records
			'ajax' => self::$params['ajax'], //does this table support ajax?
			'screen' => self::$params['screen'],
		]);

		add_action( "current_screen", array(__CLASS__, 'addOptions') );
		add_filter( "set-screen-option", array(__CLASS__, 'setOptions'), 10, 3 );
	}

	public static function setParams($atts) {
		self::$params = shortcode_atts(self::$default, $atts);
	}

	public static function updateParams($atts) {
		self::$params = shortcode_atts(self::$params , $atts);
	}

	public static function addOptions() {
		$option = 'per_page';
		$args = array(
			'label' => 'Posts per page',
			'default' => get_option('posts_per_page'),
			'option' => 'per_page'
		);
		add_screen_option( $option, $args );
	}

	public static function setOptions($status, $option, $value) {
		return ( $option == 'per_page' ) ? (int) $value : $status;
	}

	public static function generateColumns($items) {
		$columns = [];

	    foreach ($items as $item) {
		    $columns[$item] = ucfirst( str_replace('_', ' ', $item) );
	    }

	    return $columns;
	}

	public function addActions($item, $column_name) {
		$id = (isset($item['ID'])) ? $item['ID'] : $item['id'];

		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&item=%s">Edit</a>',$_REQUEST['page'],'edit', $id),
			'delete'    => sprintf('<a href="?page=%s&action=%s&item=%s">Delete</a>',$_REQUEST['page'],'delete', $id),
		);

		return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions) );
	}

	function column_default( $item, $column_name ) {
		$columns = array_keys(self::$params['columns']);
		$default_key = self::$params['default_key'];
        $default_column = $default_key ? $columns[$default_key] : false;

		if ( count($columns) > 2 && $column_name == $default_column ) {
			return $this->addActions($item, $column_name);
		}

		return $item[ $column_name ];
	}

	public function get_columns() {
        $default = [];

	    if ( self::$params['bulk_actions'] ) {
            $default = [
                'cb' => '<input type="checkbox" />',
            ];
        }

//	    $new = self::generateColumns(array_keys(self::$params['columns']));

	    return array_merge($default, self::$params['columns']);
//	    return array_merge($default, $new);
	}

	public function get_sortable_columns() {
	    $sortable_columns = [];

	    foreach ( self::$params['columns'] as $key => $column ) {
		    $sortable_columns[$key] = [$column, true];
	    }

	    return $sortable_columns;
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = self::$params['hidden'];
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->items = self::$params['items'];

//		usort( $data, array( $this, 'usort_reorder' ) );
//		$this->items = $data;

		if ( self::$params['total'] ) {
			$this->set_pagination_args( array(
				'total_items' => self::$params['total'],                     // WE have to calculate the total number of items.
				'per_page'    => self::$params['per_page'],                        // WE have to determine how many items to show on a page.
				'total_pages' => ceil( self::$params['total'] / self::$params['per_page'] ), // WE have to calculate the total number of pages.
			) );
		}
	}

	public function get_bulk_actions() {
	    $actions = [];

        if ( self::$params['bulk_actions'] ) {
            $actions = array(
                'delete' => 'Delete'
            );
        }

		return $actions;
	}

	public function column_cb($item) {
		$id = (isset($item['ID'])) ? $item['ID'] : $item['id'];
		return sprintf(
			'<input type="checkbox" name="item[]" value="%s" />', $id
		);
	}

	/**
	 * Callback to allow sorting of example data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : self::$params['columns'][0]; // WPCS: Input var ok.
		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.
		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}
