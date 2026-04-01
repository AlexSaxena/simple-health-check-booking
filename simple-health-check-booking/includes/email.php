<?php

if (!defined('ABSPATH')) {
    exit;
}

function shcb_send_booking_email($data)
{
    if (empty($data['email'])) {
        return false;
    }

    $to = $data['email'];
    $subject = 'Bokningsbekräftelse';

    $full_name = esc_html($data['full_name']);
    $date = esc_html($data['date']);
    $time = esc_html($data['time']);

    $vaccine_name = esc_html($data['vaccine']['namn'] ?? '');
    $clinic_name = esc_html($data['clinic']['namn'] ?? '');
    $clinic_address = esc_html($data['clinic']['adress'] ?? '');
    $clinic_phone = esc_html($data['clinic']['telefon'] ?? '');

    $message = "
    <html>
    <body>
        <h2>Bokningsbekräftelse</h2>

        <p>Hej {$full_name},</p>

        <p>Din bokning är bekräftad. Här är dina detaljer:</p>

        <h3>Bokningsinformation</h3>
        <ul>
            <li><strong>Vaccin:</strong> {$vaccine_name}</li>
            <li><strong>Datum:</strong> {$date}</li>
            <li><strong>Tid:</strong> {$time}</li>
        </ul>

        <h3>Mottagning</h3>
        <ul>
            <li><strong>Namn:</strong> {$clinic_name}</li>
            <li><strong>Adress:</strong> {$clinic_address}</li>
            <li><strong>Telefon:</strong> {$clinic_phone}</li>
        </ul>

        <h3>Inför ditt besök</h3>
        <p>
            Kom i god tid till din bokade tid. 
            Om du känner dig sjuk eller får förhinder, vänligen kontakta mottagningen.
        </p>

        <p>Välkommen!</p>
    </body>
    </html>
    ";

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    $sent = wp_mail($to, $subject, $message, $headers);

    if (!$sent) {
        error_log('Booking email failed for: ' . $to);
    }

    return $sent;
}
