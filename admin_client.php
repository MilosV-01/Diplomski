<?php
include "config.php";

// Uplata članarine
if (isset($_POST['plati_clanarinu'])) {
    $id = $_POST['id']; 
    $trajanje = $_POST['trajanje'];  
    
    $datum_pocetka = date("Y-m-d");
    
    $datum_kraja = date("Y-m-d", strtotime("+$trajanje months", strtotime($datum_pocetka)));
    
    $update_sql = "UPDATE klijent SET datum_pocetka = ?, datum_kraja = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $datum_pocetka, $datum_kraja, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: admin_client.php");
        exit();
    } else {
        echo "Greška prilikom ažuriranja članarine.";
    }
}

// Brisanje klijenta
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $delete_sql = "DELETE FROM klijent WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header("Location: admin_client.php");
    exit();
}

// Dodavanje klijenta
if (isset($_POST['submit'])) {
    $ime = $_POST['ime'];
    $email = $_POST['email'];
    $lozinka = password_hash($_POST['lozinka'], PASSWORD_BCRYPT); // Heširanje lozinke
    $telefon = $_POST['telefon'];
    $adresa = $_POST['adresa'];

    $sql = "INSERT INTO klijent (ime, email, lozinka, telefon, adresa) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssss', $ime, $email, $lozinka, $telefon, $adresa);
    mysqli_stmt_execute($stmt);
    header("Location: admin_client.php");
    exit();
}

// Dohvatanje klijenata
$sql = "SELECT * FROM klijent";
$result = mysqli_query($conn, $sql);

// Prikaz nepročitanih poruka
$sql_poruke = "SELECT * FROM kontakt WHERE procitana = 0";
$result_poruke = mysqli_query($conn, $sql_poruke);

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

        <!-- Forma za dodavanje novog klijenta -->
        <div class="card mb-4">
            <div class="card-header">
                Dodaj novog klijenta
            </div>
            <div class="card-body">
                <form action="admin_client.php" method="POST">
                    <div class="form-group">
                        <label for="ime">Ime:</label>
                        <input type="text" class="form-control" id="ime" name="ime" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="lozinka">Lozinka:</label>
                        <input type="password" class="form-control" id="lozinka" name="lozinka" required>
                    </div>
                    <div class="form-group">
                        <label for="telefon">Telefon:</label>
                        <input type="text" class="form-control" id="telefon" name="telefon" required>
                    </div>
                    <div class="form-group">
                        <label for="adresa">Adresa:</label>
                        <input type="text" class="form-control" id="adresa" name="adresa" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">Dodaj klijenta</button>
                </form>
            </div>
        </div>
        <div class="input-group"> 
        <div class="input-group mb-3">
    <input type="text" id="myInput" class="form-control" onkeyup="myFunction()" placeholder="Pretraga" aria-label="Pretraga" aria-describedby="button-addon2">
</div>
</div>
<script>
function myFunction() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementsByTagName("table")[0];
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) { // Kreni od 1 da preskočiš zaglavlje
        tr[i].style.display = "none"; 
        td = tr[i].getElementsByTagName("td");

        for (j = 0; j < td.length; j++) { // Prođi kroz sve kolone
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Prikaz red ako se poklapa pretraga
                    break; // Ako jedan podatak odgovara, prikaži ceo red
                }
            }
        }
    }
}
</script>

<!-- Lista svih klijenata -->
<div class="card mb-4">
    <div class="card-header">
        Svi klijenti
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ime</th>
                    <th>Email</th>
                    <th>Telefon</th>
                    <th>Adresa</th>
                    <th>Datum početka</th>
                    <th>Datum kraja</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['ime']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['telefon']; ?></td>
                    <td><?php echo $row['adresa']; ?></td>
                    <td><?php echo $row['datum_pocetka']; ?></td>
                    <td><?php echo $row['datum_kraja']; ?></td>
                    <td>
                        <form action="admin_client.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" name="delete">Obriši</button>
                        </form>
                        <form action="admin_client.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <select name="trajanje" required>
                                <option value="1">1 mesec</option>
                                <option value="3">3 meseca</option>
                                <option value="6">6 meseci</option>
                                <option value="12">12 meseci</option>
                            </select>
                            <button type="submit" class="btn btn-success btn-sm" name="plati_clanarinu">Plati članarinu</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

        <!-- Lista nepročitanih poruka -->
        <div class="card mb-4">
            <div class="card-header">
                Nepročitane poruke
            </div>
            <div class="card-body">
                <?php while($row_poruka = mysqli_fetch_assoc($result_poruke)) { ?>
                <div class="mb-3">
                    <h5><?php echo $row_poruka['ime']; ?> (<?php echo $row_poruka['email']; ?>)</h5>
                    <p><?php echo $row_poruka['tekst']; ?></p>
                </div>
                <hr>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
