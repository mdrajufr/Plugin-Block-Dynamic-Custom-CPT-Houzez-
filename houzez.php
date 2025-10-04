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
 * Custom Post Type Manager Class
 */
class HouzezCPTManager {
    
    /**
     * Register Property Custom Post Type
     */
    public function register_property_cpt() {
        $labels = $this->get_property_labels();
        $args = $this->get_property_args($labels);
        
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
            'taxonomies'    => array('property_category', 'property_status', 'property_type'),
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
        
        // Register default terms
        $this->register_default_status_terms();
    }
    
    /**
     * Register Property Type Taxonomy
     */
    public function register_property_type() {
        $labels = $this->get_type_labels();
        $args = $this->get_taxonomy_args($labels);
        
        register_taxonomy('property_type', array('property'), $args);
        
        // Register default terms
        $this->register_default_type_terms();
    }
    
    /**
     * Get category taxonomy labels
     *
     * @return array
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
     *
     * @return array
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
     *
     * @return array
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
        // Price field
        register_post_meta('property', 'fave_property_price', array(
            'type' => 'string',
            'description' => 'Property price',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Location field
        register_post_meta('property', 'fave_property_location', array(
            'type' => 'string',
            'description' => 'Property location',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Size field
        register_post_meta('property', 'fave_property_size', array(
            'type' => 'string',
            'description' => 'Property size',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Bedrooms field
        register_post_meta('property', 'fave_property_bedrooms', array(
            'type' => 'string',
            'description' => 'Number of bedrooms',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Bathrooms field
        register_post_meta('property', 'fave_property_bathrooms', array(
            'type' => 'string',
            'description' => 'Number of bathrooms',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Garage field
        register_post_meta('property', 'fave_property_garage', array(
            'type' => 'string',
            'description' => 'Number of garage spaces',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Year built field
        register_post_meta('property', 'fave_property_year', array(
            'type' => 'string',
            'description' => 'Year built',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Agent field
        register_post_meta('property', 'fave_agents', array(
            'type' => 'string',
            'description' => 'Property agent',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        // Featured property field
        register_post_meta('property', 'fave_featured', array(
            'type' => 'boolean',
            'description' => 'Is property featured',
            'single' => true,
            'show_in_rest' => true,
            'default' => false
        ));
    }
}

/**
 * Block Renderer Class
 */
class HouzezBlockRenderer {
    private $attributes;
    private $defaults;
    private $query;
    
    /**
     * Constructor
     */
    public function __construct($attributes = array()) {
        $this->attributes = $attributes;
        $this->set_defaults();
        $this->sanitize_attributes();
    }
    
    /**
     * Set default attribute values
     */
    private function set_defaults() {
        $this->defaults = array(
            'postsToShow' => 6,
            'order' => 'DESC',
            'orderBy' => 'date',
            'layout' => 'grid',
            'columns' => 3,
            'showFeatured' => true,
            'showPrice' => true,
            'showLocation' => true,
            'showSize' => true,
            'showBedrooms' => true,
            'showBathrooms' => true,
            'showGarage' => true,
            'showYearBuilt' => true,
            'showAgent' => true,
            'showStatus' => true,
            'showTaxonomies' => true,
            'showExcerpt' => true,
            'excerptLength' => 20,
            'showMeta' => true,
            'showMap' => false,
            'imageSize' => 'medium_large',
            'pricePrefix' => '$',
            'sizeSuffix' => 'sq ft',
            'categoryFilter' => '',
            'statusFilter' => '',
            'featuredOnly' => false
        );
    }
    
