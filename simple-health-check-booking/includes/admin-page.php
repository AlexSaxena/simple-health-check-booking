<?php

if (!defined('ABSPATH')) {
    exit;
}

function shcb_register_admin_page()
{
    add_management_page(
        'Hälsokontroll Bokningar',
        'Hälsokontroll Bokningar',
        'manage_options',
        'shcb-bookings',
        'shcb_render_admin_page'
    );
}
add_action('admin_menu', 'shcb_register_admin_page');

function shcb_handle_toggle_status()
{
    if (!is_admin()) {
        return;
    }

    if (!isset($_GET['shcb_toggle_status'], $_GET['booking_id'])) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $booking_id = intval($_GET['booking_id']);

    if ($booking_id > 0) {
        $current_status = get_post_meta($booking_id, 'status', true);

        $new_status = ($current_status === 'completed') ? 'pending' : 'completed';

        update_post_meta($booking_id, 'status', $new_status);
    }

    $redirect_url = remove_query_arg(['shcb_toggle_status', 'booking_id']);
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_init', 'shcb_handle_toggle_status');

function shcb_render_admin_page()
{
    $selected_clinic = intval($_GET['clinic_filter'] ?? 0);
    $search_term = sanitize_text_field($_GET['s'] ?? '');

    $meta_query = [];
    $search_results = [];

    if ($selected_clinic > 0) {
        $meta_query[] = [
            'key' => 'clinic_id',
            'value' => $selected_clinic,
            'compare' => '='
        ];
    }

    $query_args = [
        'post_type' => 'health_booking',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => $meta_query,
        'orderby' => 'date',
        'order' => 'DESC',
    ];

    $bookings = get_posts($query_args);
    $clinics = shcb_get_clinics();

    if (!empty($search_term)) {
        $filtered = [];

        foreach ($bookings as $booking) {
            $name = get_post_meta($booking->ID, 'full_name', true);
            $email = get_post_meta($booking->ID, 'email', true);

            if (
                stripos($name, $search_term) !== false ||
                stripos($email, $search_term) !== false
            ) {
                $filtered[] = $booking;
            }
        }

        $bookings = $filtered;
    }
?>
    <div class="wrap">
        <h1>Hälsokontroll Bokningar</h1>

        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="shcb-bookings">

            <select name="clinic_filter">
                <option value="0">Alla mottagningar</option>
                <?php foreach ($clinics as $clinic) : ?>
                    <option value="<?php echo esc_attr($clinic['id']); ?>" <?php selected($selected_clinic, $clinic['id']); ?>>
                        <?php echo esc_html($clinic['namn']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input
                type="text"
                name="s"
                placeholder="Sök namn eller email"
                value="<?php echo esc_attr($search_term); ?>">

            <button type="submit" class="button">Filtrera</button>
        </form>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Namn och kontakt</th>
                    <th>Vaccination och mottagning</th>
                    <th>Datum och tid</th>
                    <th>Hälsostatus</th>
                    <th>Status</th>
                    <th>Åtgärd</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)) : ?>
                    <?php foreach ($bookings as $booking) : ?>
                        <?php
                        $full_name = get_post_meta($booking->ID, 'full_name', true);
                        $email = get_post_meta($booking->ID, 'email', true);
                        $phone = get_post_meta($booking->ID, 'phone', true);
                        $clinic_id = intval(get_post_meta($booking->ID, 'clinic_id', true));
                        $vaccination_type = get_post_meta($booking->ID, 'vaccination_type', true);
                        $booking_date = get_post_meta($booking->ID, 'booking_date', true);
                        $booking_time = get_post_meta($booking->ID, 'booking_time', true);
                        $health_status = get_post_meta($booking->ID, 'health_status', true);
                        $status = get_post_meta($booking->ID, 'status', true);

                        $clinic = shcb_get_clinic_by_id($clinic_id);
                        $vaccine = shcb_get_vaccine_by_id($vaccination_type);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($full_name); ?></strong><br>
                                <?php echo esc_html($email); ?><br>
                                <?php echo esc_html($phone); ?>
                            </td>
                            <td>
                                <?php echo esc_html($vaccine['namn'] ?? 'Okänt vaccin'); ?><br>
                                <?php echo esc_html($clinic['namn'] ?? 'Okänd mottagning'); ?>
                            </td>
                            <td>
                                <?php echo esc_html($booking_date); ?><br>
                                <?php echo esc_html($booking_time); ?>
                            </td>
                            <td>
                                <?php echo $health_status === 'approved' ? 'Godkänd' : 'Kontakta mottagning'; ?>
                            </td>
                            <td>
                                <?php echo $status === 'completed' ? 'Genomförd' : 'Pending'; ?>
                            </td>
                            <td>
                                <a
                                    class="button button-small"
                                    href="<?php echo esc_url(add_query_arg([
                                                'page' => 'shcb-bookings',
                                                'shcb_toggle_status' => 1,
                                                'booking_id' => $booking->ID,
                                            ], admin_url('tools.php'))); ?>">
                                    <?php echo $status === 'completed' ? 'Sätt till pending' : 'Markera som genomförd'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">Inga bokningar hittades.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}
