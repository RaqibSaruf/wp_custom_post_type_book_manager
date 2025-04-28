<?php

declare(strict_types=1);

namespace Saruf\WpCptBookManager\Init;

use Saruf\WpCptBookManager\Helpers\Template;

class OtherFields
{

    private $meta_box_id;
    private $other_field_nonce;
    private $other_nonce_field_id;
    private $post_meta_rating_key;
    private $post_meta_published_date_key;

    public function __construct()
    {
        $this->meta_box_id = WP_CPT_NAME . '_other_fields';
        $this->other_field_nonce = WP_CPT_NAME . '_other_fields_nonce';
        $this->other_nonce_field_id = WP_CPT_NAME . '_save_other_fields';
        $this->post_meta_rating_key = WP_CPT_NAME . '_rating';
        $this->post_meta_published_date_key = WP_CPT_NAME . '_published_date';
        $this->init_hooks();
    }

    public function init_hooks()
    {
        add_action('add_meta_boxes', [$this, 'add_other_fields']);
        add_action('save_post', [$this, 'save_book_other_fields']);

        add_filter("manage_". WP_CPT_NAME."_posts_columns", [$this, 'add_other_field_columns']);
        add_action("manage_". WP_CPT_NAME."_posts_custom_column", [$this, 'get_other_field_columns'], 10, 2);
    }

    public function add_other_fields()
    {
        add_meta_box($this->meta_box_id, 'Other info', [$this, 'other_fields_callback'], WP_CPT_NAME, 'normal', 'high');
    }

    public function other_fields_callback($post)
    {
        wp_nonce_field($this->other_nonce_field_id, $this->other_field_nonce);

        $rating = get_post_meta($post->ID, $this->post_meta_rating_key, true);
        $published_date = get_post_meta($post->ID, $this->post_meta_published_date_key, true);

        echo Template::render('Init/Views/other-fields.php', compact('rating', 'published_date'));
    }

    public function save_book_other_fields($post_id)
    {
        if (!isset($_POST[$this->other_field_nonce]) || !wp_verify_nonce($_POST[$this->other_field_nonce], $this->other_nonce_field_id)) {
            return;
        }

        if (isset($_POST['rating'])) {
            update_post_meta($post_id, $this->post_meta_rating_key, sanitize_text_field($_POST['rating']));
        }
        if (isset($_POST['published_date'])) {
            update_post_meta($post_id, $this->post_meta_published_date_key, sanitize_text_field($_POST['published_date']));
        }
    }

    public function add_other_field_columns($columns) {
        $columns['rating'] = 'Rating';
        $columns['published_date'] = 'Published Date';
        return $columns;
    }

    public function get_other_field_columns($column, $post_id) {
        if ('rating' === $column) {
            echo get_post_meta($post_id, $this->post_meta_rating_key, true);
        }
        if ('published_date' === $column) {
            echo get_post_meta($post_id, $this->post_meta_published_date_key, true);
        }
    }
}
