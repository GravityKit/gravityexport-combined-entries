<?php

/**
 * Plugin Name:        GravityExport Combined Entries
 * Description:        Adds ability to merge entries of multiple forms into one result set with GravityExport.
 * Version:            0.1.1
 * Author:             GravityKit
 * Author URI:         https://www.gravitykit.com
 * License:            GPLv2 or later
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.html
 */

add_action('plugins_loaded', function () {
    if (class_exists(\GFAPI::class)) {
        require_once 'class-combined-entries.php';

        GravityExport_CombinedEntries::get_instance();
    }
}, 20);
