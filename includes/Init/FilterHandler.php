<?php

declare(strict_types=1);

namespace Saruf\WpCptBookManager\Init;

class FilterHandler
{
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        add_action('pre_get_posts', [$this, 'filter_books_by_author_genre_rating']);
        add_action('restrict_manage_posts', [$this, 'add_filter_fields']);
    }

    public function filter_books_by_author_genre_rating($query)
    {
        if (!is_admin() || !isset($_GET['post_type']) || $_GET['post_type'] !== WP_CPT_NAME) {
            return;
        }

        if (!empty($_GET[WP_CPT_NAME . '_author'])) {
            $query->set('tax_query', [
                [
                    'taxonomy' => WP_CPT_NAME . '_author',
                    'field' => 'id',
                    'terms' => $_GET[WP_CPT_NAME . '_author'],
                    'operator' => 'IN',
                ],
            ]);
        }

        if (!empty($_GET[WP_CPT_NAME . '_genre'])) {
            $query->set('tax_query', [
                [
                    'taxonomy' => WP_CPT_NAME . '_genre',
                    'field' => 'id',
                    'terms' => $_GET[WP_CPT_NAME . '_genre'],
                    'operator' => 'IN',
                ],
            ]);
        }

        if (!empty($_GET[WP_CPT_NAME . '_rating'])) {
            $query->set('meta_query', [
                [
                    'key' => WP_CPT_NAME . '_rating',
                    'value' => $_GET[WP_CPT_NAME . '_rating'],
                    'compare' => '>=',
                ],
            ]);
        }
    }

    public function add_filter_fields()
    {
        global $typenow;

        if ($typenow !== WP_CPT_NAME) {
            return;
        }

        $author_taxonomy = WP_CPT_NAME . '_author';
        $genre_taxonomy = WP_CPT_NAME . '_genre';

        $selected_author = $_GET[$author_taxonomy] ?? '';
        $selected_genre = $_GET[$genre_taxonomy] ?? '';
        $selected_rating = $_GET[WP_CPT_NAME . '_rating'] ?? '';

        wp_dropdown_categories([
            'show_option_all' => __('All Authors', 'textdomain'),
            'taxonomy'        => $author_taxonomy,
            'name'            => $author_taxonomy,
            'orderby'         => 'name',
            'selected'        => $selected_author,
            'hierarchical'    => true,
            'depth'           => 1,
            'show_count'      => true,
            'hide_empty'      => false,
        ]);

        wp_dropdown_categories([
            'show_option_all' => __('All Genres', 'textdomain'),
            'taxonomy'        => $genre_taxonomy,
            'name'            => $genre_taxonomy,
            'orderby'         => 'name',
            'selected'        => $selected_genre,
            'hierarchical'    => true,
            'depth'           => 1,
            'show_count'      => true,
            'hide_empty'      => false,
        ]);

        echo '<input type="number" 
            name="' . esc_attr(WP_CPT_NAME . '_rating') . '" 
            value="' . esc_attr($selected_rating) . '" 
            placeholder="Min Rating" 
            style="width: 120px; margin-left: 10px;" />';
    }
}
