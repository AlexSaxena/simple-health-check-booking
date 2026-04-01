<?php

/**
 * Security check for no absolute paths
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

function shcb_get_mock_data()
{
    return [
        'vacciner' => [
            [
                'id' => 'covid',
                'namn' => 'Covid-19',
                'beskrivning' => 'Grundskydd mot Covid-19',
            ],
            [
                'id' => 'influensa',
                'namn' => 'Influensa',
                'beskrivning' => 'Säsongsinfluensa vaccination',
            ],
            [
                'id' => 'hepatit',
                'namn' => 'Hepatit A',
                'beskrivning' => 'Skydd mot Hepatit A',
            ],
        ],
        'mottagningar' => [
            [
                'id' => 1,
                'namn' => 'Svea Vaccin Stockholm City',
                'adress' => 'Drottninggatan 45, 111 21 Stockholm',
                'telefon' => '08-123 45 67',
                'email' => 'stockholm@sveavaccin.se',
                'tillgangliga_tider' => [
                    'måndag' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'tisdag' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'onsdag' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'torsdag' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'fredag' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00'],
                    'lördag' => ['09:00', '10:00', '11:00', '12:00', '13:00'],
                    'söndag' => [],
                ],
            ],
            [
                'id' => 2,
                'namn' => 'Svea Vaccin Göteborg Centrum',
                'adress' => 'Kungsgatan 22, 411 19 Göteborg',
                'telefon' => '031-456 78 90',
                'email' => 'goteborg@sveavaccin.se',
                'tillgangliga_tider' => [
                    'måndag' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'tisdag' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'onsdag' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'torsdag' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
                    'fredag' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00'],
                    'lördag' => ['10:00', '11:00', '12:00', '13:00'],
                    'söndag' => [],
                ],
            ],
            [
                'id' => 3,
                'namn' => 'Svea Vaccin Malmö Väster',
                'adress' => 'Stora Nygatan 33, 211 37 Malmö',
                'telefon' => '040-789 01 23',
                'email' => 'malmo@sveavaccin.se',
                'tillgangliga_tider' => [
                    'måndag' => ['10:00', '11:00', '12:00', '14:00', '15:00', '16:00'],
                    'tisdag' => ['10:00', '11:00', '12:00', '14:00', '15:00', '16:00'],
                    'onsdag' => ['10:00', '11:00', '12:00', '14:00', '15:00', '16:00'],
                    'torsdag' => ['10:00', '11:00', '12:00', '14:00', '15:00', '16:00'],
                    'fredag' => ['10:00', '11:00', '12:00', '14:00', '15:00'],
                    'lördag' => ['11:00', '12:00', '13:00', '14:00'],
                    'söndag' => [],
                ],
            ],
            [
                'id' => 4,
                'namn' => 'Svea Vaccin Stockholm Södermalm',
                'adress' => 'Götgatan 78, 118 30 Stockholm',
                'telefon' => '08-234 56 78',
                'email' => 'sodermalm@sveavaccin.se',
                'tillgangliga_tider' => [
                    'måndag' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'],
                    'tisdag' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'],
                    'onsdag' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'],
                    'torsdag' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'],
                    'fredag' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'],
                    'lördag' => ['10:00', '11:00', '12:00', '13:00', '14:00'],
                    'söndag' => [],
                ],
            ],
        ],
    ];
}


function shcb_get_vaccines()
{
    $data = shcb_get_mock_data();
    return $data['vacciner'];
}

function shcb_get_clinics()
{
    $data = shcb_get_mock_data();
    return $data['mottagningar'];
}

function shcb_get_clinic_by_id($id)
{
    $clinics = shcb_get_clinics();
    foreach ($clinics as $clinic) {
        if ((int) $clinic['id'] === (int) $id) {
            return $clinic;
        }
    }
    return null;
}

function shcb_get_available_times($clinic_id, $weekday)
{
    $clinic = shcb_get_clinic_by_id($clinic_id);
    if (!$clinic || empty($clinic['tillgangliga_tider'][$weekday])) {
        return [];
    }
    return $clinic['tillgangliga_tider'][$weekday];
}

function shcb_get_vaccine_by_id($id)
{
    $vaccines = shcb_get_vaccines();

    foreach ($vaccines as $vaccine) {
        if ($vaccine['id'] === $id) {
            return $vaccine;
        }
    }

    return null;
}
