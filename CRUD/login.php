<?php
    // jalankan sessionnya - biasakan jalankan dulu sessionnya taro di atas
    session_start();
    // panggil function taro diatas biar bisa berfungsi
    require 'function.php';
    // set cookie dulu sebelum session
    if ( isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
        $id  = $_COOKIE['id'];
        $key = $_COOKIE['key'];

        // ambil username berdasarkan ID
        $result = mysqli_query($db, "SELECT username FROM user WHERE id = $id");
        $row = mysqli_fetch_assoc($result); // ambil username dari table user simpan di variable $row

        // cek cookie dan username sama apa tidak?
        // key adalah username yg sudah diacak!
        if ( $key === hash('sha256', $row['username']) ) {
            $_SESSION["login"] = true; // jika cookienya ada masuk ke session login
        }
    }
    // kalo sudah login, jangan tampilkan halaman login
    if ( isset($_SESSION["login"])) {
        header("Location: index.php");
        exit;
    }
    // cek apakah tombol submit sudah ditekan apa belum?
    if ( isset($_POST["login"])) {
        // menangkap username dan password dari POST
        $username = $_POST["username"];
        $password = $_POST["password"];

        // cek username di db
        $result = mysqli_query($db, "SELECT * FROM user WHERE username = '$username'");

        // cek username -> kalo ada
        if ( mysqli_num_rows($result) === 1) { // APAKAH ada berapa baris yang dikembalikan dari fungsi di DB, JIKA HASILNYA SAMA DENGAN 1(KALO ADA)
            // cek password
            $row = mysqli_fetch_assoc($result); // artinya DIDALAM FUNGSI ROW ADA datanya DI DB, ID USERNAME dan PASSWORD
            if (password_verify($password, $row["password"])) { // ngecek sebuah string sama ga dengan hashnya, kebalikan dari password hash = untuk mengacak string jd hash
                // set session
                $_SESSION["login"] = true; // variable session keynya login diisi boolean

                // tambah pengecekan - jika remembernya di cek set cookie
                if (isset($_POST["remember"])) {
                    // set cookie dengan algoritma hash
                    // id dari $row table user dan key dari username dan acak menggunakan has
                    // tambahkan time expirednya 1menit, harus sama
                    setcookie('id', $row['id'], time() + 60);
                    setcookie('key', hash('sha256', $row['username']), time() + 60); // ngambil dari username yg sudah di hash
                }
                header("Location: index.php");
                exit;
            }
        }

        $error = true;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="" />
    <script src=""></script>
</head>
<body>
    <?php if( isset($error)) : ?>
    <p style="color: red; font-style: italic;">username & password SALAH!</p>
    <?php endif; ?>

    <form action="" method="post">
            <label for="username"></label>
            USERNAME: <input type="text" name="username" id="username" required><br>
            <label for="password"></label>
            PASSWORD: <input type="password" name="password" id="password" required><br>
            <label for="remember"></label>
            REMEMBER <input type="checkbox" name="remember" id="remember"><br>
            <button type="submit" name="login">Login!</button>
    </form>
</body>
</html>