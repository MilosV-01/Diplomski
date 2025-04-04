<?php
session_set_cookie_params([
    'lifetime' => 3600, // 1 sat
    'path' => '/',
    'domain' => '', // Ostavite prazno ako ne koristite specifičan domen
    'secure' => false, // Postavite na true ako koristite HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // ili 'Strict' ili 'None'
]);

ini_set('session.gc_maxlifetime', 3600);
session_start();

include "config.php";



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $email = $_POST['email'];
        $lozinka = $_POST['lozinka'];

        $stmt = $conn->prepare("SELECT * FROM klijent WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($lozinka, $row['lozinka'])) {
                $_SESSION['user'] = $row['ime'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['id'] = $row['id']; // Dodaj ID korisnika u sesiju

                if ($row['role'] == 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                echo 'Netačna lozinka.';
            }
        } else {
            echo 'Korisnik sa tim emailom ne postoji.';
        }
        $stmt->close();
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
        $email = $_POST['email'];
        $ime = $_POST['ime'];
        $telefon = $_POST['telefon'];
        $adresa = $_POST['adresa'];
        $lozinka = password_hash($_POST['lozinka'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO klijent (email, ime, telefon, adresa, lozinka, role) VALUES (?, ?, ?, ?, ?, 'klijent')");
        $stmt->bind_param("sssss", $email, $ime, $telefon, $adresa, $lozinka);

        if ($stmt->execute()) {
            $_SESSION['user'] = $ime;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $stmt->insert_id; // Dodaj ID korisnika u sesiju
            header('Location: index.php');
            exit();
        } else {
            echo 'Greška: ' . $stmt->error;
        }
        $stmt->close();
    }



    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }


    if (isset($_POST['kupi'])) {
        if (isset($_SESSION['user'])) {
            $plan_id = intval($_POST['plan_id']);
    
            // Dobij trajanje iz tabele plan
            $sql = "SELECT trajanje FROM plan WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $plan_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($row = $result->fetch_assoc()) {
                $trajanje = $row['trajanje'];
    
                // Pronađi korisnika na osnovu emaila iz sesije
                $sql = "SELECT id FROM klijent WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $_SESSION['email']); // Pronađi korisnika prema emailu
                $stmt->execute();
                $result = $stmt->get_result();
    
                if ($user_row = $result->fetch_assoc()) {
                    $id_korisnika = $user_row['id']; // Korisnički ID
    
                    // Unesi podatke u tabelu klijent_plan
                    $sql = "INSERT INTO klijent_plan (klijent_id, plan_id, datum_pocetka, trajanje) VALUES (?, ?, NOW(), ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iii', $id_korisnika, $plan_id, $trajanje);
    
                    if ($stmt->execute()) {
                        echo "<script>alert('Plan je uspešno kupljen.')</script>";
                    } else {
                        echo "<script>alert('Greška prilikom kupovine plana.')</script>";
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('Korisnik nije pronađen.')</script>";
                }
            } else {
                echo "<script>alert('Plan sa ID-om $plan_id ne postoji.')</script>";
            }
        } else {
            echo "<script>alert('Morate biti ulogovani da biste kupili plan.')</script>";
        }
        // Napomena: Ne zatvarajte $conn ovde; to bi trebalo da bude na kraju skripte.
    }
    



    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // Uzimamo ID iz URL-a i konvertujemo ga u ceo broj

        // Pronađi plan iz baze na osnovu ID-a
        $sql = "SELECT * FROM plan WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Podaci o planu
            $naziv = htmlspecialchars($row['naziv']);
            $opis = htmlspecialchars($row['opis']);
            $trajanje = htmlspecialchars($row['trajanje']);
            $cena = htmlspecialchars($row['cena']);

            // Sadržaj HTML dokumenta
            echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>$naziv</title>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
            </head>
            <body>
                <header class='bg-primary text-white text-center py-3'>
                    <h1 style='margin-top: 2%'>Plan Treninga</h1>
                </header>
                <div class='container mt-5' style='display: block; background-color: orange;'>
                    <h2 style='font-size: 40pt; color: black; text-align: center;'>$naziv</h2><br>
                    <p style='font-size: 40pt; color: white;'>$opis</p>
                    <p style='font-size: 40pt; color: white;'><strong>Trajanje:</strong> $trajanje meseci</p>
                    <p style='font-size: 40pt'><strong>Cena:</strong> $cena RSD</p>
                    <form action='' method='POST'>
                        <input type='hidden' name='plan_id' value='$id'>
                        <button  style='font-size: 40pt' type='submit' name='kupi' class='btn btn-primary'>KUPI</button>
                    </form>
                </div>
            </body>
            </html>";
        } else {
            echo "<p>Plan sa ovim ID-om ne postoji.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>ID plana nije prosleđen.</p>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MLD GYM - Planovi</title>
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
            <a href="#" class="logo">
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
            <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button class="login-btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['user']); ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="client.php">Uredi profil</a>
                        <form action="" method="POST">
                            <button class="dropdown-item" type="submit" name="logout">Odjavi se</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <button class="login-btn">LOG IN</button>
            <?php endif; ?>
        </nav>
    </header>
    <div class="blur-bg-overlay"></div>
    <?php if (!isset($_SESSION['user'])): ?>
    <div class="form-popup">
        <span class="close-btn material-symbols-rounded">close</span>
        <div class="form-box login">
            <div class="form-details">
                <h2 style="color: white">Welcome Back</h2>
                <p>Please log in using your personal information to stay connected with us.</p>
            </div>
            <div class="form-content">
                <h2>LOGIN</h2>
                <form action="" method="POST">
                    <div class="input-field">
                        <input type="text" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="lozinka" required>
                        <label>Password</label>
                    </div>
                    <button type="submit" name="login">Log In</button>
                </form>
                <div class="bottom-link">
                    Don't have an account?
                    <a href="#" id="signup-link">Sign up</a>
                </div>
            </div>
        </div>
        <div class="form-box signup">
            <div class="form-details">
                <h2>Create Account</h2>
                <p>To become a part of our community, please sign up using your personal information.</p>
            </div>
            <div class="form-content">
                <h2>SIGN UP</h2>
                <form action="" method="POST">
                    <div class="input-field">
                        <input type="text" name="email" required>
                        <label>Enter your email</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="ime" required>
                        <label>Enter your name</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="telefon" required>
                        <label>Enter your number</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="adresa" required>
                        <label>Enter your address</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="lozinka" required>
                        <label>Create password</label>
                    </div>
                    <button type="submit" name="signup">Sign Up</button>
                </form>
                <div class="bottom-link">
                    Already have an account? 
                    <a href="#" id="login-link">Login</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <footer>
        <p>© 2024 MLD GYM. Sva prava zadržana.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
