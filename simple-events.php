<?php
namespace SimpleEvents;

/*
Plugin Name: Simple Events
Version: 0.1
Plugin URI:
Description: events posttype with simple time info, regular posts can be associated with events posts
Author: Peter Sharp
Author URI: petersharp.co.nz
*/

class Plugin {

  const TEXT_DOMAIN = 'simple-events';

  public function initialize() {
    global $wpdb;
    include_once 'lib/Shortcodes.php';

    $shortcodes = new Shortcodes($wpdb);
    $shortcodes->add_shortcodes();
    $this->register_hooks();
  }

  private function register_hooks() {
    add_action('init', [$this, 'register_events_posttype']);
    add_action('cmb2_init', [$this, 'cmb2_add_event_metabox']);
  }

  public function register_events_posttype() {
    $belowPosts = 5;
    $labels = [
  		'name'               => _x( 'Events', 'post type general name', self::TEXT_DOMAIN ),
  		'singular_name'      => _x( 'Event', 'post type singular name', self::TEXT_DOMAIN ),
  		'menu_name'          => _x( 'Events', 'admin menu', self::TEXT_DOMAIN ),
  		'name_admin_bar'     => _x( 'Event', 'add new on admin bar', self::TEXT_DOMAIN ),
  		'add_new'            => _x( 'Add New', 'event', self::TEXT_DOMAIN ),
  		'add_new_item'       => __( 'Add New Event', self::TEXT_DOMAIN ),
  		'new_item'           => __( 'New Event', self::TEXT_DOMAIN ),
  		'edit_item'          => __( 'Edit Event', self::TEXT_DOMAIN ),
  		'view_item'          => __( 'View Event', self::TEXT_DOMAIN ),
  		'all_items'          => __( 'All Events', self::TEXT_DOMAIN ),
  		'search_items'       => __( 'Search Events', self::TEXT_DOMAIN ),
  		'parent_item_colon'  => __( 'Parent Events:', self::TEXT_DOMAIN ),
  		'not_found'          => __( 'No events found.', self::TEXT_DOMAIN ),
  		'not_found_in_trash' => __( 'No events found in Trash.', self::TEXT_DOMAIN )
  	];

  	$args = [
  		'labels'             => $labels,
      'description'        => __( 'Description.', self::TEXT_DOMAIN ),
  		'public'             => true,
  		'publicly_queryable' => true,
  		'show_ui'            => true,
  		'show_in_menu'       => true,
      'menu_icon'          => 'dashicons-calendar-alt',
  		'query_var'          => true,
  		'rewrite'            => ['slug' => 'event'],
  		'capability_type'    => 'post',
  		'has_archive'        => true,
  		'hierarchical'       => false,
  		'menu_position'      => $belowPosts,
      'show_in_rest'       => true,
  		'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ]
  	];

    register_post_type( 'event', $args );
  }

  public function cmb2_add_event_metabox() {



  	$cmb = new_cmb2_box( [
  		'id'           => 'event_info',
  		'title'        => __( 'Event Info', self::TEXT_DOMAIN ),
  		'object_types' => ['event'],
  		'context'      => 'normal',
  		'priority'     => 'high',
  	] );

  	$cmb->add_field( array(
  		'name' => __( 'Repeats', self::TEXT_DOMAIN ),
  		'id' => 'repeates',
  		'type' => 'select',
  		'default' => false,
      'options' => [
        '' => __('Never', self::TEXT_DOMAIN),
        'daily' => __('Every Day', self::TEXT_DOMAIN),
        'weekly' => __('Every Week', self::TEXT_DOMAIN),
        'monthly' => __('Every Month', self::TEXT_DOMAIN),
        'yearly' => __('Every Year', self::TEXT_DOMAIN),
      ]
  	) );

  	$cmb->add_field( [
  		'name' => __( 'Time', self::TEXT_DOMAIN ),
  		'id' => 'time',
  		'type' => 'text_datetime_timestamp',
  	] );

  	$cmb->add_field( [
  		'name' => __( 'Location', self::TEXT_DOMAIN ),
      'desc' => __( 'Drag the marker to the event location', self::TEXT_DOMAIN ),
  		'id' => 'location',
  		'type' => 'pw_map'
  	] );

  }

  public function install() {
    $this->register_events_posttype();
    flush_rewrite_rules();
  }
}

function plugin() {
  static $instance;
  if(!$instance) {
    $instance = new Plugin();
  }
  return $instance;
}

plugin()->initialize();

register_activation_hook( __FILE__, [plugin(), 'install'] );
