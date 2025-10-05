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
            $result = register_post_type('property', $args);
            
            // register_post_type returns void on success, WP_Error on failure
            if (is_wp_error($result)) {
                throw new Exception($result->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Houzez: Failed to register property CPT - ' . $e->getMessage());
        }
    }
    
    /**
     * Get property CPT labels
     *
     * @return array
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
            'capability_type' => 'post',
            'map_meta_cap'  => true,
        );
    }
    
    /**
     * Get property CPT supported features
     *
     * @return array
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
        
        try {
            $result = register_taxonomy('property_category', array('property'), $args);
            
            if (is_wp_error($result)) {
                throw new Exception($result->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Houzez: Failed to register property_category taxonomy - ' . $e->getMessage());
        }
    }
    
    /**
     * Register Property Status Taxonomy
     */
    public function register_property_status() {
        $labels = $this->get_status_labels();
        $args = $this->get_taxonomy_args($labels);
        
        try {
            $result = register_taxonomy('property_status', array('property'), $args);
            
            if (!is_wp_error($result)) {
                $this->register_default_status_terms();
            } else {
                throw new Exception($result->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Houzez: Failed to register property_status taxonomy - ' . $e->getMessage());
        }
    }
    
    /**
     * Register Property Type Taxonomy
     */
    public function register_property_type() {
        $labels = $this->get_type_labels();
        $args = $this->get_taxonomy_args($labels);
        
        try {
            $result = register_taxonomy('property_type', array('property'), $args);
            
            if (!is_wp_error($result)) {
                $this->register_default_type_terms();
            } else {
                throw new Exception($result->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Houzez: Failed to register property_type taxonomy - ' . $e->getMessage());
        }
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
                $result = wp_insert_term($name, 'property_status', array('slug' => $slug));
                if (is_wp_error($result)) {
                    error_log('Houzez: Failed to insert term ' . $slug . ' - ' . $result->get_error_message());
                }
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
                $result = wp_insert_term($name, 'property_type', array('slug' => $slug));
                if (is_wp_error($result)) {
                    error_log('Houzez: Failed to insert term ' . $slug . ' - ' . $result->get_error_message());
                }
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
            $result = register_post_meta('property', $key, $args);
            
            // register_post_meta returns boolean, not WP_Error
            if ($result === false) {
                error_log('Houzez: Failed to register meta ' . $key);
            }
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
        return max(0, min(20, $bedrooms)); // Reasonable limit
    }
    
    /**
     * Sanitize bathrooms field
     */
    public function sanitize_bathrooms($value) {
        $bathrooms = floatval($value);
        return max(0, min(10, $bathrooms)); // Reasonable limit
    }
    
    /**
     * Sanitize garage field
     */
    public function sanitize_garage($value) {
        $garage = intval($value);
        return max(0, min(10, $garage)); // Reasonable limit
    }
    
    /**
     * Sanitize year field
     */
    public function sanitize_year($value) {
        $year = intval($value);
        $current_year = (int) date('Y');
        return max(1800, min($current_year + 5, $year)); // Reasonable range
    }
}

/**
 * Template Renderer Class
 */
class HouzezTemplateRenderer {
    private $attributes;
    
    public function __construct($attributes) {
        $this->attributes = $attributes;
    }
    
    /**
     * Render property image
     */
    public function render_property_image($post_id, $permalink) {
        if (has_post_thumbnail($post_id)) {
            $image = get_the_post_thumbnail($post_id, $this->attributes['imageSize'], array(
                'class' => 'property-image',
                'alt' => esc_attr(get_the_title($post_id)),
                'loading' => 'lazy'
            ));
            ?>
            <div class="property-image-wrap">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo wp_kses_post($image); // Fixed: escape image output ?>
                </a>
                <?php if ($this->attributes['showFeatured'] && get_post_meta($post_id, 'fave_featured', true)): ?>
                    <span class="featured-label"><?php esc_html_e('Featured', 'houzez'); ?></span>
                <?php endif; ?>
            </div>
            <?php
        } else {
            ?>
            <div class="property-image-wrap">
                <a href="<?php echo esc_url($permalink); ?>">
                    <div class="property-image-placeholder">
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
    public function render_property_meta($meta) {
        if (!$this->attributes['showMeta']) return;
        ?>
        <div class="property-meta">
            <?php if ($this->attributes['showBedrooms'] && !empty($meta['bedrooms'])): ?>
                <span class="meta-bedrooms">
                    🛏️ <?php echo esc_html($meta['bedrooms']); ?> <?php esc_html_e('Beds', 'houzez'); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showBathrooms'] && !empty($meta['bathrooms'])): ?>
                <span class="meta-bathrooms">
                    🚿 <?php echo esc_html($meta['bathrooms']); ?> <?php esc_html_e('Baths', 'houzez'); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showSize'] && !empty($meta['size'])): ?>
                <span class="meta-size">
                    📐 <?php echo esc_html($this->format_size($meta['size'])); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showGarage'] && !empty($meta['garage'])): ?>
                <span class="meta-garage">
                    🚗 <?php echo esc_html($meta['garage']); ?> <?php esc_html_e('Garage', 'houzez'); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Format property size
     */
    private function format_size($size) {
        if (empty($size)) return '';
        return number_format(floatval($size)) . ' ' . esc_html($this->attributes['sizeSuffix']);
    }
    
    /**
     * Render property details
     */
    public function render_property_details($post_id, $permalink, $title, $meta) {
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
     * Format property price
     */
    private function format_price($price) {
        if (empty($price)) return '';
        return esc_html($this->attributes['pricePrefix']) . number_format(floatval($price));
    }
    
    /**
     * Get property excerpt
     */
    private function get_property_excerpt($post_id) {
        $excerpt = get_the_excerpt($post_id);
        if (empty($excerpt)) {
            $excerpt = get_the_content(null, false, $post_id);
        }
        
        $words = explode(' ', wp_strip_all_tags($excerpt));
        
        if (count($words) > $this->attributes['excerptLength']) {
            $words = array_slice($words, 0, $this->attributes['excerptLength']);
            $excerpt = implode(' ', $words) . '...';
        }
        
        return $excerpt;
    }
    
    /**
     * Render individual property item
     */
    public function render_property_item($post_id, $permalink, $title, $meta) {
        ?>
        <article class="property-item <?php echo esc_attr($this->attributes['layout']); ?>-item">
            <?php $this->render_property_image($post_id, $permalink); ?>
            <?php $this->render_property_details($post_id, $permalink, $title, $meta); ?>
        </article>
        <?php
    }
}

/**
 * Block Renderer Class
 */
class HouzezBlockRenderer {
    private $attributes;
    private $defaults;
    private $query;
    private $template_renderer;
    
    /**
     * Constructor
     */
    public function __construct($attributes = array()) {
        $this->attributes = $attributes;
        $this->set_defaults();
        $this->sanitize_attributes();
        $this->template_renderer = new HouzezTemplateRenderer($this->attributes);
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
            'imageSize' => HouzezConstants::IMAGE_SIZE_MEDIUM_LARGE, // Using constant
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
        
        // Sanitize values with bounds using constants
        $this->attributes['postsToShow'] = max(1, min(HouzezConstants::MAX_POSTS_TO_SHOW, (int)$this->attributes['postsToShow']));
        $this->attributes['columns'] = max(1, min(HouzezConstants::MAX_COLUMNS, (int)$this->attributes['columns']));
        $this->attributes['excerptLength'] = max(1, min(HouzezConstants::MAX_EXCERPT_LENGTH, (int)$this->attributes['excerptLength']));
        
        // Validate enums
        $this->attributes['order'] = in_array($this->attributes['order'], array('ASC', 'DESC'), true) ? $this->attributes['order'] : 'DESC';
        $this->attributes['orderBy'] = in_array($this->attributes['orderBy'], array('date', 'modified', 'title', 'price', 'size'), true) ? $this->attributes['orderBy'] : 'date';
        $this->attributes['layout'] = in_array($this->attributes['layout'], array('grid', 'list', 'masonry', 'carousel'), true) ? $this->attributes['layout'] : 'grid';
        
        // Validate image sizes using constants
        $valid_image_sizes = array(
            HouzezConstants::IMAGE_SIZE_THUMBNAIL,
            HouzezConstants::IMAGE_SIZE_MEDIUM,
            HouzezConstants::IMAGE_SIZE_MEDIUM_LARGE,
            HouzezConstants::IMAGE_SIZE_LARGE,
            HouzezConstants::IMAGE_SIZE_FULL
        );
        $this->attributes['imageSize'] = in_array($this->attributes['imageSize'], $valid_image_sizes, true) ? $this->attributes['imageSize'] : HouzezConstants::IMAGE_SIZE_MEDIUM_LARGE;
        
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
        
        // Capability checks
        if (!current_user_can('edit_posts')) {
            $this->attributes['showFeatured'] = false;
        }
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
        
        return apply_filters('houzez_property_block_meta_query', $meta_query, $this->attributes); // Improved filter name
    }
    
    /**
     * Build tax query for filters
     */
    private function build_tax_query() {
        $tax_query = array('relation' => 'AND');
        
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
        
        return apply_filters('houzez_property_block_tax_query', $tax_query, $this->attributes); // Improved filter name
    }
    
    /**
     * Execute the query with optimized caching
     */
    private function execute_query() {
        // Generate cache key based on attributes
        $transient_key = 'houzez_properties_' . md5(serialize($this->attributes));
        $cached_post_ids = get_transient($transient_key);
        
        // Optimized caching: Store only post IDs instead of entire WP_Query object
        if ($cached_post_ids !== false && is_array($cached_post_ids) && !empty($cached_post_ids)) {
            $query_args = array(
                'post_type'      => 'property',
                'post_status'    => 'publish',
                'posts_per_page' => $this->attributes['postsToShow'],
                'post__in'       => $cached_post_ids,
                'orderby'        => 'post__in', // Maintain cached order
                'no_found_rows'  => true,
            );
            
            $this->query = new WP_Query($query_args);
            return $this->query->have_posts();
        }
        
        // No cache found or cache invalid, build fresh query
        $query_args = array(
            'post_type'      => 'property',
            'posts_per_page' => $this->attributes['postsToShow'],
            'post_status'    => 'publish',
            'order'          => $this->attributes['order'],
            'orderby'        => $this->attributes['orderBy'],
            'no_found_rows'  => true,
        );
        
        // Add meta query if needed
        $meta_query = $this->build_meta_query();
        if (!empty($meta_query)) {
            $query_args['meta_query'] = $meta_query;
        }
        
        // Add tax query if needed
        $tax_query = $this->build_tax_query();
        if (count($tax_query) > 1) { // More than just relation
            $query_args['tax_query'] = $tax_query;
        }
        
        $this->query = new WP_Query(apply_filters('houzez_property_block_query_args', $query_args, $this->attributes)); // Improved filter name
        
        // Cache only post IDs for better performance
        if ($this->query->have_posts()) {
            $post_ids = wp_list_pluck($this->query->posts, 'ID');
            set_transient($transient_key, $post_ids, HouzezConstants::CACHE_EXPIRATION);
        }
        
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
                $meta = $this->get_property_meta($post_id);
                
                $this->template_renderer->render_property_item($post_id, $permalink, $title, $meta);
            endwhile;
            
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Block Renderer Factory
 */
class HouzezBlockRendererFactory {
    
    /**
     * Create block renderer instance
     */
    public static function create($attributes = array()) {
        return new HouzezBlockRenderer($attributes);
    }
}

/**
 * Main Plugin Class with Robust Singleton Pattern
 */
class HouzezPlugin {
    
    /**
     * Plugin instance
     *
     * @var HouzezPlugin
     */
    private static $instance = null;
    
    /**
     * Plugin version
     */
    const VERSION = '0.1.0';
    
    /**
     * Minimum required WordPress version
     */
    const MIN_WP_VERSION = '6.7';
    
    /**
     * Minimum required PHP version  
     */
    const MIN_PHP_VERSION = '7.4';
    
    /**
     * Manager instances
     */
    private $cpt_manager;
    private $taxonomy_manager;
    private $meta_manager;
    
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
        $this->check_requirements();
        $this->init_managers();
        $this->init_hooks();
    }
    
    /**
     * Check system requirements
     */
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
    
    /**
     * PHP version notice
     */
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
    
    /**
     * WordPress version notice
     */
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
    
    /**
     * Initialize manager instances
     */
    private function init_managers() {
        $this->cpt_manager = new HouzezCPTManager();
        $this->taxonomy_manager = new HouzezTaxonomyManager();
        $this->meta_manager = new HouzezMetaManager();
    }
    
    /**
     * Get manager instances
     */
    public function get_cpt_manager() {
        return $this->cpt_manager;
    }
    
    public function get_taxonomy_manager() {
        return $this->taxonomy_manager;
    }
    
    public function get_meta_manager() {
        return $this->meta_manager;
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $this->init();
        flush_rewrite_rules();
        
        // Set a flag for first activation
        if (!get_option('houzez_activated')) {
            update_option('houzez_activated', time());
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
        
        // Clear any transients we created
        $this->clear_transients();
    }
    
    /**
     * Clear plugin transients
     */
    private function clear_transients() {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_houzez_properties_%'
            )
        );
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_timeout_houzez_properties_%'
            )
        );
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
        
        do_action('houzez_plugin_initialized');
    }
    
    /**
     * Register blocks with error handling and extensibility
     */
    public function register_blocks() {
        $block_path = plugin_dir_path(__FILE__) . 'build/houzez';
        
        if (!file_exists($block_path . '/block.json')) {
            error_log('Houzez: block.json not found at ' . $block_path);
            return;
        }

        /**
         * Filter block registration arguments
         */
        $block_args = apply_filters('houzez_property_block_args', array( // Improved filter name
            'render_callback' => array($this, 'render_properties_block')
        ));
        
        try {
            $result = register_block_type($block_path, $block_args);
            
            if ($result === false) {
                error_log('Houzez: Failed to register block - unknown error');
            }
        } catch (Exception $e) {
            error_log('Houzez: Failed to register block - ' . $e->getMessage());
        }
    }
    
    /**
     * Render callback for properties block
     */
    public function render_properties_block($attributes, $content) {
        $renderer = HouzezBlockRendererFactory::create($attributes);
        return $renderer->render();
    }
    
    /**
     * Register custom post types
     */
    public function register_custom_post_types() {
        $this->cpt_manager->register_property_cpt();
    }
    
    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        $this->taxonomy_manager->register_property_taxonomy();
        $this->taxonomy_manager->register_property_status();
        $this->taxonomy_manager->register_property_type();
    }
    
    /**
     * Register meta fields for properties
     */
    public function register_meta_fields() {
        $this->meta_manager->register_property_meta_fields();
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        // Enqueue compiled CSS from SCSS
        if (file_exists(plugin_dir_path(__FILE__) . 'src/houzez/style.css')) {
            wp_enqueue_style(
                'houzez-block-style',
                plugins_url('src/houzez/style.css', __FILE__),
                array('wp-block-library'),
                self::VERSION
            );
        } elseif (file_exists(plugin_dir_path(__FILE__) . 'build/houzez/style.css')) {
            // Fallback to build directory
            wp_enqueue_style(
                'houzez-block-style',
                plugins_url('build/houzez/style.css', __FILE__),
                array('wp-block-library'),
                self::VERSION
            );
        }
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on property-related pages
        if (!in_array($hook, array('post.php', 'post-new.php', 'edit.php'))) {
            return;
        }
        
        global $post_type;
        if ($post_type !== 'property') {
            return;
        }
        
        // Enqueue admin CSS if it exists
        if (file_exists(plugin_dir_path(__FILE__) . 'src/houzez/admin.css')) {
            wp_enqueue_style(
                'houzez-admin',
                plugins_url('src/houzez/admin.css', __FILE__),
                array(),
                self::VERSION
            );
        }
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'houzez'), '1.0');
    }
    
    /**
     * Prevent unserialization
     */
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