    /**
     * Sanitize and validate attributes
     */
    private function sanitize_attributes() {
        $this->attributes = wp_parse_args((array)$this->attributes, $this->defaults);
        
        // Sanitize values
        $this->attributes['postsToShow'] = max(1, (int)$this->attributes['postsToShow']);
        $this->attributes['columns'] = max(1, min(6, (int)$this->attributes['columns']));
        $this->attributes['excerptLength'] = max(10, (int)$this->attributes['excerptLength']);
        
        // Validate enums
        $this->attributes['order'] = in_array($this->attributes['order'], array('ASC', 'DESC'), true) ? $this->attributes['order'] : 'DESC';
        $this->attributes['orderBy'] = in_array($this->attributes['orderBy'], array('date', 'modified', 'title', 'price', 'size'), true) ? $this->attributes['orderBy'] : 'date';
        $this->attributes['layout'] = in_array($this->attributes['layout'], array('grid', 'list', 'masonry', 'carousel'), true) ? $this->attributes['layout'] : 'grid';
        $this->attributes['imageSize'] = in_array($this->attributes['imageSize'], array('thumbnail', 'medium', 'medium_large', 'large', 'full'), true) ? $this->attributes['imageSize'] : 'medium_large';
        
        // Sanitize booleans
        $bool_attributes = array(
            'showFeatured', 'showPrice', 'showLocation', 'showSize', 'showBedrooms',
            'showBathrooms', 'showGarage', 'showYearBuilt', 'showAgent', 'showStatus',
            'showTaxonomies', 'showExcerpt', 'showMeta', 'showMap', 'featuredOnly'
        );
        
        foreach ($bool_attributes as $attr) {
            $this->attributes[$attr] = (bool) $this->attributes[$attr];
        }
        
        // Sanitize strings
        $this->attributes['pricePrefix'] = sanitize_text_field($this->attributes['pricePrefix']);
        $this->attributes['sizeSuffix'] = sanitize_text_field($this->attributes['sizeSuffix']);
        $this->attributes['categoryFilter'] = sanitize_text_field($this->attributes['categoryFilter']);
        $this->attributes['statusFilter'] = sanitize_text_field($this->attributes['statusFilter']);
    }
    
    /**
     * Build meta query for property features
     */
    private function build_meta_query() {
        $meta_query = array();
        
        if ($this->attributes['featuredOnly']) {
            $meta_query[] = array(
                'key' => 'fave_featured',
                'value' => '1',
                'compare' => '='
            );
        }
        
        return $meta_query;
    }
    
    /**
     * Build tax query for filters
     */
    private function build_tax_query() {
        $tax_query = array();
        
        // Property type filter
        if (!empty($this->attributes['categoryFilter'])) {
            $tax_query[] = array(
                'taxonomy' => 'property_type',
                'field' => 'slug',
                'terms' => $this->attributes['categoryFilter']
            );
        }
        
        // Property status filter
        if (!empty($this->attributes['statusFilter'])) {
            $tax_query[] = array(
                'taxonomy' => 'property_status',
                'field' => 'slug',
                'terms' => $this->attributes['statusFilter']
            );
        }
        
        return $tax_query;
    }
    
    /**
     * Execute the query
     */
    private function execute_query() {
        $query_args = array(
            'post_type'      => 'property',
            'posts_per_page' => $this->attributes['postsToShow'],
            'post_status'    => 'publish',
            'order'          => $this->attributes['order'],
            'orderby'        => $this->attributes['orderBy']
        );
        
        // Add meta query if needed
        $meta_query = $this->build_meta_query();
        if (!empty($meta_query)) {
            $query_args['meta_query'] = $meta_query;
        }
        
        // Add tax query if needed
        $tax_query = $this->build_tax_query();
        if (!empty($tax_query)) {
            $query_args['tax_query'] = $tax_query;
        }
        
        $this->query = new WP_Query($query_args);
        
        return $this->query->have_posts();
    }
    
