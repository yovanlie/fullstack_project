<?php
require_once("parent.php");

class User extends ParentClass
{
    public function register($fname, $lname, $username, $plain_password, $confirm_password)
    {
        // Validasi input
        if (empty($fname) || empty($lname) || empty($username) || empty($plain_password) || empty($confirm_password)) {
            return "Semua field harus diisi.";
        }
        if ($plain_password !== $confirm_password) {
            return "Password dan Konfirmasi Password tidak sama.";
        }

        // Cek apakah username sudah ada
        $sql = "SELECT * FROM member WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "Username sudah terdaftar. Silakan pilih username yang lain.";
        }

        // Masukkan data ke dalam database
        $sql = "INSERT INTO member (fname, lname, username, password, profile) VALUES (?, ?, ?, ?, 'member')";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssss", $fname, $lname, $username, $plain_password);

        if ($stmt->execute()) {
            return "Registrasi berhasil. Silakan login.";
        } else {
            return "Terjadi kesalahan. Silakan coba lagi.";
        }
    }

    public function login($username, $plain_password)
    {
        // Validasi input
        if (empty($username) || empty($plain_password)) {
            return "Username dan Password harus diisi.";
        }

        // Cek user di database
        $sql = "SELECT * FROM member WHERE username = ? AND password = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ss", $username, $plain_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $this->loginSuccess($user);
            return true;
        } else {
            return "Username atau Password salah.";
        }
    }

    private function loginSuccess($user)
    {
        // Start session and set session variables
        session_start();
        $_SESSION["user_id"] = $user["idmember"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["user_name"] = $user["fname"] . " " . $user["lname"];
        $_SESSION["user_role"] = $user["profile"];
    }
}

