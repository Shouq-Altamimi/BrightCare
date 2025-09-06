<?php
session_start();
include 'db_connect.php'; 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: Home page.html");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
if ($currentPage === 'Doctor.php' && $_SESSION['user_type'] !== 'doctor') {
    header("Location: Home page.html");
    exit();
}

if ($currentPage === 'Patient_HomePage.php' && $_SESSION['user_type'] !== 'patient') {
    header("Location: Home page.html");
    exit();
}

$patientId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstName, lastName, DoB, emailAddress, Gender FROM Patient WHERE id = ?");
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$stmt = $conn->prepare("
    SELECT Appointment.id, date, time, status, reason,
           Doctor.firstName AS docFirst, Doctor.lastName AS docLast, Doctor.uniqueFileName
    FROM Appointment
    JOIN Doctor ON Appointment.DoctorID = Doctor.id
    WHERE Appointment.PatientID = ? AND Appointment.status != 'Done'
    ORDER BY STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') ASC
");


$stmt->bind_param("i", $patientId);
$stmt->execute();
$appointments = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Homepage</title>
    <link rel="stylesheet" href="Full pages.css">

</head>
<body>
    <header class="navbar">
            <div class="logo">
                <a href="index.html">
                <img src="Images/Logo.png" alt="Logo"> </a>
                <span>BrightCare</span>
            </div>
            
        <a href="Home page.html" class="Log-Out-button">Sign-Out</a>
    </header>


    <div class="WelcomeAndPI">
        <div class="welcomeP">
            <h1>Welcome, <span><?= htmlspecialchars($patient['firstName']) ?></span></h1>
        </div>
        <br>
        <div class="patient-info">
        <div><strong>First Name:</strong> <?= htmlspecialchars($patient['firstName']) ?></div>
        <div><strong>Last Name:</strong> <?= htmlspecialchars($patient['lastName']) ?></div>
        <div><strong>Email:</strong> <?= htmlspecialchars($patient['emailAddress']) ?></div>
    </div>


    </div>


    <div class="appointment">
    <?php if (isset($_SESSION['booking_success'])): ?>
    <p class="success">Your appointment has been booked successfully!</p>
    <?php unset($_SESSION['booking_success']); ?>
    <?php endif; ?>

        <a href="AppointmentBookingPage.php" id="book">+ Book an Appointment</a>
        <br><br><br>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor's Name</th>
                    <th>Doctor's Photo</th>
                    <th>Status</th>
                    <th>Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td>Dr. <?= htmlspecialchars($row['docFirst'] . ' ' . $row['docLast']) ?></td>
                        <td><img src="<?= htmlspecialchars($row['uniqueFileName']) ?>" alt="Doctor Photo" width="70"></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><a href="#" class="cancel" data-id="<?= $row['id'] ?>">Cancel</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
document.querySelectorAll('.cancel').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();  

        const row = this.closest('tr');
        const appointmentId = this.getAttribute('data-id');

        if (confirm("Are you sure you want to cancel this appointment?")) {
            fetch('cancel_appointment.php?id=' + appointmentId)
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === 'true') {
                        row.remove(); 
                    } else {
                        alert('Failed to cancel appointment.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong.');
                });
        }
    });
});
</script>

    <div class="footer">
        <p id="copy">&copy; 2025 BrightCare. All rights reserved.</p>
        <p>
            <a href="#about">About Us</a> |
            <a href="#services">Services</a> |
            <a href="#contact">Contact</a>
        </p>
      </div>
        
</body>
</html>
