# 💪 Diplomski projekat – Teretana

Ovaj projekat predstavlja web aplikaciju za upravljanje teretanom, razvijen korišćenjem **HTML**, **CSS**, **JavaScript**, **PHP**, i **MySQL**.

---

## 🛠️ Tehnologije

- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Baza podataka: MySQL (PHPMyAdmin)
- Lokalni server: XAMPP

---

## ⚙️ Instalacija (lokalno pokretanje)

1. Preuzmi projekat ili ga kloniraj sa GitHub-a:
   ```bash
   git clone https://github.com/MilosV-01/Diplomski.git
   ```

2. Prebaci ceo folder u `C:\xampp\htdocs` ako već nije tamo.

3. Pokreni **XAMPP** i uključi:
   - Apache
   - MySQL

4. Otvori PHPMyAdmin:  
   [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

5. Kreiraj novu bazu podataka sa imenom:  
   `teretana`

6. Importuj SQL fajl:
   - Klikni na bazu `teretana`
   - Idi na **Import**
   - Odaberi fajl `database/teretana.sql` i klikni **Go**

7. Otvori projekat u browseru:  
   [http://localhost/Diplomski](http://localhost/Diplomski)

---

## 🔌 Konekcija sa bazom

Fajl za konekciju sa bazom nalazi se u:  
`config.php`

Proveri da su podaci tačni:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "teretana";
$conn = new mysqli($host, $user, $password, $database);
```

---

## 📁 Struktura projekta

```
Diplomski/
│
├── config.php              # Konekcija sa bazom
├── index.php               # Početna stranica
├── database/
│   └── teretana.sql        # Baza podataka (export)
├── css/                    # Stilovi
├── js/                     # JavaScript fajlovi
├── ...                     # Ostali PHP fajlovi
└── README.md               # Ovaj fajl
```

---

## 📌 Autor

**Miloš Vukmirović**  
GitHub: [@MilosV-01](https://github.com/MilosV-01)
