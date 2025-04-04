<?php
session_set_cookie_params([
    'lifetime' => 3600, 
    'path' => '/',
    'domain' => '', 
    'secure' => false, 
    'httponly' => true,
    'samesite' => 'Lax' 
]);

ini_set('session.gc_maxlifetime', 3600);

session_start();
include 'config.php';

// Obrada forme za ažuriranje planova prikazanih na početnoj strani
if (isset($_POST['update_plans'])) {
    // Resetuj sve planove
    $sql_reset = "UPDATE plan SET prikazi_na_pocetnoj = 0";
    mysqli_query($conn, $sql_reset);

    // Ažuriraj selektovane planove
    if (isset($_POST['planovi'])) {
        $selected_plans = $_POST['planovi'];
        foreach ($selected_plans as $plan_id) {
            $sql_update = "UPDATE plan SET prikazi_na_pocetnoj = 1 WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_update, 'i', $plan_id);
            mysqli_stmt_execute($stmt_update);
        }
    }
    echo "<script>alert('Planovi na početnoj strani su ažurirani.');</script>";
}
// Dohvatanje podataka o porudžbinama
$sql = "SELECT kp.id, k.ime AS klijent_ime, k.email AS klijent_email, k.telefon AS klijent_broj, p.naziv AS plan_naziv, kp.datum_pocetka, kp.trajanje
FROM klijent_plan kp
JOIN klijent k ON kp.klijent_id = k.id
JOIN plan p ON kp.plan_id = p.id";
$result_orders = mysqli_query($conn, $sql);

// Dohvatanje planova treninga
$sql = "SELECT * FROM plan";
$result = mysqli_query($conn, $sql);

// Obrada za brisanje porudžbine
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $sql_delete = "DELETE FROM klijent_plan WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, 'i', $order_id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        echo "<script>alert('Porudžbina uspešno obrisana.');</script>";
    } else {
        echo "Greška: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Admin Dashboard</h1>
        
        <div class="mb-4">
            <form action="" method="POST" style="display: inline;">
                <a href="index.php" class="btn btn-danger" name="logout">Odjavi se</a>
                <a href="admin_plan.php" class="btn btn-primary">Pregledaj planove</a>
                <a href="admin_client.php" class="btn btn-primary">Pogledaj članove</a>
            </form>
        </div>

        <!-- Prikaz planova treninga -->
        <div class="card mb-4">
            <div class="card-header">
                Lista planova treninga na početnoj stranici!
            </div>
            <div class="card-body">
                <form action="admin_dashboard.php" method="POST">
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table class='table table-bordered table-striped'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>Izaberi</th>";
                        echo "<th>Naziv</th>";
                        echo "<th>Opis</th>";
                        echo "<th>Trajanje</th>";
                        echo "<th>Cena</th>";
                        echo "<th>Akcija</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><input type='checkbox' name='planovi[]' value='" . $row['id'] . "' " . ($row['prikazi_na_pocetnoj'] ? 'checked' : '') . "></td>";
                            echo "<td>" . $row['naziv'] . "</td>";
                            echo "<td>" . $row['opis'] . "</td>";
                            echo "<td>" . $row['trajanje'] . "</td>";
                            echo "<td>" . $row['cena'] . "</td>";
                            echo "<td><a href='edit_plan.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Izmeni</a></td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo "<p>Nema planova.</p>";
                    }
                    ?>
                    <button type="submit" name="update_plans" class="btn btn-success mt-3">Ažuriraj prikazane planove</button>
                </form>
            </div>
        </div>

        <!-- Prikaz porudžbina -->
        <div class="card mb-4">
            <div class="card-header">
                Lista porudžbina
            </div>
            <div class="card-body">
                <?php 
                if (mysqli_num_rows($result_orders) > 0) {
                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Ime klijenta</th>";
                    echo "<th>Email klijenta</th>";
                    echo "<th>Broj telefona klijenta</th>";
                    echo "<th>Naziv plana</th>";
                    echo "<th>Datum početka</th>";
                    echo "<th>Trajanje</th>";
                    echo "<th>Akcija</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while($order = mysqli_fetch_assoc($result_orders)) {
                        echo "<tr>";
                        echo "<td>" . $order['klijent_ime'] . "</td>";
                        echo "<td>" . $order['klijent_email'] . "</td>";
                        echo "<td>" . $order['klijent_broj'] . "</td>";
                        echo "<td>" . $order['plan_naziv'] . "</td>";
                        echo "<td>" . $order['datum_pocetka'] . "</td>";
                        echo "<td>" . $order['trajanje'] . "</td>";
                        echo "<td>
                                <form action='admin_dashboard.php' method='POST' onsubmit='return confirm(\"Da li ste sigurni da želite da obrišete ovu porudžbinu?\");'>
                                    <input type='hidden' name='order_id' value='" . $order['id'] . "'>
                                    <button type='submit' name='delete_order' class='btn btn-danger btn-sm'>Obriši</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>Nema porudžbina.</p>";
                }
                ?>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
