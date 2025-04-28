<?php
declare(strict_types=1);

namespace Saruf\WpCptBookManager\Init;

use Saruf\WpCptBookManager\Helpers\Template;

class GenreTaxonomy {
    public function __construct()
    {
        $this->init_hooks();
    }

    public function init_hooks(){
        add_action('init', [$this, 'register_genre_taxonomy']);
        add_action('save_post', [$this, 'save_genre_taxonomy']);
    }

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
                'add_new_item' => 'Add New Genre',
            ],
            'public' => true,
            'hierarchical' => false,
            'rewrite' => ['slug' => 'genre'],
            'meta_box_cb' => [$this, 'genre_taxonomy_select'],
        ];
        register_taxonomy(WP_CPT_NAME . '_genre', WP_CPT_NAME, $args);
    }

    public function genre_taxonomy_select($post)
    {
        $taxonomy = WP_CPT_NAME . '_genre';
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'orderby' => 'name',
            'hide_empty' => false,
        ]);

        $selected_term = wp_get_object_terms($post->ID, $taxonomy);
        $selected_term_id = !empty($selected_term) ? $selected_term[0]->term_id : '';

        echo Template::render('Init/Views/select-taxonomy.php', [
            'terms' => $terms,
            'selected_term_id' => $selected_term_id,
            'taxonomy_name' => $taxonomy,
            'label' => 'Genre'
        ]);
    }

    public function save_genre_taxonomy($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $taxonomy = WP_CPT_NAME . '_genre';
        $new_author_taxnonomy = 'new_' . $taxonomy;

        if (!isset($_POST[$taxonomy])) {
            return;
        }

        if ($_POST[$taxonomy] === 'add_new' && !empty($_POST[$new_author_taxnonomy])) {
            $new_author_name = sanitize_text_field($_POST[$new_author_taxnonomy]);

            $existing_term = term_exists($new_author_name, $taxonomy);

            if ($existing_term === 0 || $existing_term === null) {
                $new_term = wp_insert_term($new_author_name, $taxonomy);

                if (!is_wp_error($new_term) && isset($new_term['term_id'])) {
                    wp_set_object_terms($post_id, [$new_term['term_id']], $taxonomy);
                }
            } else {
                $term_id = is_array($existing_term) ? $existing_term['term_id'] : $existing_term;
                wp_set_object_terms($post_id, [$term_id], $taxonomy);
            }
        } elseif (is_numeric($_POST[$taxonomy])) {
            $author_id = (int) $_POST[$taxonomy];
            wp_set_object_terms($post_id, [$author_id], $taxonomy);
        }
    }
}