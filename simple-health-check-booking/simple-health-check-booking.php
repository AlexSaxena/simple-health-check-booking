<?php

/**
 * Plugin Name: Simple Health Check Booking
 * Description: A simple booking plugin for Svea Vaccin.
 * Version: 1.0.0
 * Author: Alex Saxena
 */

if (!defined("ABSPATH")) {
  exit;
}

define("SHCB_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("SHCB_PLUGIN_URL", plugin_dir_url(__FILE__));

require_once SHCB_PLUGIN_PATH . 'includes/data.php';
require_once SHCB_PLUGIN_PATH . 'includes/post-type.php';
require_once SHCB_PLUGIN_PATH . 'includes/shortcode.php';
require_once SHCB_PLUGIN_PATH . 'includes/form-handler.php';
require_once SHCB_PLUGIN_PATH . 'includes/email.php';
require_once SHCB_PLUGIN_PATH . 'includes/admin-page.php';

function shcb_enqueue_assets()
{
  wp_enqueue_style(
    "shcb-booking-css",
    SHCB_PLUGIN_URL . 'assets/css/booking.css',
    array(),
    "1.0.0",
  );

  wp_enqueue_script(
    "shcb-booking-js",
    SHCB_PLUGIN_URL . 'assets/js/booking.js',
    array(),
    "1.0.0",
    true,
  );

  wp_localize_script(
    'shcb-booking-js',
    'shcbData',
    array(
      'clinics' => shcb_get_clinics(),
    )
  );
}

add_action("wp_enqueue_scripts", "shcb_enqueue_assets");
