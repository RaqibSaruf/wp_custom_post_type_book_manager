<?php

declare(strict_types=1);

namespace Saruf\WpCptBookManager\Init;

use Saruf\WpCptBookManager\Helpers\Template;

class AuthorTaxonomy
{
    public function __construct()
    {
        $this->init_hooks();
    }

    public function init_hooks()
    {
        add_action('init', [$this, 'register_author_taxonomy']);
        add_action('save_post', [$this, 'save_author_taxonomy']);
    }

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
                'add_new_item' => 'Add New Author',
            ],
            'public' => true,
            'hierarchical' => false,
            'rewrite' => ['slug' => 'author'],
            'meta_box_cb' => [$this, 'author_taxonomy_select'],
        ];
        register_taxonomy(WP_CPT_NAME . '_author', WP_CPT_NAME, $args);
    }

    public function author_taxonomy_select($post)
    {
        $taxonomy = WP_CPT_NAME . '_author';
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
            'label' => 'Author'
        ]);
    }

    public function save_author_taxonomy($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $taxonomy = WP_CPT_NAME . '_author';
        $new_author_taxnonomy = 'new_' . $taxonomy ;

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
