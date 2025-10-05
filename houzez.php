<?php
/**
 * Plugin Name:       Houzez
 * Description:       Advanced property management system with Gutenberg blocks.
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

// Prevent direct class access
if (!class_exists('HouzezCPTManager')) {

// Define image size constants
class HouzezConstants {
    const IMAGE_SIZE_THUMBNAIL = 'thumbnail';
    const IMAGE_SIZE_MEDIUM = 'medium';
    const IMAGE_SIZE_MEDIUM_LARGE = 'medium_large';
    const IMAGE_SIZE_LARGE = 'large';
    const IMAGE_SIZE_FULL = 'full';
    
    const CACHE_EXPIRATION = 15 * MINUTE_IN_SECONDS;
    const MAX_POSTS_TO_SHOW = 50;
    const MAX_COLUMNS = 6;
    const MAX_EXCERPT_LENGTH = 100;
    
    // Block attributes defaults
    const DEFAULT_POSTS_TO_SHOW = 6;
    const DEFAULT_COLUMNS = 3;
    const DEFAULT_EXCERPT_LENGTH = 20;
    const DEFAULT_ORDER = 'DESC';
    const DEFAULT_ORDER_BY = 'date';
    const DEFAULT_LAYOUT = 'grid';
    const DEFAULT_IMAGE_SIZE = 'medium_large';
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
        
        try {
            register_post_type('property', $args);
        } catch (Exception $e) {
            error_log('Houzez: Failed to register property CPT - ' . $e->getMessage());
        }
    }
    
    /**
     * Get property CPT labels
     */
    private function get_property_labels() {
        return array(
            'name'                  => _x('Properties', 'post type general name', 'houzez'),
            'singular_name'         => _x('Property', 'post type singular name', 'houzez'),
            'menu_name'             => __('Properties', 'houzez'),
            'name_admin_bar'        => __('Property', 'houzez'),
            'add_new'               => _x('Add New', 'property', 'houzez'),
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
            'taxonomies'    => array('property_category', 'property_status', 'property_type'),
            'capability_type' => 'post',
            'map_meta_cap'  => true,
        );
    }
    
    /**
     * Get property CPT supported features
     */
    private function get_property_supports() {
        return array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'revisions',
            'author',
            'comments',
            'custom-fields',
            'page-attributes'
        );
    }
}

/**
 * Taxonomy Manager Class
 */
class HouzezTaxonomyManager {
    
    /**
     * Register Property Category Taxonomy
     */
    public function register_property_taxonomy() {
        $labels = $this->get_category_labels();
        $args = $this->get_taxonomy_args($labels);
        register_taxonomy('property_category', array('property'), $args);
    }
    
    /**
     * Register Property Status Taxonomy
     */
    public function register_property_status() {
        $labels = $this->get_status_labels();
        $args = $this->get_taxonomy_args($labels);
        register_taxonomy('property_status', array('property'), $args);
        $this->register_default_status_terms();
    }
    
    /**
     * Register Property Type Taxonomy
     */
    public function register_property_type() {
        $labels = $this->get_type_labels();
        $args = $this->get_taxonomy_args($labels);
        register_taxonomy('property_type', array('property'), $args);
        $this->register_default_type_terms();
    }
    
