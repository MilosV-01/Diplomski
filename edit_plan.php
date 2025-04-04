<?php
include 'config.php';

// Proverite da li je ID planova prosleđen
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Nevažeći ID planova.');
}

$id = $_GET['id'];

// Dohvatanje trenutnih podataka o planu
$sql = "SELECT * FROM plan WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    die('Plan treninga ne postoji.');
}

$row = mysqli_fetch_assoc($result);

// Obrada forme za ažuriranje plana
if (isset($_POST['update'])) {
    $naziv = $_POST['naziv'];
    $opis = $_POST['opis'];
    $trajanje = $_POST['trajanje'];
    $cena = $_POST['cena'];

    $update_sql = "UPDATE plan SET naziv = ?, opis = ?, trajanje = ?, cena = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, 'ssssi', $naziv, $opis, $trajanje, $cena, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Plan treninga uspešno ažuriran.'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "Greška: " . mysqli_error($conn);
    }
}

// Obrada forme za brisanje plana
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM plan WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Plan treninga uspešno obrisan.'); window.location.href='admin_dashboard.php';</script>";
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
    <title>Uredi Plan Treninga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Uredi Plan Treninga</h1>
        <a href="admin_plan.php" class="btn btn-secondary mb-3">Nazad</a>

        <div class="card">
            <div class="card-header">
                Forma za uređivanje plana treninga
            </div>
            <div class="card-body">
                <form action="edit_plan.php?id=<?php echo $id; ?>" method="POST">
                    <div class="form-group">
                        <label for="naziv">Naziv:</label>
                        <input type="text" class="form-control" id="naziv" name="naziv" value="<?php echo htmlspecialchars($row['naziv']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis:</label>
                        <textarea class="form-control" id="opis" name="opis" required><?php echo htmlspecialchars($row['opis']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="trajanje">Trajanje:</label>
                        <input type="text" class="form-control" id="trajanje" name="trajanje" value="<?php echo htmlspecialchars($row['trajanje']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cena">Cena:</label>
                        <input type="number" step="0.01" class="form-control" id="cena" name="cena" value="<?php echo htmlspecialchars($row['cena']); ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Ažuriraj plan</button>
                </form>
            </div>
        </div>

        <!-- Forma za brisanje plana -->
        <div class="card mt-4">
            <div class="card-header">
                Brisanje plana treninga
            </div>
            <div class="card-body">
                <form action="edit_plan.php?id=<?php echo $id; ?>" method="POST">
                    <p>Da li ste sigurni da želite da obrišete ovaj plan treninga?</p>
                    <button type="submit" name="delete" class="btn btn-danger">Obriši plan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
