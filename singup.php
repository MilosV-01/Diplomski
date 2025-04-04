<?php
include "config.php";
if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $ime = $_POST['ime'];
    $telefon = $_POST['telefon'];
    $adresa = $_POST['adresa'];
    $lozinka = password_hash($_POST['lozinka'], PASSWORD_BCRYPT);
    $role = 'klijent'; // Default role

    $query = "INSERT INTO klijent (ime, email, telefon, adresa, lozinka, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $ime, $email, $telefon, $adresa, $lozinka, $role);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo "Uspešna registracija! Možete se sada prijaviti.";
    } else {
        echo "Greška pri registraciji. Pokušajte ponovo.";
    }
}
?>
