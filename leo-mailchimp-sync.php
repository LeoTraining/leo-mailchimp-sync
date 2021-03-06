<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @see              natehobi.com
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Leo Mailchimp Sync
 * Plugin URI:        https://github.com/LeoTraining/leo-mailchimp-sync
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Nate Hobi
 * Author URI:        natehobi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leo-mailchimp-sync
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('PLUGIN_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-leo-mailchimp-sync-activator.php.
 */
function activate_leo_mailchimp_sync()
{
    require_once plugin_dir_path(__FILE__).'includes/class-leo-mailchimp-sync-activator.php';
    Leo_Mailchimp_Sync_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-leo-mailchimp-sync-deactivator.php.
 */
function deactivate_leo_mailchimp_sync()
{
    require_once plugin_dir_path(__FILE__).'includes/class-leo-mailchimp-sync-deactivator.php';
    Leo_Mailchimp_Sync_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_leo_mailchimp_sync');
register_deactivation_hook(__FILE__, 'deactivate_leo_mailchimp_sync');

require plugin_dir_path(__FILE__).'includes/class-mailchimp-job.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__).'includes/class-leo-mailchimp-sync.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_leo_mailchimp_sync()
{
    $plugin = new Leo_Mailchimp_Sync();
    $plugin->run();
}

require __DIR__.'/vendor/autoload.php';

run_leo_mailchimp_sync();
