<?php

declare(strict_types=1);

namespace Saruf\WpCptBookManager;

class PostTypeHandler
{
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        // Register custom post type and taxonomies
        add_action('init', [$this, 'register_book_post_type']);
        add_action('init', [$this, 'register_author_taxonomy']);
        add_action('init', [$this, 'register_genre_taxonomy']);

        // Add custom meta boxes
        add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);

        // Save custom fields data
        add_action('save_post', [$this, 'save_book_details']);

        // Customize the admin columns
        add_filter('manage_book_posts_columns', [$this, 'admin_books_columns']);
        add_action('manage_book_posts_custom_column', [$this, 'admin_books_custom_column'], 10, 2);

        // Filter books in the admin list
        add_action('pre_get_posts', [$this, 'filter_books_by_author_genre']);
    }

    // Register Custom Post Type for Books
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
            'taxonomies' => ['author', 'genre'], // Associating with custom taxonomies
        ];
        register_post_type('book', $args);
    }

    // Register Custom Taxonomy for Authors
    public function register_author_taxonomy()
    {
        $args = [
            'labels' => [
                'name' => 'Authors',
                'singular_name' => 'Author',
                'search_items' => 'Search Authors',
                'all_items' => 'All Authors',
                'edit_item' => 'Edit Author',
                'update_item' => 'Update Author',
            ],
            'public' => true,
            'hierarchical' => false,
        ];
        register_taxonomy('author', 'book', $args);
    }

    // Register Custom Taxonomy for Genres
    public function register_genre_taxonomy()
    {
        $args = [
            'labels' => [
                'name' => 'Genres',
                'singular_name' => 'Genre',
                'search_items' => 'Search Genres',
                'all_items' => 'All Genres',
                'edit_item' => 'Edit Genre',
                'update_item' => 'Update Genre',
            ],
            'public' => true,
            'hierarchical' => true,
        ];
        register_taxonomy('genre', 'book', $args);
    }

    // Add Meta Boxes for Custom Fields
    public function add_custom_meta_boxes()
    {
        add_meta_box('wpbm_book_details', 'Book Details', [$this, 'book_details_callback'], 'book', 'normal', 'high');
    }

    // Meta Box callback function
    public function book_details_callback($post)
    {
        // Add a nonce field to check for security later
        wp_nonce_field('wpbm_save_book_details', 'wpbm_book_details_nonce');

        // Retrieve current values
        $rating = get_post_meta($post->ID, '_wpbm_rating', true);
        $published_date = get_post_meta($post->ID, '_wpbm_published_date', true);

        // Output the fields
?>
        <p>
            <label for="wpbm_rating">Rating</label><br>
            <input type="number" id="wpbm_rating" name="wpbm_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5">
        </p>
        <p>
            <label for="wpbm_published_date">Published Date</label><br>
            <input type="date" id="wpbm_published_date" name="wpbm_published_date" value="<?php echo esc_attr($published_date); ?>">
        </p>
<?php
    }

    // Save the custom fields data
    public function save_book_details($post_id)
    {
        // Check if nonce is set and valid
        if (!isset($_POST['wpbm_book_details_nonce']) || !wp_verify_nonce($_POST['wpbm_book_details_nonce'], 'wpbm_save_book_details')) {
            return;
        }

        // Save custom fields
        if (isset($_POST['wpbm_rating'])) {
            update_post_meta($post_id, '_wpbm_rating', sanitize_text_field($_POST['wpbm_rating']));
        }
        if (isset($_POST['wpbm_published_date'])) {
            update_post_meta($post_id, '_wpbm_published_date', sanitize_text_field($_POST['wpbm_published_date']));
        }
    }

    // Admin Book Listing with Pagination (Add new columns)
    public function admin_books_columns($columns)
    {
        $columns['rating'] = 'Rating';
        $columns['published_date'] = 'Published Date';
        return $columns;
    }

    // Custom column content for admin
    public function admin_books_custom_column($column, $post_id)
    {
        if ('rating' === $column) {
            echo get_post_meta($post_id, '_wpbm_rating', true);
        }
        if ('published_date' === $column) {
            echo get_post_meta($post_id, '_wpbm_published_date', true);
        }
    }

    // Filter Books by Author, Genre, Name, Rating
    public function filter_books_by_author_genre($query)
    {
        if (!is_admin() || !isset($_GET['post_type']) || $_GET['post_type'] !== 'book') {
            return;
        }

        // Author filter
        if (!empty($_GET['author'])) {
            $query->set('tax_query', [
                [
                    'taxonomy' => 'author',
                    'field' => 'id',
                    'terms' => $_GET['author'],
                    'operator' => 'IN',
                ],
            ]);
        }

        // Genre filter
        if (!empty($_GET['genre'])) {
            $query->set('tax_query', [
                [
                    'taxonomy' => 'genre',
                    'field' => 'id',
                    'terms' => $_GET['genre'],
                    'operator' => 'IN',
                ],
            ]);
        }

        // Rating filter
        if (!empty($_GET['rating'])) {
            $query->set('meta_query', [
                [
                    'key' => '_wpbm_rating',
                    'value' => $_GET['rating'],
                    'compare' => '=',
                ],
            ]);
        }
    }
}
