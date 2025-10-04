<?php

/**
 * Server Side Renderer for Events/Properties
 * OOP implementation with proper encapsulation and separation of concerns
 */
class EventRenderer {
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
            'author' => true,
            'showComments' => true,
            'showDate' => true,
            'showTaxonomies' => true,
            'imageSize' => 'medium_large',
        );
    }
    
    /**
     * Sanitize and validate attributes
     */
    private function sanitize_attributes() {
        $this->attributes = wp_parse_args((array)$this->attributes, $this->defaults);
        
        $this->attributes['postsToShow'] = max(1, (int)$this->attributes['postsToShow']);
        $this->attributes['order'] = in_array($this->attributes['order'], array('ASC', 'DESC'), true) ? $this->attributes['order'] : 'DESC';
        $this->attributes['author'] = (bool) $this->attributes['author'];
        $this->attributes['showComments'] = (bool) $this->attributes['showComments'];
        $this->attributes['showDate'] = (bool) $this->attributes['showDate'];
        $this->attributes['showTaxonomies'] = (bool) $this->attributes['showTaxonomies'];
        $this->attributes['imageSize'] = in_array($this->attributes['imageSize'], array('thumbnail', 'medium', 'medium_large', 'large', 'full'), true) ? $this->attributes['imageSize'] : 'medium_large';
    }
    
    /**
     * Execute the query
     */
    private function execute_query() {
        $this->query = new WP_Query(array(
            'post_type'      => 'property',
            'posts_per_page' => $this->attributes['postsToShow'],
            'post_status'    => 'publish',
            'order'          => $this->attributes['order'],
            'tax_query'      => array(
                array(
                    'taxonomy' => 'property_category',
                    'field'    => 'slug',
                    'terms'    => array('music'),
                ),
            ),
        ));
        
        return $this->query->have_posts();
    }
    
    /**
     * Render individual post item
     */
    private function render_post_item($post_id, $permalink, $title) {
        ?>
        <article>
            <h3 class="lp-title">
                <a class="lp-title-link" href="<?php echo esc_url($permalink); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>
        </article>
        <?php
    }
    
    /**
     * Render the no posts message
     */
    private function render_no_posts() {
        return '<p>' . esc_html__('No posts available.', 'events') . '</p>';
    }
    
    /**
     * Main rendering method
     */
    public function render() {
        if (!$this->execute_query()) {
            return $this->render_no_posts();
        }
        
        ob_start();
        ?>
        <section class="latest-events-section" aria-label="<?php echo esc_attr__('Latest events', 'events'); ?>">
            <?php
            $index = 0;
            while ($this->query->have_posts()):
                $this->query->the_post();
                $post_id = get_the_ID();
                $permalink = get_permalink($post_id);
                $title = get_the_title($post_id);
                $this->render_post_item($post_id, $permalink, $title);
                $index++;
            endwhile;
            
            wp_reset_postdata();
            ?>
        </section>
        <?php
        return ob_get_clean();
    }
}

/**
 * Optional: Keep the wrapper function for backward compatibility
 */
if (! function_exists('server_side_rendering')) {
    function server_side_rendering($attributes, $content) {
        $renderer = new EventRenderer($attributes);
        return $renderer->render();
    }
}

// Usage example:
$renderer = new EventRenderer([]);
echo $renderer->render();

// Or using the wrapper function:
// echo server_side_rendering([], '');