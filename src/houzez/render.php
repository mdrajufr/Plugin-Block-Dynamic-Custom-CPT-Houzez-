<?php

/**
 * Server Side Renderer for Houzez Properties
 * OOP implementation with proper encapsulation and separation of concerns
 */
class HouzezPropertyRenderer {
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
        $this->attributes['showFeatured'] = (bool) $this->attributes['showFeatured'];
        $this->attributes['showPrice'] = (bool) $this->attributes['showPrice'];
        $this->attributes['showLocation'] = (bool) $this->attributes['showLocation'];
        $this->attributes['showSize'] = (bool) $this->attributes['showSize'];
        $this->attributes['showBedrooms'] = (bool) $this->attributes['showBedrooms'];
        $this->attributes['showBathrooms'] = (bool) $this->attributes['showBathrooms'];
        $this->attributes['showGarage'] = (bool) $this->attributes['showGarage'];
        $this->attributes['showYearBuilt'] = (bool) $this->attributes['showYearBuilt'];
        $this->attributes['showAgent'] = (bool) $this->attributes['showAgent'];
        $this->attributes['showStatus'] = (bool) $this->attributes['showStatus'];
        $this->attributes['showTaxonomies'] = (bool) $this->attributes['showTaxonomies'];
        $this->attributes['showExcerpt'] = (bool) $this->attributes['showExcerpt'];
        $this->attributes['showMeta'] = (bool) $this->attributes['showMeta'];
        $this->attributes['showMap'] = (bool) $this->attributes['showMap'];
        $this->attributes['featuredOnly'] = (bool) $this->attributes['featuredOnly'];
        
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
        
        // Property category filter
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
        $words = explode(' ', $excerpt);
        
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
                <?php if ($this->attributes['showFeatured']): ?>
                    <span class="featured-label"><?php esc_html_e('Featured', 'houzez'); ?></span>
                <?php endif; ?>
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
                    <i class="houzez-icon icon-hotel-double-bed-1"></i>
                    <?php echo esc_html($meta['bedrooms']); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showBathrooms'] && !empty($meta['bathrooms'])): ?>
                <span class="meta-bathrooms">
                    <i class="houzez-icon icon-bathroom-shower-1"></i>
                    <?php echo esc_html($meta['bathrooms']); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showSize'] && !empty($meta['size'])): ?>
                <span class="meta-size">
                    <i class="houzez-icon icon-ruler"></i>
                    <?php echo esc_html($this->format_size($meta['size'])); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->attributes['showGarage'] && !empty($meta['garage'])): ?>
                <span class="meta-garage">
                    <i class="houzez-icon icon-garage"></i>
                    <?php echo esc_html($meta['garage']); ?>
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
                    <i class="houzez-icon icon-pin-1"></i>
                    <?php echo esc_html($meta['location']); ?>
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
                    <span><?php esc_html_e('Agent:', 'houzez'); ?></span>
                    <?php echo esc_html($meta['agent']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->attributes['showStatus'] && !empty($meta['status'])): ?>
                <div class="property-status">
                    <?php echo esc_html(implode(', ', $meta['status'])); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->attributes['showYearBuilt'] && !empty($meta['year_built'])): ?>
                <div class="property-year-built">
                    <span><?php esc_html_e('Year Built:', 'houzez'); ?></span>
                    <?php echo esc_html($meta['year_built']); ?>
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
        return '<p>' . esc_html__('No properties found.', 'houzez') . '</p>';
    }
    
    /**
     * Get CSS classes for the wrapper
     */
    private function get_wrapper_classes() {
        $classes = array(
            'houzez-properties-block',
            'layout-' . $this->attributes['layout'],
            'columns-' . $this->attributes['columns']
        );
        
        return implode(' ', $classes);
    }
    
    /**
     * Main rendering method
     */
    public function render() {
        if (!$this->execute_query()) {
            return $this->render_no_properties();
        }
        
        ob_start();
        ?>
        <section class="<?php echo esc_attr($this->get_wrapper_classes()); ?>" 
                 aria-label="<?php echo esc_attr__('Property listings', 'houzez'); ?>">
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
        </section>
        
        <style>
            .houzez-properties-block.layout-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo esc_attr($this->attributes['columns']); ?>, 1fr);
                gap: 20px;
            }
            .houzez-properties-block.layout-list .property-item {
                display: flex;
                margin-bottom: 20px;
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
            }
        </style>
        <?php
        return ob_get_clean();
    }
}

/**
 * Wrapper function for backward compatibility and block rendering
 */
if (! function_exists('houzez_properties_render_callback')) {
    function houzez_properties_render_callback($attributes, $content) {
        $renderer = new HouzezPropertyRenderer($attributes);
        return $renderer->render();
    }
}

// Register the block render callback
add_action('init', function() {
    register_block_type('create-block/houzez', array(
        'render_callback' => 'houzez_properties_render_callback'
    ));
});