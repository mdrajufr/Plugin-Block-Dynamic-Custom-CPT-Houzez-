<?php
/**
 * Server-Side Renderer for Houzez Gutenberg Blocks
 * 
 * @package Houzez
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Server-Side Renderer for Gutenberg Blocks
 */
class HouzezBlockRenderer {
    
    /**
     * Render properties block with SSR
     */
    public static function render_properties_block($attributes, $content, $block) {
        $attributes = self::sanitize_block_attributes($attributes);
        
        // Check if properties post type exists
        if (!post_type_exists('property')) {
            return self::render_error_message(__('Properties post type is not registered.', 'houzez'));
        }
        
        $query_args = self::build_query_args($attributes);
        $properties_query = new WP_Query($query_args);
        
        if (!$properties_query->have_posts()) {
            wp_reset_postdata();
            return self::render_no_properties();
        }
        
        ob_start();
        self::render_properties_grid($properties_query, $attributes);
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Sanitize and validate block attributes
     */
    private static function sanitize_block_attributes($attributes) {
        $defaults = array(
            'postsToShow' => HouzezConstants::DEFAULT_POSTS_TO_SHOW,
            'order' => HouzezConstants::DEFAULT_ORDER,
            'orderBy' => HouzezConstants::DEFAULT_ORDER_BY,
            'layout' => HouzezConstants::DEFAULT_LAYOUT,
            'columns' => HouzezConstants::DEFAULT_COLUMNS,
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
            'showExcerpt' => true,
            'excerptLength' => HouzezConstants::DEFAULT_EXCERPT_LENGTH,
            'showMeta' => true,
            'imageSize' => HouzezConstants::DEFAULT_IMAGE_SIZE,
            'pricePrefix' => '$',
            'sizeSuffix' => 'sq ft',
            'categoryFilter' => '',
            'statusFilter' => '',
            'featuredOnly' => false
        );
        
        $attributes = wp_parse_args((array)$attributes, $defaults);
        
        // Sanitize numeric values with bounds
        $attributes['postsToShow'] = max(1, min(HouzezConstants::MAX_POSTS_TO_SHOW, (int)$attributes['postsToShow']));
        $attributes['columns'] = max(1, min(HouzezConstants::MAX_COLUMNS, (int)$attributes['columns']));
        $attributes['excerptLength'] = max(1, min(HouzezConstants::MAX_EXCERPT_LENGTH, (int)$attributes['excerptLength']));
        
        // Validate enums
        $attributes['order'] = in_array($attributes['order'], array('ASC', 'DESC'), true) ? $attributes['order'] : 'DESC';
        $attributes['orderBy'] = in_array($attributes['orderBy'], array('date', 'modified', 'title', 'price', 'size'), true) ? $attributes['orderBy'] : 'date';
        $attributes['layout'] = in_array($attributes['layout'], array('grid', 'list', 'masonry', 'carousel'), true) ? $attributes['layout'] : 'grid';
        
        // Validate image sizes
        $valid_image_sizes = array(
            HouzezConstants::IMAGE_SIZE_THUMBNAIL,
            HouzezConstants::IMAGE_SIZE_MEDIUM,
            HouzezConstants::IMAGE_SIZE_MEDIUM_LARGE,
            HouzezConstants::IMAGE_SIZE_LARGE,
            HouzezConstants::IMAGE_SIZE_FULL
        );
        $attributes['imageSize'] = in_array($attributes['imageSize'], $valid_image_sizes, true) ? $attributes['imageSize'] : HouzezConstants::DEFAULT_IMAGE_SIZE;
        
        // Sanitize strings
        $attributes['pricePrefix'] = sanitize_text_field($attributes['pricePrefix']);
        $attributes['sizeSuffix'] = sanitize_text_field($attributes['sizeSuffix']);
        $attributes['categoryFilter'] = sanitize_text_field($attributes['categoryFilter']);
        $attributes['statusFilter'] = sanitize_text_field($attributes['statusFilter']);
        
        return $attributes;
    }
    
    /**
     * Build query arguments for properties
     */
    private static function build_query_args($attributes) {
        $query_args = array(
            'post_type' => 'property',
            'posts_per_page' => $attributes['postsToShow'],
            'post_status' => 'publish',
            'order' => $attributes['order'],
            'orderby' => $attributes['orderBy'],
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        );
        
        // Add meta query for featured properties if needed
        if ($attributes['featuredOnly']) {
            $query_args['meta_query'] = array(
                array(
                    'key' => 'fave_featured',
                    'value' => '1',
                    'compare' => '='
                )
            );
        }
        
        // Add taxonomy filters
        $tax_query = array();
        
        if (!empty($attributes['categoryFilter'])) {
            $tax_query[] = array(
                'taxonomy' => 'property_type',
                'field' => 'slug',
                'terms' => $attributes['categoryFilter']
            );
        }
        
        if (!empty($attributes['statusFilter'])) {
            $tax_query[] = array(
                'taxonomy' => 'property_status',
                'field' => 'slug',
                'terms' => $attributes['statusFilter']
            );
        }
        
        if (!empty($tax_query)) {
            $tax_query['relation'] = 'AND';
            $query_args['tax_query'] = $tax_query;
        }
        
        return apply_filters('houzez_properties_query_args', $query_args, $attributes);
    }
    
    /**
     * Render properties grid
     */
    private static function render_properties_grid($query, $attributes) {
        $wrapper_classes = self::get_wrapper_classes($attributes);
        $grid_styles = self::get_grid_styles($attributes);
        ?>
        <div class="<?php echo esc_attr($wrapper_classes); ?>" <?php echo $grid_styles; ?>>
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <?php self::render_property_item(get_the_ID(), $attributes); ?>
            <?php endwhile; ?>
        </div>
        <?php
    }
    
    /**
     * Get wrapper CSS classes
     */
    private static function get_wrapper_classes($attributes) {
        $classes = array(
            'houzez-properties-block',
            'layout-' . esc_attr($attributes['layout']),
            'columns-' . esc_attr($attributes['columns'])
        );
        
        return implode(' ', $classes);
    }
    
    /**
     * Get grid styles for CSS grid
     */
    private static function get_grid_styles($attributes) {
        if ($attributes['layout'] === 'grid') {
            return 'style="grid-template-columns: repeat(' . esc_attr($attributes['columns']) . ', 1fr);"';
        }
        return '';
    }
    
    /**
     * Render individual property item
     */
    private static function render_property_item($post_id, $attributes) {
        $permalink = get_permalink($post_id);
        $title = get_the_title($post_id);
        $meta = self::get_property_meta($post_id);
        ?>
        <article class="property-item <?php echo esc_attr($attributes['layout']); ?>-item">
            <?php self::render_property_image($post_id, $permalink, $attributes); ?>
            <?php self::render_property_details($post_id, $permalink, $title, $meta, $attributes); ?>
        </article>
        <?php
    }
    
    /**
     * Render property image
     */
    private static function render_property_image($post_id, $permalink, $attributes) {
        if (has_post_thumbnail($post_id)) {
            $image = get_the_post_thumbnail($post_id, $attributes['imageSize'], array(
                'class' => 'property-image',
                'alt' => esc_attr(get_the_title($post_id)),
                'loading' => 'lazy'
            ));
            ?>
            <div class="property-image-wrap">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo $image; ?>
                </a>
                <?php if ($attributes['showFeatured'] && get_post_meta($post_id, 'fave_featured', true)): ?>
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
     * Render property details
     */
    private static function render_property_details($post_id, $permalink, $title, $meta, $attributes) {
        ?>
        <div class="property-details">
            <h3 class="property-title">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>
            
            <?php if ($attributes['showLocation'] && !empty($meta['location'])): ?>
                <div class="property-location">
                    📍 <?php echo esc_html($meta['location']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($attributes['showPrice'] && !empty($meta['price'])): ?>
                <div class="property-price">
                    <?php echo esc_html(self::format_price($meta['price'], $attributes['pricePrefix'])); ?>
                </div>
            <?php endif; ?>
            
            <?php self::render_property_meta($meta, $attributes); ?>
            
            <?php if ($attributes['showExcerpt']): ?>
                <div class="property-excerpt">
                    <?php echo wp_kses_post(self::get_property_excerpt($post_id, $attributes['excerptLength'])); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($attributes['showAgent'] && !empty($meta['agent'])): ?>
                <div class="property-agent">
                    <strong><?php esc_html_e('Agent:', 'houzez'); ?></strong> <?php echo esc_html($meta['agent']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($attributes['showStatus'] && !empty($meta['status'])): ?>
                <div class="property-status">
                    <strong><?php esc_html_e('Status:', 'houzez'); ?></strong> <?php echo esc_html(implode(', ', $meta['status'])); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($attributes['showYearBuilt'] && !empty($meta['year_built'])): ?>
                <div class="property-year-built">
                    <strong><?php esc_html_e('Year Built:', 'houzez'); ?></strong> <?php echo esc_html($meta['year_built']); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render property meta information
     */
    private static function render_property_meta($meta, $attributes) {
        if (!$attributes['showMeta']) return;
        ?>
        <div class="property-meta">
            <?php if ($attributes['showBedrooms'] && !empty($meta['bedrooms'])): ?>
                <span class="meta-bedrooms">
                    🛏️ <?php echo esc_html($meta['bedrooms']); ?> <?php esc_html_e('Beds', 'houzez'); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($attributes['showBathrooms'] && !empty($meta['bathrooms'])): ?>
                <span class="meta-bathrooms">
                    🚿 <?php echo esc_html($meta['bathrooms']); ?> <?php esc_html_e('Baths', 'houzez'); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($attributes['showSize'] && !empty($meta['size'])): ?>
                <span class="meta-size">
                    📐 <?php echo esc_html(self::format_size($meta['size'], $attributes['sizeSuffix'])); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($attributes['showGarage'] && !empty($meta['garage'])): ?>
                <span class="meta-garage">
                    🚗 <?php echo esc_html($meta['garage']); ?> <?php esc_html_e('Garage', 'houzez'); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get property meta data
     */
    private static function get_property_meta($post_id) {
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
    private static function format_price($price, $prefix) {
        if (empty($price)) return '';
        return $prefix . number_format(floatval($price));
    }
    
    /**
     * Format property size
     */
    private static function format_size($size, $suffix) {
        if (empty($size)) return '';
        return number_format(floatval($size)) . ' ' . $suffix;
    }
    
    /**
     * Get property excerpt
     */
    private static function get_property_excerpt($post_id, $excerpt_length) {
        $excerpt = get_the_excerpt($post_id);
        if (empty($excerpt)) {
            $excerpt = get_the_content(null, false, $post_id);
        }
        
        $words = explode(' ', wp_strip_all_tags($excerpt));
        
        if (count($words) > $excerpt_length) {
            $words = array_slice($words, 0, $excerpt_length);
            $excerpt = implode(' ', $words) . '...';
        }
        
        return $excerpt;
    }
    
    /**
     * Render no properties message
     */
    private static function render_no_properties() {
        return '<div class="houzez-no-properties"><p>' . esc_html__('No properties found.', 'houzez') . '</p></div>';
    }
    
    /**
     * Render error message
     */
    private static function render_error_message($message) {
        return '<div class="houzez-error"><p>' . esc_html($message) . '</p></div>';
    }
}

