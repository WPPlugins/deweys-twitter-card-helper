<?php
/**
 * @package   WADTwitterCards
 * @author    Luke DeWitt <dewey@whatadewitt.com>
 * @license   GPL-2.0+
 * @link      http://www.whatadewitt.ca
 * @copyright 2014 Luke DeWitt
 *
 * @wordpress-plugin
 * Plugin Name: Dewey's Twitter Card Helper
 * Plugin URI:  http://www.whatadewitt.ca
 * Description: Simplifies the use of Twitter Cards in WP
 * Version:     2.0.5
 * Author:      Luke DeWitt
 * Author URI:  http://www.whatadewitt.ca
 * Text Domain: wad_twitter_cards
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-wad_twitter_cards.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'wad_twitter_cards', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'wad_twitter_cards', 'deactivate' ) );

WADTwitterCards::get_instance();
