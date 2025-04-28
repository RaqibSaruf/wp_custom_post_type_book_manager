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
}
