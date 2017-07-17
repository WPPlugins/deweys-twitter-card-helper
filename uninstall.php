<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WADTwitterCards
 * @author    Luke DeWitt <dewey@whatadewitt.com>
 * @license   GPL-2.0+
 * @link      http://www.whatadewitt.ca
 * @copyright 2014 Luke DeWitt
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
