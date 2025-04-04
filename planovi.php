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

$sql = "SELECT * FROM plan";
$result = mysqli_query($conn, $sql);

$sql2 = "SELECT * FROM clanarina";
$result2 = mysqli_query($conn, $sql2);
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
    <div class="container-header">
        <div class="p-3" style="max-width: 900px;">
            <h5 class="text-white text-uppercase">Best Gym Center</h5>
            <h1 class="display-2 text-white text-uppercase mb-md-4">članarina</h1>
        </div>
    </div>
   
    <div class="container mt-5">
        <div class="row justify-content-center">
            <?php
            if (mysqli_num_rows($result2) > 0) {
                while($row = mysqli_fetch_assoc($result2)) {
                    echo '
                    <div class="col-lg-3 col-md-6 mb-4 d-flex align-items-stretch">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">'.htmlspecialchars($row['naziv']).'</h5>
                                <p class="card-text">'.htmlspecialchars($row['opis']).'</p>
                                <p class="card-text"><strong>Cena: '.htmlspecialchars($row['cena']).' RSD</strong></p>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>Nema dostupnih članarina.</p>';
            }
            ?>
        </div>
    </div>

    <div class="container-header">
        <div class="p-3" style="max-width: 900px;">
            <h1 class="display-2 text-white text-uppercase mb-md-4">Planovi</h1>
        </div>
    </div>
    <div class="training">
        <div class="container mt-5">
            <div class="row">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo '
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <p class="card-title">'.htmlspecialchars($row['naziv']).'</p>
                                <p class="small-desc">'.htmlspecialchars($row['opis']).'</p>
                                <p class="small-desc"><small>Trajanje: '.htmlspecialchars($row['trajanje']).'</small></p>
                                <p class="small-desc"><strong>Cena: '.htmlspecialchars($row['cena']).' RSD</strong></p>';

                                
                        if (isset($_SESSION['user'])) {
                            echo '
                                <div class="go-corner">
                                    <div class="go-arrow">
                                        <a href="generate_plan.php?id='.htmlspecialchars($row['id']).'">→</a>
                                    </div>
                                </div>';
                        } else {
                            echo '
                                <div class="go-corner">
                                    <div class="go-arrow">
                                        <a href="#" class="show-login-popup">→</a>
                                    </div>
                                </div>';
                        }
                        
                        echo '
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>Nema dostupnih planova treninga.</p>';
                }
                ?>
            </div>
        </div>
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
                    <a href="#"><i class="fab fa-facebook-f">Fb</i></a>
                    <a href="#"><i class="fab fa-twitter">X</i></a>
                    <a href="#"><i class="fab fa-instagram">Ig</i></a>
                    <a href="#"><i class="fab fa-linkedin-in">Ln</i></a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showLoginPopupLinks = document.querySelectorAll('.show-login-popup');

            showLoginPopupLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    alert('Morate se ulogovati da biste kupili plan.');
                });
            });
        });
    </script>
</body>
</html>
