<?php

/**
 * Plugin Name:       Houzez
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       houzez
 *
 * @package CreateBlock
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Main Plugin Class
 */
class HouzezPlugin {
    
    /**
     * Plugin instance
     *
     * @var HouzezPlugin
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     *
     * @return HouzezPlugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize plugin functionality
     */
    public function init() {
        $this->register_blocks();
        $this->register_custom_post_types();
        $this->register_taxonomies();
    }
    
    /**
     * Register blocks
     */
    public function register_blocks() {
        /**
         * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
         * based on the registered block metadata.
         * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
         *
         * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
         */
        if (function_exists('wp_register_block_types_from_metadata_collection')) {
            wp_register_block_types_from_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
            return;
        }

        /**
         * Registers the block(s) metadata from the `blocks-manifest.php` file.
         * Added to WordPress 6.7 to improve the performance of block type registration.
         *
         * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
         */
        if (function_exists('wp_register_block_metadata_collection')) {
            wp_register_block_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
        }
        
        /**
         * Registers the block type(s) in the `blocks-manifest.php` file.
         *
         * @see https://developer.wordpress.org/reference/functions/register_block_type/
         */
        $manifest_data = require __DIR__ . '/build/blocks-manifest.php';
        foreach (array_keys($manifest_data) as $block_type) {
            register_block_type(__DIR__ . "/build/{$block_type}");
        }
    }
    
    /**
     * Register custom post types
     */
    public function register_custom_post_types() {
        $cpt_manager = new HouzezCPTManager();
        $cpt_manager->register_property_cpt();
    }
    
    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        $taxonomy_manager = new HouzezTaxonomyManager();
        $taxonomy_manager->register_property_taxonomy();
    }
}

/**
 * Custom Post Type Manager Class
 */
class HouzezCPTManager {
    
    /**
     * Register Property Custom Post Type
     */
    public function register_property_cpt() {
        $labels = $this->get_property_labels();
        $args = $this->get_property_args($labels);
        
        // wordpress function (property-slug, $args-all arguments)
        register_post_type('property', $args);

    }
    
    /**
     * Get property CPT labels
     *
     * @return array
     */
    private function get_property_labels() {
        return array(
            'name'                  => __('Properties', 'houzez'),
            'singular_name'         => __('Property', 'houzez'),
            'menu_name'             => __('Properties', 'houzez'),
            'name_admin_bar'        => __('Property', 'houzez'),
            'add_new'               => __('Add New Property', 'houzez'),
            'add_new_item'          => __('Add New Property', 'houzez'),
            'new_item'              => __('New Property', 'houzez'),
            'edit_item'             => __('Edit Property', 'houzez'),
            'view_item'             => __('View Property', 'houzez'),
            'all_items'             => __('All Properties', 'houzez'),
            'search_items'          => __('Search Properties', 'houzez'),
            'not_found'             => __('No Properties found.', 'houzez'),
            'not_found_in_trash'    => __('No Properties found in Trash.', 'houzez')
        );
    }
    
    /**
     * Get property CPT arguments
     *
     * @param array $labels
     * @return array
     */
    private function get_property_args($labels) {
        return array(
            'label'         => __('Property', 'houzez'),
            'labels'        => $labels,
            'public'        => true,
            'show_ui'       => true,
            'show_in_menu'  => true,
            'menu_position' => 5,
            'menu_icon'     => 'dashicons-admin-home',
            'show_in_rest'  => true,
            'has_archive'   => true,
            'rewrite'       => array('slug' => 'properties'),
            'supports'      => $this->get_property_supports(),
        );
    }
    
    /**
     * Get property CPT supported features
     *
     * @return array
     */
    private function get_property_supports() {
        return array(
            'title',          // Property title
            'editor',         // Full description/details
            'excerpt',        // Short description
            'thumbnail',      // Featured image
            'revisions',      // Track changes
            'author',         // Assign agent/user
            'comments',       // Enable reviews/inquiries
            'custom-fields',  // Store extra data
            'page-attributes' // Manual ordering
        );
    }
}

/**
 * Taxonomy Manager Class
 */
class HouzezTaxonomyManager {
    
    /**
     * Register Property Taxonomy
     */
    public function register_property_taxonomy() {
        $labels = $this->get_taxonomy_labels();
        $args = $this->get_taxonomy_args($labels);
        
        register_taxonomy('property_category', array('property'), $args);
    }
    
    /**
     * Get taxonomy labels
     *
     * @return array
     */
    private function get_taxonomy_labels() {
        return array(
            'name'              => _x('Property Categories', 'taxonomy general name', 'houzez'),
            'singular_name'     => _x('Property Category', 'taxonomy singular name', 'houzez'),
            'search_items'      => __('Search Property Categories', 'houzez'),
            'all_items'         => __('All Property Categories', 'houzez'),
            'parent_item'       => __('Parent Property Category', 'houzez'),
            'parent_item_colon' => __('Parent Property Category:', 'houzez'),
            'edit_item'         => __('Edit Property Category', 'houzez'),
            'update_item'       => __('Update Property Category', 'houzez'),
            'add_new_item'      => __('Add New Property Category', 'houzez'),
            'new_item_name'     => __('New Property Category Name', 'houzez'),
            'menu_name'         => __('Property Categories', 'houzez'),
        );
    }
    
    /**
     * Get taxonomy arguments
     *
     * @param array $labels
     * @return array
     */
    private function get_taxonomy_args($labels) {
        return array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'property-category'),
        );
    }
}

/**
 * Initialize the plugin
 */
function houzez_plugin_init() {
    return HouzezPlugin::get_instance();
}

// Start the plugin
houzez_plugin_init();