<?php

/**
 * Security check for no absolute paths
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

function shcb_get_weekday_name($date)
{
    $timestamp = strtotime($date);

    $days = [
        'söndag',
        'måndag',
        'tisdag',
        'onsdag',
        'torsdag',
        'fredag',
        'lördag'
    ];

    return $days[date('w', $timestamp)];
}

function shcb_redirect_with_error($current_url, $error_code)
{
    $redirect_url = add_query_arg(
        [
            'shcb_error' => $error_code,
        ],
        $current_url
    );

    wp_redirect($redirect_url);
    exit;
}

function shcb_handle_booking_form()
{
    if (!isset($_POST['shcb_submit_booking'])) {
        return;
    }

    // Hämta och sanera formulärdata
    $full_name = sanitize_text_field($_POST['full_name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $clinic_id = intval($_POST['clinic_id'] ?? 0);
    $vaccination_type = sanitize_text_field($_POST['vaccination_type'] ?? '');
    $booking_date = sanitize_text_field($_POST['booking_date'] ?? '');
    $booking_time = sanitize_text_field($_POST['booking_time'] ?? '');
    $fever = sanitize_text_field($_POST['fever'] ?? '');
    $recent_illness = sanitize_text_field($_POST['recent_illness'] ?? '');
    $vaccine_allergy = sanitize_text_field($_POST['vaccine_allergy'] ?? '');
    $medication = sanitize_text_field($_POST['medication'] ?? '');
    $current_url = esc_url_raw($_POST['shcb_current_url'] ?? home_url('/'));

    // Validera obligatoriska fält
    if (
        empty($full_name) ||
        empty($email) ||
        empty($phone) ||
        empty($clinic_id) ||
        empty($vaccination_type) ||
        empty($booking_date) ||
        empty($booking_time) ||
        empty($fever) ||
        empty($recent_illness) ||
        empty($vaccine_allergy) ||
        empty($medication)
    ) {
        shcb_redirect_with_error($current_url, 'missing_fields');
    }

    // Validera email
    if (!is_email($email)) {
        shcb_redirect_with_error($current_url, 'invalid_email');
    }

    // Hämta mottagning och vaccin
    $clinic = shcb_get_clinic_by_id($clinic_id);
    $vaccine = shcb_get_vaccine_by_id($vaccination_type);

    if (!$clinic || !$vaccine) {
        shcb_redirect_with_error($current_url, 'invalid_selection');
    }

    // Validera datum
    $today = date('Y-m-d');

    if ($booking_date < $today) {
        shcb_redirect_with_error($current_url, 'invalid_date');
    }

    // Validera tid utifrån vald mottagning och veckodag
    $weekday = shcb_get_weekday_name($booking_date);
    $available_times = shcb_get_available_times($clinic_id, $weekday);

    if (!in_array($booking_time, $available_times, true)) {
        shcb_redirect_with_error($current_url, 'invalid_time');
    }

    // Sätt status
    $health_status = 'approved';
    $status = 'pending';

    if (
        $fever === 'yes' ||
        $recent_illness === 'yes' ||
        $vaccine_allergy === 'yes' ||
        $medication === 'yes'
    ) {
        $health_status = 'contact_clinic';
    }

    // Skapa titel för posten
    $title = 'Bokning - ' . $full_name . ' - ' . $booking_date . ' ' . $booking_time;

    // Skapa post
    $post_id = wp_insert_post([
        'post_title'  => $title,
        'post_type'   => 'health_booking',
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id) || !$post_id) {
        error_log('SHCB: Failed to create booking post');
        shcb_redirect_with_error($current_url, 'save_failed');
    }

    // Spara meta
    update_post_meta($post_id, 'full_name', $full_name);
    update_post_meta($post_id, 'email', $email);
    update_post_meta($post_id, 'phone', $phone);
    update_post_meta($post_id, 'clinic_id', $clinic_id);
    update_post_meta($post_id, 'vaccination_type', $vaccination_type);
    update_post_meta($post_id, 'booking_date', $booking_date);
    update_post_meta($post_id, 'booking_time', $booking_time);
    update_post_meta($post_id, 'fever', $fever);
    update_post_meta($post_id, 'recent_illness', $recent_illness);
    update_post_meta($post_id, 'vaccine_allergy', $vaccine_allergy);
    update_post_meta($post_id, 'medication', $medication);
    update_post_meta($post_id, 'health_status', $health_status);
    update_post_meta($post_id, 'status', $status);

    // Skicka email endast om bokningen är godkänd
    if ($health_status === 'approved' && $clinic && $vaccine) {
        shcb_send_booking_email([
            'email' => $email,
            'full_name' => $full_name,
            'clinic' => $clinic,
            'vaccine' => $vaccine,
            'date' => $booking_date,
            'time' => $booking_time
        ]);
    }

    // Redirect tillbaka till sidan med resultatdata
    $redirect_url = add_query_arg(
        [
            'shcb_result'      => $health_status,
            'clinic_id'        => $clinic_id,
            'vaccination_type' => $vaccination_type,
            'booking_date'     => $booking_date,
            'booking_time'     => $booking_time,
        ],
        $current_url
    );

    wp_redirect($redirect_url);
    exit;
}

add_action('init', 'shcb_handle_booking_form');
