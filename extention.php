<?php
/*
Plugin Name: Booking Extension
Description: Extension de réservation de créneau.
Version: 1.0
Author: Sarra et Naissiane
*/

// Enqueue jQuery UI datepicker
function enqueue_datepicker() {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-datepicker-css', plugins_url('jquery-ui.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_datepicker');

// Shortcode pour afficher le formulaire de réservation
function booking_form_shortcode() {
    ob_start();
    ?>
    <div class="booking-form">
        <form action="" method="post">
            <label for="first_name">Prénom:</label>
            <input type="text" name="first_name" required>

            <label for="last_name">Nom:</label>
            <input type="text" name="last_name" required>

            <label for="email">Adresse e-mail:</label>
            <input type="email" name="email" required>

            <label for="calendar">Calendrier:</label>
            <input type="text" name="calendar" id="datepicker" required>

            <label for="timeslot">Plage horaire:</label>
            <select name="timeslot" required>
                <optgroup label="Matin">
                    <option value="morning_1">9h - 11h</option>
                    <option value="morning_2">11h - 13h</option>
                    <option value="morning_3">13h - 15h</option>
                </optgroup>
                <optgroup label="Après-midi">
                    <option value="afternoon_1">15h - 17h</option>
                    <option value="afternoon_2">17h - 19h</option>
                    <option value="afternoon_3">19h - 21h</option>
                </optgroup>
            </select>

            <input type="submit" name="submit_booking" value="Réserver">
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $("#datepicker").datepicker();
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('booking_form', 'booking_form_shortcode');

// Traitement du formulaire
function process_booking_form() {
    if (isset($_POST['submit_booking'])) {
        // Récupération des données du formulaire
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $calendar = sanitize_text_field($_POST['calendar']);
        $timeslot = sanitize_text_field($_POST['timeslot']);

        // Envoi d'un e-mail à l'administrateur
        $admin_email = get_option('admin_email');
        $subject = 'Nouvelle réservation';
        $message = "Nouvelle réservation effectuée :\n\n";
        $message .= "Nom: $last_name\n";
        $message .= "Prénom: $first_name\n";
        $message .= "Adresse e-mail: $email\n";
        $message .= "Calendrier: $calendar\n";
        $message .= "Horaire: $timeslot\n";

        // Ajout de lignes de débogage
        error_log('Debug: ' . print_r($message, true));

        // Envoyer l'e-mail
        $mail_result = wp_mail($admin_email, $subject, $message);

        // Ajout de lignes de débogage
        error_log('Debug: Email sent? ' . ($mail_result ? 'Yes' : 'No'));

        // Affichage du message de confirmation
        echo '<p>Réservation réussie. Merci!</p>';
    }
}
add_action('init', 'process_booking_form', 10);  // Définir la priorité à 10