    /**
     * Get category taxonomy labels
     */
    private function get_category_labels() {
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
     * Get status taxonomy labels
     */
    private function get_status_labels() {
        return array(
            'name'              => _x('Property Status', 'taxonomy general name', 'houzez'),
            'singular_name'     => _x('Property Status', 'taxonomy singular name', 'houzez'),
            'search_items'      => __('Search Property Status', 'houzez'),
            'all_items'         => __('All Property Status', 'houzez'),
            'parent_item'       => __('Parent Property Status', 'houzez'),
            'parent_item_colon' => __('Parent Property Status:', 'houzez'),
            'edit_item'         => __('Edit Property Status', 'houzez'),
            'update_item'       => __('Update Property Status', 'houzez'),
            'add_new_item'      => __('Add New Property Status', 'houzez'),
            'new_item_name'     => __('New Property Status Name', 'houzez'),
            'menu_name'         => __('Property Status', 'houzez'),
        );
    }
    
    /**
     * Get type taxonomy labels
     */
    private function get_type_labels() {
        return array(
            'name'              => _x('Property Types', 'taxonomy general name', 'houzez'),
            'singular_name'     => _x('Property Type', 'taxonomy singular name', 'houzez'),
            'search_items'      => __('Search Property Types', 'houzez'),
            'all_items'         => __('All Property Types', 'houzez'),
            'parent_item'       => __('Parent Property Type', 'houzez'),
            'parent_item_colon' => __('Parent Property Type:', 'houzez'),
            'edit_item'         => __('Edit Property Type', 'houzez'),
            'update_item'       => __('Update Property Type', 'houzez'),
            'add_new_item'      => __('Add New Property Type', 'houzez'),
            'new_item_name'     => __('New Property Type Name', 'houzez'),
            'menu_name'         => __('Property Types', 'houzez'),
        );
    }
    
    /**
     * Get taxonomy arguments
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
            'capabilities'      => array(
                'manage_terms'  => 'manage_categories',
                'edit_terms'    => 'manage_categories',
                'delete_terms'  => 'manage_categories',
                'assign_terms'  => 'edit_posts'
            )
        );
    }
    
    /**
     * Register default status terms
     */
    private function register_default_status_terms() {
        $default_terms = array(
            'for-sale'   => __('For Sale', 'houzez'),
            'for-rent'   => __('For Rent', 'houzez'),
            'sold'       => __('Sold', 'houzez'),
            'rented'     => __('Rented', 'houzez')
        );
        
        foreach ($default_terms as $slug => $name) {
            if (!term_exists($slug, 'property_status')) {
                wp_insert_term($name, 'property_status', array('slug' => $slug));
            }
        }
    }
    
    /**
     * Register default type terms
     */
    private function register_default_type_terms() {
        $default_terms = array(
            'house'      => __('House', 'houzez'),
            'apartment'  => __('Apartment', 'houzez'),
            'condo'      => __('Condo', 'houzez'),
            'villa'      => __('Villa', 'houzez'),
            'commercial' => __('Commercial', 'houzez'),
            'land'       => __('Land', 'houzez')
        );
        
        foreach ($default_terms as $slug => $name) {
            if (!term_exists($slug, 'property_type')) {
                wp_insert_term($name, 'property_type', array('slug' => $slug));
            }
        }
    }
}

/**
 * Meta Fields Manager Class
 */
class HouzezMetaManager {
    
