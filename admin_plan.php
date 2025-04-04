<?php

include "config.php";

// Provera da li je forma za dodavanje novog plana ili članarine poslata
if (isset($_POST['submit'])) {
    
    $naziv = $_POST['naziv'];
    $opis = $_POST['opis'];
    $trajanje = $_POST['trajanje'];
    $cena = $_POST['cena'];
    $tip = $_POST['tip']; // 'plan' ili 'clanarina'
    
    if ($tip == 'plan') {
        $sql = "INSERT INTO plan (naziv, opis, trajanje, cena) VALUES ('$naziv', '$opis', '$trajanje', '$cena')";
    } elseif ($tip == 'clanarina') {
        $sql = "INSERT INTO clanarina (naziv, opis, trajanje, cena) VALUES ('$naziv', '$opis', '$trajanje', '$cena')";
    }
    
    if (mysqli_query($conn, $sql)) {  
        echo "Uspešno dodat " . ($tip == 'plan' ? "plan" : "članarina") . ".";
    } else {
        echo "Greška: " . mysqli_error($conn);
    }
}

// Prikaz planova treninga
$sql_plan = "SELECT * FROM plan";
$result_plan = mysqli_query($conn, $sql_plan);

// Prikaz članarina
$sql_clanarina = "SELECT * FROM clanarina";
$result_clanarina = mysqli_query($conn, $sql_clanarina);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Admin</h1>
        
        <div class="mb-4">
            <a href="index.php" class="btn btn-danger">Odjavi se</a>
            <a href="admin_dashboard.php" class="btn btn-primary">Pregledaj dashboard</a>
            <a href="admin_client.php" class="btn btn-primary">Pogledaj članove</a>
        </div>

        <!-- Dodavanje novog plana ili članarine -->
        <div class="card mb-4">
            <div class="card-header">
                Dodaj novi plan ili članarinu
            </div>
            <div class="card-body">
                <form action="admin_plan.php" method="POST">
                    <div class="form-group">
                        <label for="tip">Tip:</label>
                        <select class="form-control" id="tip" name="tip" required>
                            <option value="plan">Plan</option>
                            <option value="clanarina">Članarina</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="naziv">Naziv:</label>
                        <input type="text" class="form-control" id="naziv" name="naziv" required>
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis:</label>
                        <input type="text" class="form-control" id="opis" name="opis" required>
                    </div>
                    <div class="form-group">
                        <label for="trajanje">Trajanje:</label>
                        <input type="text" class="form-control" id="trajanje" name="trajanje" required>
                    </div>
                    <div class="form-group">
                        <label for="cena">Cena:</label>
                        <input type="text" class="form-control" id="cena" name="cena" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">Dodaj</button>
                </form>
            </div>
        </div>

        <!-- Prikaz planova treninga -->
        <div class="card mb-4">
            <div class="card-header">
                Lista planova treninga
            </div>
            <div class="card-body">
                <?php 
                if (mysqli_num_rows($result_plan) > 0) {
                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Naziv</th>";
                    echo "<th>Opis</th>";
                    echo "<th>Trajanje</th>";
                    echo "<th>Cena</th>";
                    echo "<th>Akcija</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while($row = mysqli_fetch_assoc($result_plan)) {
                        echo "<tr>";
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
                    echo "Nema planova treninga.";
                }
                ?>
            </div>
        </div>

        <!-- Prikaz članarina -->
        <div class="card mb-4">
            <div class="card-header">
                Lista članarina
            </div>
            <div class="card-body">
                <?php 
                if (mysqli_num_rows($result_clanarina) > 0) {
                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Naziv</th>";
                    echo "<th>Opis</th>";
                    echo "<th>Trajanje</th>";
                    echo "<th>Cena</th>";
                    echo "<th>Akcija</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while($row = mysqli_fetch_assoc($result_clanarina)) {
                        echo "<tr>";
                        echo "<td>" . $row['naziv'] . "</td>";
                        echo "<td>" . $row['opis'] . "</td>";
                        echo "<td>" . $row['trajanje'] . "</td>";
                        echo "<td>" . $row['cena'] . "</td>";
                        echo "<td><a href='edit_clanarina.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Izmeni</a></td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "Nema članarina.";
                }
                ?>
            </div>
        </div>

    </div>
</body>
</html>
