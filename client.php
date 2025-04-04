<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM klijent WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ime = mysqli_real_escape_string($conn, $_POST['ime']);
    $telefon = mysqli_real_escape_string($conn, $_POST['telefon']);
    $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);

    $sql = "UPDATE klijent SET ime='$ime', telefon='$telefon', adresa='$adresa' WHERE email='$email'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['user'] = $ime;
        header('Location: client.php');
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uredi Profil</title>
    <link rel="stylesheet" href="icon.css">
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="script.js" defer></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <nav class="navbar">
            <span class="hamburger-btn material-symbols-rounded">menu</span>
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="logo">
                <h2>MLD GYM</h2>
            </a>
            <ul class="links">
                <span class="close-btn material-symbols-rounded">close</span>
                <li><a href="index.php">POČETNA</a></li>
                <li><a href="planovi.php">PLANOVI</a></li>
                <li><a href="o_nama.php">O NAMA</a></li>
                <li><a href="kontakt.php">KONTAKT</a></li>
            </ul>
            <div class="dropdown">
                <button class="login-btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION['user']); ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="client.php">Uredi profil</a>
                    <form action="index.php" method="POST">
                        <button class="dropdown-item" type="submit" name="logout">Odjavi se</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <form action="" method="POST">
            <div class="form-group">
                <label for="ime" style="color: white;">Ime:</label>
                <input type="text" class="form-control" id="ime" name="ime" value="<?php echo htmlspecialchars($user['ime']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefon" style="color: white;">Telefon:</label>
                <input type="text" class="form-control" id="telefon" name="telefon" value="<?php echo htmlspecialchars($user['telefon']); ?>" required>
            </div>
            <div class="form-group">
                <label for="adresa" style="color: white;">Adresa:</label>
                <input type="text" class="form-control" id="adresa" name="adresa" value="<?php echo htmlspecialchars($user['adresa']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Sačuvaj Promene</button>
        </form>

        <!-- Prikaz informacija o članarini -->
        <div class="card-body">
            <h4 style="color: white; background-color: black;">Informacije o članarini</h4>
            <p style="color: white; background-color: black;"><strong>Datum početka članarine:</strong> 
                <?php echo isset($user['datum_pocetka']) && $user['datum_pocetka'] != null ? htmlspecialchars($user['datum_pocetka']) : 'Nije dostupno'; ?>
            </p>
            <p style="color: white; background-color: black;"><strong>Datum isteka članarine:</strong> 
                <?php echo isset($user['datum_kraja']) && $user['datum_kraja'] != null ? htmlspecialchars($user['datum_kraja']) : 'Nije dostupno'; ?>
            </p>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
