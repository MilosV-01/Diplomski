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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MLD GYM - O Nama</title>
    <link rel="stylesheet" href="icon.css">
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="script.js" defer></script>
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
    <div class="container">
        <div class="o_nama">
            <h1 style="color: white; text-align:center;">O NAMA</h1>
            <p>
                <b>Dobrodošli na naš sajt, vašu destinaciju broj jedan za personalizovane planove treninga!</b><br><br>
                Mi smo tim posvećenih stručnjaka za fitnes koji veruju da svako zaslužuje priliku da dostigne svoje
                ciljeve, bilo da su vezani za gubitak težine, izgradnju mišića, poboljšanje kondicije ili jednostavno vođenje
                zdravijeg načina života. Sa dugogodišnjim iskustvom u oblasti sporta i rekreacije, razumemo koliko je
                važno imati pravilan plan i podršku na putu ka postizanju tih ciljeva.<br><br>

                <b>Naša misija</b><br><br>
                Naša misija je jednostavna: pomoći vam da ostvarite svoj puni potencijal kroz prilagođene i efikasne
                trening planove.<br><br>

                <b>Naš tim</b><br><br>
                Naš tim čine licencirani fitnes treneri, nutricionisti i stručnjaci za zdravlje i wellness, koji su
                posvećeni pružanju vrhunske usluge i podrške. Sa strašću za fitnes i posvećenošću našim klijentima,
                svakodnevno radimo na tome da vam obezbedimo najnovije i najefikasnije metode treninga.<br><br>
                
                <img src="images/team.jpg" alt="tim slika" height="150px" width="250px" style="text-align: center"><br><br>

                <b>Zašto mi?</b><br><br>
                Personalizovani pristup: Svaki naš plan je prilagođen vašim jedinstvenim potrebama i ciljevima.<br>
                Stručnost: Naš tim se sastoji od iskusnih i licenciranih stručnjaka.<br>
                Podrška: Pružamo kontinuiranu podršku i motivaciju kako biste ostali na pravom putu.<br>
                Rezultati: Naši planovi su dokazano efikasni i pomažu klijentima da postignu željene rezultate.<br><br>

                <b>Naši programi</b><br><br>
                Bez obzira da li ste početnik ili iskusni sportista, naši programi su dizajnirani da vam pomognu da
                napredujete i postignete svoje ciljeve. Nudimo različite planove koji uključuju:<br><br>

                • Planovi za gubitak težine<br>
                • Planovi za izgradnju mišića<br>
                • Planovi za poboljšanje kondicije<br><br>

                <b>Kontaktirajte nas</b><br><br>
                Ako ste spremni da započnete svoje fitnes putovanje, ili imate bilo kakva pitanja, slobodno nas
                kontaktirajte. Tu smo da vam pomognemo da postignete svoje ciljeve i živite zdravijim i srećnijim
                životom.<br><br>
                <a href="mailto:vukmirovicm48@gmail.com">vukmirovicm48@gmail.com</a><br><br>

                <b>Hvala vam što ste posetili naš sajt. Radujemo se što ćemo biti deo vašeg puta ka zdravijem i snažnijem
                telu!</b>
            </p>
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

</body>
</html>
