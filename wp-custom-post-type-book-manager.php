<?php

declare(strict_types=1);

/*
 * Plugin Name: Custom Post Type Book Manager
 * Description: This is a simple custom post type book manager plugin for wordpress.
 * Version: 1.0
 * Author: Saruf <raqibul.dev@gmail.com>
 * 
 */

if (! defined('ABSPATH')) {
    exit;
}


define('WP_CPT_BOOK_MANAGER_VERSION', '1.0');
define('WP_CPT_BOOK_MANAGER_FILE', __FILE__);
define('WP_CPT_BOOK_MANAGER_DIR', __DIR__);
define('WP_CPT_BOOK_MANAGER_INCLUDES', WP_CPT_BOOK_MANAGER_DIR . '/includes');
define('WP_CPT_BOOK_MANAGER_URL', plugin_dir_url(WP_CPT_BOOK_MANAGER_FILE));
define('WP_CPT_NAME', 'wp_cpt_book');

/**
 * Register the "book" custom post type
 */

require_once WP_CPT_BOOK_MANAGER_DIR . '/vendor/autoload.php';

use Saruf\WpCptBookManager\BookManager;



function wp_cpt_book_manager_init()
{
    BookManager::getInstance();
}

wp_cpt_book_manager_init();