    /**
     * Get property meta data
     */
    private function get_property_meta($post_id) {
        return array(
            'price' => get_post_meta($post_id, 'fave_property_price', true),
            'location' => get_post_meta($post_id, 'fave_property_location', true),
            'size' => get_post_meta($post_id, 'fave_property_size', true),
            'bedrooms' => get_post_meta($post_id, 'fave_property_bedrooms', true),
            'bathrooms' => get_post_meta($post_id, 'fave_property_bathrooms', true),
            'garage' => get_post_meta($post_id, 'fave_property_garage', true),
            'year_built' => get_post_meta($post_id, 'fave_property_year', true),
            'agent' => get_post_meta($post_id, 'fave_agents', true),
            'status' => wp_get_post_terms($post_id, 'property_status', array('fields' => 'names'))
        );
    }
    
    /**
     * Format property price
     */
    private function format_price($price) {
        if (empty($price)) return '';
        
        return $this->attributes['pricePrefix'] . number_format(floatval($price));
    }
    
    /**
     * Format property size
     */
    private function format_size($size) {
        if (empty($size)) return '';
        
        return number_format(floatval($size)) . ' ' . $this->attributes['sizeSuffix'];
    }
    
    /**
     * Get property excerpt
     */
    private function get_property_excerpt($post_id) {
        $excerpt = get_the_excerpt($post_id);
        if (empty($excerpt)) {
            $excerpt = get_the_content($post_id);
        }
        
        $words = explode(' ', wp_strip_all_tags($excerpt));
        
        if (count($words) > $this->attributes['excerptLength']) {
            $words = array_slice($words, 0, $this->attributes['excerptLength']);
            $excerpt = implode(' ', $words) . '...';
        }
        
        return $excerpt;
    }
    
    /**
     * Render property image
     */
    private function render_property_image($post_id, $permalink) {
        if (has_post_thumbnail($post_id)) {
            $image = get_the_post_thumbnail($post_id, $this->attributes['imageSize'], array(
                'class' => 'property-image',
                'alt' => get_the_title($post_id)
            ));
            ?>
            <div class="property-image-wrap">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo $image; ?>
                </a>
                <?php if ($this->attributes['showFeatured'] && get_post_meta($post_id, 'fave_featured', true)): ?>
                    <span class="featured-label"><?php esc_html_e('Featured', 'houzez'); ?></span>
                <?php endif; ?>
            </div>
            <?php
        } else {
            // Fallback image
            ?>
            <div class="property-image-wrap">
                <a href="<?php echo esc_url($permalink); ?>">
                    <div style="background: #f0f0f0; height: 200px; display: flex; align-items: center; justify-content: center; color: #666;">
                        <?php esc_html_e('No Image', 'houzez'); ?>
                    </div>
                </a>
            </div>
            <?php
        }
    }
    
