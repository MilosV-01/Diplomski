<?php

include "config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM clanarina WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $naziv = $row['naziv'];
    $opis = $row['opis'];
    $trajanje = $row['trajanje'];
    $cena = $row['cena'];
} else {
    $id = '';
    $naziv = '';
    $opis = '';
    $trajanje = '';
    $cena = '';
}

if (isset($_POST['submit'])) {
    $naziv = $_POST['naziv'];
    $opis = $_POST['opis'];
    $trajanje = $_POST['trajanje'];
    $cena = $_POST['cena'];
    
    if (!empty($id)) {
        $sql = "UPDATE clanarina SET naziv='$naziv', opis='$opis', trajanje='$trajanje', cena='$cena' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO clanarina (naziv, opis, trajanje, cena) VALUES ('$naziv', '$opis', '$trajanje', '$cena')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "Članarina je uspešno ažurirana.";
    } else {
        echo "Greška: " . mysqli_error($conn);
    }
}

// Obrada forme za brisanje clanarine
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM clanarina WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Clanarina je uspesno obrisana.'); window.location.href='admin_dashboard.php';</script>";
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izmeni članarinu</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container mt-4">
        <h1 class="mb-4">Izmeni članarinu</h1>
        <a href="admin_plan.php" class="btn btn-secondary mb-3">Nazad</a>

        <form action="edit_clanarina.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label for="naziv">Naziv:</label>
                <input type="text" class="form-control" id="naziv" name="naziv" value="<?php echo $naziv; ?>" required>
            </div>
            <div class="form-group">
                <label for="opis">Opis:</label>
                <input type="text" class="form-control" id="opis" name="opis" value="<?php echo $opis; ?>" required>
            </div>
            <div class="form-group">
                <label for="trajanje">Trajanje:</label>
                <input type="text" class="form-control" id="trajanje" name="trajanje" value="<?php echo $trajanje; ?>" required>
            </div>
            <div class="form-group">
                <label for="cena">Cena:</label>
                <input type="text" class="form-control" id="cena" name="cena" value="<?php echo $cena; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Ažuriraj</button>
        </form>
        <div class="card mt-4">
            <div class="card-header">
                Brisanje plana treninga
            </div>
            <div class="card-body">
                <form action="edit_clanarina.php?id=<?php echo $id; ?>" method="POST">
                    <p>Da li ste sigurni da želite da obrišete ovu clanarinu?</p>
                    <button type="submit" name="delete" class="btn btn-danger">Obriši clanarinu</button>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>
