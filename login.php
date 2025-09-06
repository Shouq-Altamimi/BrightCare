<?php
session_start();
include 'db_connect.php';

$loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    if ($role === 'patient') {
        $stmt = $conn->prepare("SELECT id, firstName, emailAddress, password FROM Patient WHERE emailAddress = ?");
    } elseif ($role === 'doctor') {
        $stmt = $conn->prepare("SELECT id, firstName, emailAddress, password FROM Doctor WHERE emailAddress = ?");
    } else {
        header("Location: login.html?error=invalidrole");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $role;

            if ($role === 'patient') {
                header("Location: Patient_HomePage.php");
            } elseif ($role === 'doctor') {
                header("Location: doctor_home.php");
            }
            exit();
        } else {
            header("Location: login.html?error=wrongpassword");
            exit();
        }
    } else {
        header("Location: login.html?error=nouser");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>
