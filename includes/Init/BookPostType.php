<?php
declare(strict_types=1);

namespace Saruf\WpCptBookManager\Init;

class BookPostType {
    public function __construct()
    {
        $this->init_hooks();
    }

    public function init_hooks() {
        add_action('init', [$this, 'register_book_post_type']);
    }

    public function register_book_post_type()
    {
        $args = [
            'labels' => [
                'name' => 'Books',
                'singular_name' => 'Book',
                'add_new_item' => 'Add New Book',
                'edit_item' => 'Edit Book',
                'new_item' => 'New Book',
                'view_item' => 'View Book',
                'all_items' => 'All Books',
                'search_items' => 'Search Books',
                'not_found' => 'No books found',
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-book',
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'taxonomies' => ['author', 'genre'],
        ];
        register_post_type(WP_CPT_NAME, $args);
    }
}