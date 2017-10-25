<?php
namespace SimpleEvents;

use WP_Error;

class Shortcodes {

  private $db;

  public function __construct($db) {
    $this->db = $db;
  }

  public function add_shortcodes() {
    add_shortcode( 'event', [$this, 'do_event_shortcode'] );
  }

  public function do_event_shortcode($atts) {

    $event = $this->get_event_by_title($atts['title']);

    if(\is_wp_error($event)) return $event->get_error_message();

    return $this->render_event($event);
  }

  private function get_event_by_title($title) {
    $event = $this->db->get_row($this->db->prepare("SELECT
                                            ID, post_title, post_content
                                           from {$this->db->posts}
                                           where post_type = 'event'
                                           and   post_title = %s ", $title));

    if(!$event) return new WP_Error('not found', "Could not find Event with title {$title}");

    $event->repeats = get_post_meta( $event->ID, 'repeats', true );
    $event->date = get_post_meta( $event->ID, 'date', true );
    $event->time = get_post_meta( $event->ID, 'time', true );
    $event->location = get_post_meta( $event->ID, 'location', true );

    return $event;
  }

  private function render_event($data) {
    ob_start();
   ?>
   <section>
     <header>
       <h1><?php echo $data->post_title ?></h1>
     </header>
     <?php echo $data->post_content ?>
     <footer>
       <?php echo isset($data->repeats) ? $data->repeats : '' ?>
       <?php echo isset($data->date) ? $data->date : '' ?>
       <?php echo isset($data->time) ? $data->time : '' ?>
       <?php echo isset($data->location) ? join(', ', $data->location) : '' ?>
     </footer>
   </section>
   <?php
   return ob_get_clean();
  }
}
