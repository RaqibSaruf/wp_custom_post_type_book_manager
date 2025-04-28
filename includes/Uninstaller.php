<?php

declare(strict_types=1);

namespace Saruf\WpCptBookManager;

/**
 * Plugin Uninstaller class.
 */
final class Uninstaller
{

    /**
     * Runs the uninstaller.
     * @return void
     */
    public function run(): void
    {
        $this->remove_plugin_info();
    }

    /**
     * Removes plugin info.
     * @return void
     */
    private function remove_plugin_info(): void
    {
        $activated = get_option('book_manager_installation_time');

        if ($activated) {
            delete_option('book_manager_installation_time');
            delete_option('book_manager_version');
        }

    }

}
