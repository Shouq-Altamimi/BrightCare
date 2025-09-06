<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    echo 'false';
    exit();
}

if (isset($_GET['id'])) {
    $appointmentId = $_GET['id'];

    $delPresc = $conn->prepare("DELETE FROM Prescription WHERE AppointmentID = ?");
    $delPresc->bind_param("i", $appointmentId);
    $delPresc->execute();

    $stmt = $conn->prepare("DELETE FROM Appointment WHERE id = ? AND PatientID = ?");
    $stmt->bind_param("ii", $appointmentId, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo 'true';
        exit();
    }
}

echo 'false';
?>