    /**
     * Render property meta information
     */
    private function render_property_meta($meta) {
        if (!$this->attributes['showMeta']) return;
        ?>
        <div class="property-meta">
            <?php if ($this->attributes['showBedrooms'] && !empty($meta['bedrooms'])): ?>
                <span class="meta-bedrooms">
                    <?php echo esc_html($meta['bedrooms']); ?> <?php esc_html_e('Beds', 'houzez'); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showBathrooms'] && !empty($meta['bathrooms'])): ?>
                <span class="meta-bathrooms">
                    <?php echo esc_html($meta['bathrooms']); ?> <?php esc_html_e('Baths', 'houzez'); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showSize'] && !empty($meta['size'])): ?>
                <span class="meta-size">
                    <?php echo esc_html($this->format_size($meta['size'])); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showGarage'] && !empty($meta['garage'])): ?>
                <span class="meta-garage">
                    <?php echo esc_html($meta['garage']); ?> <?php esc_html_e('Garage', 'houzez'); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render property details
     */
    private function render_property_details($post_id, $permalink, $title, $meta) {
        ?>
        <div class="property-details">
            <h3 class="property-title">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>
            
            <?php if ($this->attributes['showLocation'] && !empty($meta['location'])): ?>
                <div class="property-location">
                    📍 <?php echo esc_html($meta['location']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->attributes['showPrice'] && !empty($meta['price'])): ?>
                <div class="property-price">
                    <?php echo esc_html($this->format_price($meta['price'])); ?>
                </div>
            <?php endif; ?>
            
            <?php $this->render_property_meta($meta); ?>
            
            <?php if ($this->attributes['showExcerpt']): ?>
                <div class="property-excerpt">
                    <?php echo wp_kses_post($this->get_property_excerpt($post_id)); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->attributes['showAgent'] && !empty($meta['agent'])): ?>
                <div class="property-agent">
                    <strong><?php esc_html_e('Agent:', 'houzez'); ?></strong> <?php echo esc_html($meta['agent']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->attributes['showStatus'] && !empty($meta['status'])): ?>
                <div class="property-status">
                    <strong><?php esc_html_e('Status:', 'houzez'); ?></strong> <?php echo esc_html(implode(', ', $meta['status'])); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->attributes['showYearBuilt'] && !empty($meta['year_built'])): ?>
                <div class="property-year-built">
                    <strong><?php esc_html_e('Year Built:', 'houzez'); ?></strong> <?php echo esc_html($meta['year_built']); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render individual property item
     */
    private function render_property_item($post_id, $permalink, $title) {
        $meta = $this->get_property_meta($post_id);
        ?>
        <article class="property-item <?php echo esc_attr($this->attributes['layout']); ?>-item">
            <?php $this->render_property_image($post_id, $permalink); ?>
            <?php $this->render_property_details($post_id, $permalink, $title, $meta); ?>
        </article>
        <?php
    }
    
    /**
     * Render the no properties message
     */
    private function render_no_properties() {
        return '<div class="houzez-no-properties"><p>' . esc_html__('No properties found.', 'houzez') . '</p></div>';
    }
    
    /**
     * Get CSS classes for the wrapper
     */
    private function get_wrapper_classes() {
        $classes = array(
            'houzez-properties-block',
            'layout-' . esc_attr($this->attributes['layout']),
            'columns-' . esc_attr($this->attributes['columns'])
        );
        
        return implode(' ', $classes);
    }
    
    /**
     * Get inline styles for grid layout
     */
    private function get_grid_styles() {
        if ($this->attributes['layout'] === 'grid') {
            return 'style="grid-template-columns: repeat(' . esc_attr($this->attributes['columns']) . ', 1fr);"';
        }
        return '';
    }
    