    /**
     * Register property meta fields
     */
    public function register_property_meta_fields() {
        $meta_fields = array(
            'fave_property_price' => array(
                'type' => 'number',
                'description' => 'Property price in dollars',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => array($this, 'sanitize_price')
            ),
            'fave_property_location' => array(
                'type' => 'string',
                'description' => 'Property location',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'fave_property_size' => array(
                'type' => 'number',
                'description' => 'Property size in square feet',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => array($this, 'sanitize_size')
            ),
            'fave_property_bedrooms' => array(
                'type' => 'integer',
                'description' => 'Number of bedrooms',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => array($this, 'sanitize_bedrooms')
            ),
            'fave_property_bathrooms' => array(
                'type' => 'number',
                'description' => 'Number of bathrooms',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => array($this, 'sanitize_bathrooms')
            ),
            'fave_property_garage' => array(
                'type' => 'integer',
                'description' => 'Number of garage spaces',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => array($this, 'sanitize_garage')
            ),
            'fave_property_year' => array(
                'type' => 'integer',
                'description' => 'Year built',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => array($this, 'sanitize_year')
            ),
            'fave_agents' => array(
                'type' => 'string',
                'description' => 'Property agent',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'fave_featured' => array(
                'type' => 'boolean',
                'description' => 'Is property featured',
                'single' => true,
                'show_in_rest' => true,
                'default' => false
            )
        );
        
        foreach ($meta_fields as $key => $args) {
            register_post_meta('property', $key, $args);
        }
    }
    
    /**
     * Sanitize price field
     */
    public function sanitize_price($value) {
        $price = floatval($value);
        return $price >= 0 ? $price : 0;
    }
    
    /**
     * Sanitize size field
     */
    public function sanitize_size($value) {
        $size = floatval($value);
        return $size >= 0 ? $size : 0;
    }
    
    /**
     * Sanitize bedrooms field
     */
    public function sanitize_bedrooms($value) {
        $bedrooms = intval($value);
        return max(0, min(20, $bedrooms));
    }
    
    /**
     * Sanitize bathrooms field
     */
    public function sanitize_bathrooms($value) {
        $bathrooms = floatval($value);
        return max(0, min(10, $bathrooms));
    }
    
    /**
     * Sanitize garage field
     */
    public function sanitize_garage($value) {
        $garage = intval($value);
        return max(0, min(10, $garage));
    }
    
    /**
     * Sanitize year field
     */
    public function sanitize_year($value) {
        $year = intval($value);
        $current_year = (int) date('Y');
        return max(1800, min($current_year + 5, $year));
    }
}

/**
 * Main Plugin Class
 */
class HouzezPlugin {
    
    private static $instance = null;
    const VERSION = '0.1.0';
    const MIN_WP_VERSION = '6.7';
    const MIN_PHP_VERSION = '7.4';
    
    private $cpt_manager;
    private $taxonomy_manager;
    private $meta_manager;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->check_requirements();
        $this->init_managers();
        $this->init_hooks();
    }
    
    private function check_requirements() {
        global $wp_version;
        
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'php_version_notice'));
            return;
        }
        
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'wp_version_notice'));
            return;
        }
    }
    
    public function php_version_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php 
                printf(
                    __('Houzez requires PHP version %s or higher. You are running version %s.', 'houzez'),
                    self::MIN_PHP_VERSION,
                    PHP_VERSION
                );
            ?></p>
        </div>
        <?php
    }
    
    public function wp_version_notice() {
        global $wp_version;
        ?>
        <div class="notice notice-error">
            <p><?php 
                printf(
                    __('Houzez requires WordPress version %s or higher. You are running version %s.', 'houzez'),
                    self::MIN_WP_VERSION,
                    $wp_version
                );
            ?></p>
        </div>
        <?php
    }
    
    private function init_managers() {
        $this->cpt_manager = new HouzezCPTManager();
        $this->taxonomy_manager = new HouzezTaxonomyManager();
        $this->meta_manager = new HouzezMetaManager();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function activate() {
        $this->init();
        flush_rewrite_rules();
        
        if (!get_option('houzez_activated')) {
            update_option('houzez_activated', time());
        }
    }
    
    public function deactivate() {
        flush_rewrite_rules();
        $this->clear_transients();
    }
    
    private function clear_transients() {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_houzez_properties_%'
            )
        );
    }
    
    public function init() {
        $this->register_custom_post_types();
        $this->register_taxonomies();
        $this->register_meta_fields();
        $this->register_blocks();
        
        load_plugin_textdomain('houzez', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        do_action('houzez_plugin_initialized');
    }
    
    /**
     * Register blocks with SSR
     */
    public function register_blocks() {
        $block_path = plugin_dir_path(__FILE__) . 'build/houzez';
        
        if (!file_exists($block_path . '/block.json')) {
            error_log('Houzez: block.json not found at ' . $block_path);
            return;
        }

        // Include the renderer file
        if (file_exists(plugin_dir_path(__FILE__) . 'src/houzez/render.php')) {
            include_once plugin_dir_path(__FILE__) . 'src/houzez/render.php';
        }

        $block_args = apply_filters('houzez_property_block_args', array(
            'render_callback' => array('HouzezBlockRenderer', 'render_properties_block')
        ));
        
        register_block_type($block_path, $block_args);
    }
    
    public function register_custom_post_types() {
        $this->cpt_manager->register_property_cpt();
    }
    
    public function register_taxonomies() {
        $this->taxonomy_manager->register_property_taxonomy();
        $this->taxonomy_manager->register_property_status();
        $this->taxonomy_manager->register_property_type();
    }
    
    public function register_meta_fields() {
        $this->meta_manager->register_property_meta_fields();
    }
    
    public function enqueue_frontend_scripts() {
        if (file_exists(plugin_dir_path(__FILE__) . 'src/houzez/style.css')) {
            wp_enqueue_style(
                'houzez-block-style',
                plugins_url('src/houzez/style.css', __FILE__),
                array('wp-block-library'),
                self::VERSION
            );
        } elseif (file_exists(plugin_dir_path(__FILE__) . 'build/houzez/style.css')) {
            wp_enqueue_style(
                'houzez-block-style',
                plugins_url('build/houzez/style.css', __FILE__),
                array('wp-block-library'),
                self::VERSION
            );
        }
    }
    
    public function enqueue_admin_scripts($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php', 'edit.php'))) {
            return;
        }
        
        global $post_type;
        if ($post_type !== 'property') {
            return;
        }
        
        if (file_exists(plugin_dir_path(__FILE__) . 'src/houzez/admin.css')) {
            wp_enqueue_style(
                'houzez-admin',
                plugins_url('src/houzez/admin.css', __FILE__),
                array(),
                self::VERSION
            );
        }
    }
    
    private function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'houzez'), '1.0');
    }
    
    public function __wakeup() {
        _doing_it_wrong(__FUNCTION__, __('Unserializing is forbidden.', 'houzez'), '1.0');
    }
}

} // End class_exists check

/**
 * Initialize the plugin
 */
function houzez_plugin_init() {
    return HouzezPlugin::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'houzez_plugin_init');

/**
 * Helper function to get plugin instance
 */
function houzez() {
    return HouzezPlugin::get_instance();
}