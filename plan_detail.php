<?php
include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kupi']) && isset($_POST['plan_id'])) {
    if (isset($_SESSION['email'])) {
        $plan_id = intval($_POST['plan_id']);
        $klijent_id = intval($_SESSION['user_id']); // Pretpostavlja se da imate `user_id` u sesiji

        // Pronađi datum početka i trajanje plana
        $sql = "SELECT trajanje FROM plan WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $plan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $trajanje = htmlspecialchars($row['trajanje']);
            $datum_pocetka = date('Y-m-d'); // Trenutni datum
            $datum_kraja = date('Y-m-d', strtotime("+$trajanje months", strtotime($datum_pocetka)));

            // Unesi podatke u tabelu klijent_plan
            $sql_insert = "INSERT INTO klijent_plan (klijent_id, plan_id, datum_pocetka, trajanje) VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, 'iiss', $klijent_id, $plan_id, $datum_pocetka, $trajanje);
            $stmt_insert->execute();

            // Obavesti admina
            $admin_email = 'vukmirovicai@gmail.com'; // Zamenite stvarnim admin emailom
            $subject = "Novi Plan Kupljen";
            $message = "Korisnik sa ID-jem $klijent_id je kupio plan sa ID-jem $plan_id. Trajanje: $trajanje meseci.";
            $headers = "From: no-reply@example.com";

            mail($admin_email, $subject, $message, $headers);

            echo "<p>Plan je uspešno kupljen!</p>";
        } else {
            echo "<p>Plan sa ovim ID-om ne postoji.</p>";
        }

        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt_insert);
    } else {
        echo "<p>Morate biti ulogovani da biste kupili plan.</p>";
    }
}
?>