    /**
     * Main rendering method
     */
    public function render() {
        // Check if properties post type exists
        if (!post_type_exists('property')) {
            return '<div class="houzez-error"><p>' . esc_html__('Properties post type is not registered.', 'houzez') . '</p></div>';
        }
        
        if (!$this->execute_query()) {
            return $this->render_no_properties();
        }
        
        ob_start();
        ?>
        <div class="<?php echo $this->get_wrapper_classes(); ?>" <?php echo $this->get_grid_styles(); ?>>
            <?php
            while ($this->query->have_posts()):
                $this->query->the_post();
                $post_id = get_the_ID();
                $permalink = get_permalink($post_id);
                $title = get_the_title($post_id);
                $this->render_property_item($post_id, $permalink, $title);
            endwhile;
            
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Initialize plugin functionality
     */
    public function init() {
        $this->register_custom_post_types();
        $this->register_taxonomies();
        $this->register_meta_fields();
        $this->register_blocks();
        
        // Load text domain for translations
        load_plugin_textdomain('houzez', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Register blocks
     */
    public function register_blocks() {
        // First register the block with render callback
        register_block_type('create-block/houzez', array(
            'render_callback' => array($this, 'render_properties_block'),
            'attributes' => $this->get_block_attributes()
        ));
        
        /**
         * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
         * based on the registered block metadata.
         */
        if (function_exists('wp_register_block_types_from_metadata_collection')) {
            wp_register_block_types_from_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
        }
        elseif (function_exists('wp_register_block_metadata_collection')) {
            wp_register_block_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
        }
        
        /**
         * Registers the block type(s) in the `blocks-manifest.php` file.
         */
        if (file_exists(__DIR__ . '/build/blocks-manifest.php')) {
            $manifest_data = require __DIR__ . '/build/blocks-manifest.php';
            foreach (array_keys($manifest_data) as $block_type) {
                register_block_type(__DIR__ . "/build/{$block_type}");
            }
        }
    }
    
    /**
     * Get block attributes for server-side rendering
     */
    private function get_block_attributes() {
        return array(
            'postsToShow' => array(
                'type' => 'number',
                'default' => 6
            ),
            'order' => array(
                'type' => 'string',
                'enum' => array('ASC', 'DESC'),
                'default' => 'DESC'
            ),
            'orderBy' => array(
                'type' => 'string',
                'enum' => array('date', 'modified', 'title', 'price', 'size'),
                'default' => 'date'
            ),
            'layout' => array(
                'type' => 'string',
                'enum' => array('grid', 'list', 'masonry', 'carousel'),
                'default' => 'grid'
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 3
            ),
            'showFeatured' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showPrice' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showLocation' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showSize' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showBedrooms' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showBathrooms' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showGarage' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showYearBuilt' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showAgent' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showStatus' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showTaxonomies' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showExcerpt' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'excerptLength' => array(
                'type' => 'number',
                'default' => 20
            ),
            'showMeta' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showMap' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'imageSize' => array(
                'type' => 'string',
                'enum' => array('thumbnail', 'medium', 'medium_large', 'large', 'full'),
                'default' => 'medium_large'
            ),
            'pricePrefix' => array(
                'type' => 'string',
                'default' => '$'
            ),
            'sizeSuffix' => array(
                'type' => 'string',
                'default' => 'sq ft'
            ),
            'categoryFilter' => array(
                'type' => 'string',
                'default' => ''
            ),
            'statusFilter' => array(
                'type' => 'string',
                'default' => ''
            ),
            'featuredOnly' => array(
                'type' => 'boolean',
                'default' => false
            )
        );
    }
    
    /**
     * Render callback for properties block
     */
    public function render_properties_block($attributes, $content) {
        // Use a different class name to avoid conflicts
        $renderer = new HouzezBlockRenderer($attributes);
        return $renderer->render();
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
        $taxonomy_manager->register_property_status();
        $taxonomy_manager->register_property_type();
    }
    
    /**
     * Register meta fields for properties
     */
    public function register_meta_fields() {
        $meta_manager = new HouzezMetaManager();
        $meta_manager->register_property_meta_fields();
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        // Basic inline styles for the block
        wp_add_inline_style('wp-block-library', $this->get_block_styles());
    }
    
    /**
     * Get basic block styles
     */
    private function get_block_styles() {
        return "
        .houzez-properties-block {
            margin: 20px 0;
        }
        .houzez-properties-block.layout-grid {
            display: grid;
            gap: 20px;
        }
        .property-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }
        .property-image-wrap {
            position: relative;
        }
        .property-image-wrap img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .featured-label {
            background: #ff5a5f;
            color: white;
            padding: 5px 10px;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 3px;
        }
        .property-details {
            padding: 15px;
        }
        .property-title {
            margin: 0 0 10px 0;
            font-size: 1.2em;
        }
        .property-title a {
            text-decoration: none;
            color: #333;
        }
        .property-price {
            font-size: 1.3em;
            font-weight: bold;
            color: #ff5a5f;
            margin: 10px 0;
        }
        .property-meta {
            display: flex;
            gap: 15px;
            margin: 10px 0;
            flex-wrap: wrap;
        }
        .property-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .property-excerpt {
            margin: 10px 0;
            color: #666;
        }
        ";
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts() {
        // Admin styles can be added here if needed
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