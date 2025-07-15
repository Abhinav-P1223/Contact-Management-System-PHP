# Contact Management System in PHP (CRUD + Admin + Mail)

A complete **Contact Management System** built using **PHP**, **MySQL**, and **PHPMailer**. The project features user registration, email notification using Gmail SMTP, admin access, contact CRUD operations (Create, Read, Update, Delete), and powerful search & filter functionalities with pagination.

---

## 📁 Project Structure Overview

```
crud_app/
├── add.php
├── admin.php
├── admin_login.php
├── config.php
├── delete.php
├── edit.php
├── index.php
├── insert.php
├── login.php
├── logout.php
├── update.php
├── uploads/           # Profile pictures stored here
├── phpmailer/         # PHPMailer library files
├── test.php           # Optional test file
└── README.md
```

---

## ✅ Features

* User Registration with frontend and backend validation
* Profile picture upload
* Automatic email with username and password using **PHPMailer** + Gmail SMTP
* Secure login for users and admin
* Admin dashboard with:

  * Total contacts, Male/Female stats
  * Filter contacts by gender
  * Search contacts by name/email/phone
  * Pagination
  * Edit/Delete contact
* Fully styled with Bootstrap 5

---

## ⚙️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/Abhinav-P1223/Contact-Management-System-PHP.git
cd Contact-Management-System-PHP
```

### 2. Start WAMP/XAMPP Server

* Place the project folder in `www/` (WAMP) or `htdocs/` (XAMPP).
* Start **Apache** and **MySQL**.

### 3. Import the Database

* Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
* Create a new database: `crud_app`
* Import `crud_app.sql` (You need to export it from your current DB and include in repo)

### 4. Configure Database in `config.php`

```php
function getDBConnection() {
    $conn = new mysqli("localhost", "root", "", "crud_app");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    return $conn;
}
```

### 5. Setup PHPMailer (No Composer Needed)

* Ensure PHPMailer library is placed in `/phpmailer/`
* Files used: `PHPMailer.php`, `SMTP.php`, `Exception.php` from `/src`

### 6. Gmail SMTP Email Sending (in `insert.php`)

```php
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

> Use Gmail **App Passwords** instead of normal password for SMTP.

---

## 📄 File-by-File Purpose

### `index.php`

* User login page

### `insert.php`

* Handles form submission with backend validation
* Uploads profile pic
* Stores contact in DB
* Sends email with credentials

### `add.php`

* Frontend form with Bootstrap
* Frontend + PHP validation for all fields

### `config.php`

* Central DB connection function

### `admin_login.php`

* Admin login page with session check

### `admin.php`

* Admin dashboard
* Shows all contacts with search, filter, pagination
* Allows edit/delete

### `edit.php`, `update.php`

* Edit contact info and save to DB

### `delete.php`

* Deletes contact and associated image
* Redirects back to admin.php

### `login.php`, `logout.php`

* Manages login sessions for users and admin

### `uploads/`

* Directory to store profile pictures

### `phpmailer/`

* Library used for sending emails via Gmail

---

## 🧪 Frontend + Backend Validations

* **Frontend**: HTML `required`, Bootstrap alerts, JS conditions (e.g., show country input if 'Other')
* **Backend**: PHP validation for each field with error storage in session

---

## 🔎 Admin Search, Filter, Pagination

* Search by `name`, `email`, or `phone`
* Filter by gender
* Paginate 2 contacts per page (modifiable)
* All handled via URL query params

---

## 🔐 Access Control

* Regular users: Can view their profile only
* Admin: Can view all contacts, stats, edit/delete users
* Admin session: `$_SESSION['admin']`

---

## 💡 Future Improvements

* Hash passwords instead of storing plaintext
* Email verification link
* Role-based user permissions
* Export contacts as CSV
* Responsive mobile UI

---

## 👨‍💻 Author

**Pinnamaneni Abhinava Satya Sai**
Email: [pinnamaneniabhinav@gmail.com]
GitHub: [Abhinav-P1223](https://github.com/Abhinav-P1223)



## ✅ Conclusion

This project is a strong full-stack PHP application with real-world features like form handling, admin management, email integrations, and data persistence. Clone, run, customize, and build on top of it!
