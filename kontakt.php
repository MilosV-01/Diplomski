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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
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
            header('Location: index.php');
        } else {
            echo 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['login'])) {
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

                if ($row['role'] == 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                echo 'Incorrect password.';
            }
        } else {
            echo 'No user found with that email.';
        }
        $stmt->close();
    } elseif (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MLD GYM - Kontakt</title>
    <link rel="stylesheet" href="icon.css">
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap.min.css" rel="stylesheet">
</head>
<body id="kontakt">
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
                        <a class="dropdown-item" href="client.php">Profil</a>
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
    <?php if (!isset($_SESSION['user'])): ?>
        <div class="blur-bg-overlay"></div>
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
    <div class="form-container">
    <div class="form">
        <span class="heading">Pišite nam ili zakažite svoj trening pozivom na broj recepcije.</span>
        <form action="kontakt.php" method="POST">
            <input placeholder="Ime i prezime" id="name" name="name" type="text" class="input" required>
            <input placeholder="Email" id="email" name="email" type="email" class="input" required>
            <input placeholder="Subject" id="subject" name="subject" type="text" class="input" required>
            <textarea placeholder="Unesi tekst" name="tekst" rows="10" cols="30" id="message" class="textarea" required></textarea>
            <div class="button-container">
                <button type="submit" name="posalji" class="send-button">Pošalji</button>
            </div>
        </form>
    </div>
    <a href="tel:+0611415035" style="display: block;font-size: 25pt; text-align: left;"><b>Broj: <i>061/1415-035</i></b></a>
    </div>

    <h1 style="color: white;">Lokacija</h1>
    <div class="maps">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2848.664819553714!2d20.69732197619269!3d44.44003727107565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4750b0905ca7d1ed%3A0x678c946de47a2c8e!2z0JLRg9C60LAg0JrQsNGA0LDRn9C40ZvQsCwg0JzQu9Cw0LTQtdC90L7QstCw0YY!5e0!3m2!1ssr!2srs!4v1724494552737!5m2!1ssr!2srs" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <footer class="footer text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="index.php">Početna</a>
                <a href="o_nama.php">O nama</a>
                <a href="planovi.php">Planovi treninga</a>
                <a href="kontakt.php">Kontakt</a>
            </div>
            <div class="col-md-12 social-icons mt-3">
                <a href="#">Fb<i class="fab fa-facebook-f"></i></a>
                <a href="#">X<i class="fab fa-twitter"></i></a>
                <a href="#">IG<i class="fab fa-instagram"></i></a>
                <a href="#">Ln<i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3">
                <p>&copy; <?php echo date('Y'); ?> MLD GYM. All rights reserved.</p>
            </div>
        </div>
    </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>

</body>
</html>
