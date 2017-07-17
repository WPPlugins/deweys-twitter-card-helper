<?php
/**
 * Dewey's Twitter Card Helper
 *
 * @package   WADTwitterCards
 * @author    Luke DeWitt <dewey@whatadewitt.com>
 * @license   GPL-2.0+
 * @link      http://www.whatadewitt.ca
 * @copyright 2014 Luke DeWitt
 */

/**
 * Plugin class.
 *
 * @package WADTwitterCards
 * @author  Luke DeWitt <dewey@whatadewitt.com>
 */
class WADTwitterCards {

  /**
   * Plugin version, used for cache-busting of style and script file references.
   *
   * @since   1.0.0
   *
   * @var     string
   */
  protected $version = '2.0.5';

  /**
   * Unique identifier for your plugin.
   *
   * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
   * match the Text Domain file header in the main plugin file.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $plugin_slug = 'wad_twitter_cards';

  /**
   * Instance of this class.
   *
   * @since    1.0.0
   *
   * @var      object
   */
  protected static $instance = null;

  /**
   * Slug of the plugin screen.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $plugin_screen_hook_suffix = null;

  /**
   * Initialize the plugin by setting localization, filters, and administration functions.
   *
   * @since     1.0.0
   */
  private function __construct() {

    // Load plugin text domain
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    // Twitter Cards
    add_action( 'wp_head', array( $this, 'generate_twitter_card' ) );

    // add og meta box
    add_action( 'add_meta_boxes', array( $this, 'add_twitter_meta_box' ) );

    // save og meta
    add_action( 'save_post', array( $this, 'save_twitter_data' ) );
  }

  /**
   * Return an instance of this class.
   *
   * @since     1.0.0
   *
   * @return    object    A single instance of this class.
   */
  public static function get_instance() {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
   * Load the plugin text domain for translation.
   *
   * @since    1.0.0
   */
  public function load_plugin_textdomain() {

    $domain = $this->plugin_slug;
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
  }

  /**
   * Print the twitter: meta tags to the page head
   *
   * @since    1.0.0
   */
  public function generate_twitter_card() {
    global $post;
    $tags = array();

    if ( is_singular() ) {
      if (post_type_supports( $post->post_type, 'twitter_cards' ) ) {
        // excerpt
        $excerpt = get_the_excerpt();
        if ( '' == $excerpt ) {
          $excerpt = strip_tags($post->post_content);
          $excerpt = strip_shortcodes($excerpt);
          $excerpt = str_replace(array("\n", "\r", "\t"), ' ', $excerpt);
          $excerpt = substr($excerpt, 0, 100);
          $excerpt = $excerpt.'...';
        }

        //Define defaults
        $tags['card'] = 'summary';
        $tags['site'] = '@undefined'; // YO! This should definitely be updated with the twitter_cards filter
        $tags['title'] = get_the_title();
        $tags['url'] = get_permalink();
        $tags['description'] = $excerpt;

        if ( $tc_title = get_post_meta($post->ID, 'twitter_title', true) ) {
          $tags['title'] = $tc_title;
        }

        if ( $tc_desc = get_post_meta($post->ID, 'twitter_desc', true) ) {
          $tags['description'] = $tc_desc;
        }

        if ( $tc_type = get_post_meta($post->ID, 'twitter_type', true) ) {
          $tags['card'] = $tc_type;
        }

        //Check for a post thumbnail.
        if ( current_theme_supports('post-thumbnails') && has_post_thumbnail( $post->ID ) ) {
          $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium', false);
          $tags['image'] = $thumbnail[0];
        }

        $tags = apply_filters( 'twitter_cards', $tags );
        $tags = apply_filters( "{$post->post_type}_twitter_cards", $tags );
      }
    } else if ( is_front_page() ) {
      $tags['card'] = get_bloginfo( 'name' );
      $tags['site'] = get_bloginfo( 'name' );
      $tags['creator'] = 'website';
      $tags['url'] = get_bloginfo( 'url' );
      $tags['description'] = get_bloginfo( 'description' );

      $tags = apply_filters( 'twitter_cards', $tags );
      $tags = apply_filters( "front_page_twitter_cards", $tags );
    }

    //filter post tags
    foreach ( $tags as $key => $value ) {
      echo("<meta name=\"twitter:" . $key . "\" content=\"" . htmlspecialchars($value, ENT_COMPAT, 'UTF-8', false) . "\" />\n");
    }
  }

  /**
   * Add the twitter card overrides meta box to the page
   *
   * @since    2.0.0
   */
  public function add_twitter_meta_box($post_type) {
    if ( post_type_supports($post_type, 'twitter_cards') ) {
      add_meta_box(
        'twitter_meta',
        'Custom Twitter Card Overrides',
        array( $this, 'add_twitter_meta_box_callback' ),
        $post_type
        );
    }
  }

  /**
   * Callback function to display the meta box
   *
   * @since    2.0.0
   */
  public function add_twitter_meta_box_callback() {
    wp_enqueue_style( 'wad_tc', plugin_dir_url( __FILE__ ) . '/css/admin.css' );

    wp_nonce_field( 'wad_tc', 'wad_tc_nonce' );
    include plugin_dir_path( __FILE__ ) . 'templates/twittercardform.php';
  }

  /**
   * Save the OG data
   *
   * @since    2.0.0
   */
  public function save_twitter_data($post_id) {
    if ( !post_type_supports($_POST['post_type'], 'ogtags') ) {
      return;
    }

    // verify nonce
    if ( ! wp_verify_nonce( $_POST['wad_tc_nonce'], 'wad_tc' ) ) {
      return;
    }

    if ( isset($_POST['twitter_title']) && !empty($_POST['twitter_title']) ) {
      update_post_meta($post_id, 'twitter_title', stripslashes($_POST['twitter_title']));
    } else {
      delete_post_meta($post_id, 'twitter_title');
    }

    if ( isset($_POST['twitter_desc']) && !empty($_POST['twitter_desc']) ) {
      update_post_meta($post_id, 'twitter_desc', stripslashes($_POST['twitter_desc']));
    } else {
      delete_post_meta($post_id, 'twitter_desc');
    }

    if ( isset($_POST['twitter_type']) && !empty($_POST['twitter_type']) ) {
      update_post_meta($post_id, 'twitter_type', stripslashes($_POST['twitter_type']));
    } else {
      delete_post_meta($post_id, 'twitter_type');
    }

    return $post_id;
  }
}
