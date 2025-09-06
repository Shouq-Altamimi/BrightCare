<?php
session_start();
include 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !isset($_POST['doctor'])) {
    $appointmentId = $_POST['id'];

    $stmt = $conn->prepare("UPDATE Appointment SET status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $appointmentId);
    $result = $stmt->execute();

    echo $result ? 'true' : 'false';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $appointmentId = $_GET['id'];
    
    $stmt = $conn->prepare("UPDATE Appointment SET status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $appointmentId);
    $result = $stmt->execute();

    if ($result) {
        $_SESSION['confirm_success'] = true;
        header("Location: doctor_home.php");
        exit();  
    } else {
        echo "Failed to confirm appointment. Error: " . $stmt->error;  
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor'])) {
    $pid = $_SESSION['user_id'];
    $did = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];
    $status = 'Pending';

    $stmt = $conn->prepare("INSERT INTO Appointment (PatientID, DoctorID, date, time, reason, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $pid, $did, $date, $time, $reason, $status);
    $stmt->execute();   

    $_SESSION['booking_success'] = true;
    header("Location: Patient_HomePage.php");
    exit();
}
?>
