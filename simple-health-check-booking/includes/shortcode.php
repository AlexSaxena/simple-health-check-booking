<?php

/**
 * Security check for no absolute paths
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

function shcb_render_booking_form()
{
    $vaccines = shcb_get_vaccines();
    $clinics = shcb_get_clinics();

    // Visa felmeddelanden
    if (isset($_GET['shcb_error'])) {
        $error = sanitize_text_field($_GET['shcb_error']);

        if ($error === 'missing_fields') {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Något saknas</h2>
                        <p>Vänligen fyll i alla obligatoriska fält.</p>
                    </div>
                </div>
            ';
        }

        if ($error === 'invalid_email') {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Ogiltig e-postadress</h2>
                        <p>Vänligen ange en giltig e-postadress.</p>
                    </div>
                </div>
            ';
        }

        if ($error === 'invalid_selection') {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Ogiltigt val</h2>
                        <p>Vald mottagning eller vaccination kunde inte hittas.</p>
                    </div>
                </div>
            ';
        }

        if ($error === 'invalid_date') {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Ogiltigt datum</h2>
                        <p>Välj ett framtida datum för bokningen.</p>
                    </div>
                </div>
            ';
        }

        if ($error === 'invalid_time') {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Ogiltig tid</h2>
                        <p>Den valda tiden är inte tillgänglig för vald mottagning och dag.</p>
                    </div>
                </div>
            ';
        }

        if ($error === 'save_failed') {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Något gick fel</h2>
                        <p>Bokningen kunde inte sparas. Försök igen.</p>
                    </div>
                </div>
            ';
        }
    }

    // Visa resultatvy efter submit
    if (isset($_GET['shcb_result'])) {
        $result = sanitize_text_field($_GET['shcb_result']);
        $selected_clinic_id = intval($_GET['clinic_id'] ?? 0);
        $selected_clinic = shcb_get_clinic_by_id($selected_clinic_id);

        $selected_vaccine_id = sanitize_text_field($_GET['vaccination_type'] ?? '');
        $selected_vaccine = shcb_get_vaccine_by_id($selected_vaccine_id);

        $selected_date = sanitize_text_field($_GET['booking_date'] ?? '');
        $selected_time = sanitize_text_field($_GET['booking_time'] ?? '');

        if ($result === 'approved' && $selected_clinic && $selected_vaccine) {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-success">
                        <h2>Bokningsbekräftelse</h2>
                        <p>Din bokning har registrerats och är godkänd.</p>
                        <p><strong>Vaccin:</strong> ' . esc_html($selected_vaccine['namn']) . '</p>
                        <p><strong>Mottagning:</strong> ' . esc_html($selected_clinic['namn']) . '</p>
                        <p><strong>Datum:</strong> ' . esc_html($selected_date) . '</p>
                        <p><strong>Tid:</strong> ' . esc_html($selected_time) . '</p>
                    </div>
                </div>
            ';
        }

        if ($result === 'contact_clinic' && $selected_clinic) {
            return '
                <div class="shcb-booking-form-wrapper">
                    <div class="shcb-message shcb-warning">
                        <h2>Kontakta mottagning först</h2>
                        <p>Vi behöver att du kontaktar mottagningen innan bokningen kan bekräftas.</p>
                        <p><strong>Mottagning:</strong> ' . esc_html($selected_clinic['namn']) . '</p>
                        <p><strong>Telefonnummer:</strong> ' . esc_html($selected_clinic['telefon']) . '</p>
                    </div>
                </div>
            ';
        }
    }

    ob_start();
?>

    <div class="shcb-booking-form-wrapper">
        <form method="post" action="">
            <input type="hidden" name="shcb_current_url" value="<?php echo esc_url(get_permalink()); ?>">

            <h2>Simple Health Check Booking</h2>

            <div class="shcb-step" data-step="1">
                <h3>Steg 1: Välj vaccination och mottagning</h3>

                <p>
                    <label for="shcb_vaccine">Vaccin</label><br />
                    <select name="vaccination_type" id="shcb_vaccine" required>
                        <option value="">Välj vaccin</option>
                        <?php foreach ($vaccines as $vaccine) : ?>
                            <option value="<?php echo esc_attr($vaccine['id']); ?>">
                                <?php echo esc_html($vaccine['namn']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="shcb_clinic">Mottagning</label><br />
                    <select name="clinic_id" id="shcb_clinic" required>
                        <option value="">Välj mottagning</option>
                        <?php foreach ($clinics as $clinic) : ?>
                            <option value="<?php echo esc_attr($clinic['id']); ?>">
                                <?php echo esc_html($clinic['namn']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="shcb_date">Datum</label><br />
                    <input type="date" name="booking_date" id="shcb_date" required />
                </p>

                <p>
                    <label for="shcb_time">Tid</label><br />
                    <select name="booking_time" id="shcb_time" required>
                        <option value="">Välj tid</option>
                    </select>
                </p>

                <p>
                    <button type="button" class="shcb-next">Nästa</button>
                </p>
            </div>

            <div class="shcb-step" data-step="2" style="display:none;">
                <h3>Steg 2: Hälsokontroll</h3>

                <p>
                    <label for="shcb_fever">Har du feber idag?</label><br />
                    <select name="fever" id="shcb_fever" required>
                        <option value="">Välj</option>
                        <option value="no">Nej</option>
                        <option value="yes">Ja</option>
                    </select>
                </p>

                <p>
                    <label for="shcb_recent_illness">Har du varit sjuk senaste veckan?</label><br />
                    <select name="recent_illness" id="shcb_recent_illness" required>
                        <option value="">Välj</option>
                        <option value="no">Nej</option>
                        <option value="yes">Ja</option>
                    </select>
                </p>

                <p>
                    <label for="shcb_vaccine_allergy">Har du allergier mot vacciner?</label><br />
                    <select name="vaccine_allergy" id="shcb_vaccine_allergy" required>
                        <option value="">Välj</option>
                        <option value="no">Nej</option>
                        <option value="yes">Ja</option>
                    </select>
                </p>

                <p>
                    <label for="shcb_medication">Tar du mediciner?</label><br />
                    <select name="medication" id="shcb_medication" required>
                        <option value="">Välj</option>
                        <option value="no">Nej</option>
                        <option value="yes">Ja</option>
                    </select>
                </p>

                <p>
                    <button type="button" class="shcb-prev">Tillbaka</button>
                    <button type="button" class="shcb-next">Nästa</button>
                </p>
            </div>

            <div class="shcb-step" data-step="3" style="display:none;">
                <h3>Steg 3: Personuppgifter</h3>

                <p>
                    <label for="shcb_name">Namn</label><br />
                    <input type="text" name="full_name" id="shcb_name" required />
                </p>

                <p>
                    <label for="shcb_email">Email</label><br />
                    <input type="email" name="email" id="shcb_email" required />
                </p>

                <p>
                    <label for="shcb_phone">Telefonnummer</label><br />
                    <input type="text" name="phone" id="shcb_phone" required />
                </p>

                <p>
                    <button type="button" class="shcb-prev">Tillbaka</button>
                    <button type="submit" name="shcb_submit_booking">Skicka bokning</button>
                </p>
            </div>
        </form>
    </div>

<?php
    return ob_get_clean();
}

add_shortcode('health_check_booking', 'shcb_render_booking_form');
