<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: login.html");
    exit();
}


if (!isset($_POST['appointment_id'], $_POST['medications']) || !is_array($_POST['medications'])) {
    echo "Invalid submission.";
    exit();
}

$appointmentID = $_POST['appointment_id'];
$medications = $_POST['medications']; 

$update = $conn->prepare("UPDATE Appointment SET status = 'Done' WHERE id = ?");
if ($update === false) {
    die('Error preparing update query: ' . $conn->error);
}

$update->bind_param("i", $appointmentID);
$update->execute();
$update->close();

$insert = $conn->prepare("INSERT INTO Prescription (AppointmentID, MedicationID) 
                           SELECT ?, ? FROM DUAL 
                           WHERE NOT EXISTS (SELECT 1 FROM Prescription WHERE AppointmentID = ? AND MedicationID = ?)");

if ($insert === false) {
    echo "Error preparing insert query: " . $conn->error;
    exit();
}

foreach ($medications as $medID) {
    if (is_numeric($medID)) {
        $insert->bind_param("iiii", $appointmentID, $medID, $appointmentID, $medID);
        
        echo "Inserting AppointmentID: $appointmentID, MedicationID: $medID<br>";
        
        if (!$insert->execute()) {
            echo "Error executing insert query: " . $insert->error;
        }
    } else {
        echo "Invalid medication ID: $medID<br>";
    }
}

$insert->close();

header("Location: doctor_home.php");
exit();
?>
