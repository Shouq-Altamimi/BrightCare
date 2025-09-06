<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php'; 

// Check session and role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
 header("Location: login.html");
    exit();
}

$doctorID = $_SESSION['user_id'];

// Get doctor info
$doctorQuery = $conn->prepare("
SELECT d.firstName, d.lastName, d.id, d.emailAddress, s.speciality , d.uniqueFileName
FROM Doctor d
JOIN Speciality s ON d.SpecialityID = s.id
WHERE d.id = ?

");
$doctorQuery->bind_param("s", $doctorID);
$doctorQuery->execute();
$doctorResult = $doctorQuery->get_result()->fetch_assoc();

// Get upcoming appointments
$apptQuery = $conn->prepare("
    SELECT a.id, a.date, a.time, a.reason, a.status,
           p.firstName AS patientFirst, p.lastName AS patientLast, p.DoB, p.Gender
    FROM Appointment a
    JOIN Patient p ON a.PatientID = p.id
    WHERE a.DoctorID = ? AND (a.status = 'Pending' OR a.status = 'Confirmed')
    ORDER BY a.date, a.time
");
$apptQuery->bind_param("s", $doctorID);
$apptQuery->execute();
$apptResult = $apptQuery->get_result();

// Get patients and medications
$patientsQuery = $conn->prepare("
   SELECT 
        p.id,
        p.firstName, 
        p.lastName, 
        p.DoB, 
        p.Gender,
        GROUP_CONCAT(m.MedicationName SEPARATOR ', ') AS medications
    FROM Appointment a
    JOIN Patient p ON a.PatientID = p.id
    LEFT JOIN Prescription pr ON a.id = pr.AppointmentID
    LEFT JOIN Medication m ON pr.MedicationID = m.id
    WHERE a.DoctorID = ? AND a.status = 'Done'
    GROUP BY p.id
    ORDER BY p.firstName ASC
");
$patientsQuery->bind_param("s", $doctorID);
$patientsQuery->execute();
$patientsResult = $patientsQuery->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Home</title>
    <link rel="stylesheet" href="Full pages.css">
          <style>

          /*HEADAR*/
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f8fc;
}

.navbar { 
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #ffffff;
    border-bottom: 2px solid #e5e5e5;
}

.logo {
    display: flex;
    align-items: center;
}

.logo img {
    height: 70px;
    margin-right: 10px;
}

.logo span {
    font-size: 25px;
    font-weight: bold;
    color: #34065f;
}
.logo a {
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    color: inherit; 
}
.menu {
    display: flex;
    gap: 20px;
}

.menu a {
    text-decoration: none;
    color: #333;
    font-size: 16px;
}


header{


height:60px;
}

a:any-link{
    text-decoration: none;
  }
  .Log-Out-button {
    padding: 10px 20px;
    background-color: #34065f;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
}

.Log-Out-button:hover {
    background-color: #0056b3; 
    cursor: pointer;
    text-decoration: underline;
}
  /*END HEADAR*/
/*RANA-------------------------------------------------------------------*/
#welcome {
    float: right;

}
.Rhero-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px;
    background-color:  #f0f4ff;
}

.Rcontent {
    max-width: 600px;
}

#welcomm h1 {
    font-size: 2.5rem;
    margin: 0 0 20px;
    color: #333;
}

.welcomes {
    color: #34065f;
    font-size: 3.5rem;

}

.buttons {
    display: flex;
    gap: 20px;
}
.R-image img {
    max-width: 300px;
    border-radius: 10px;
}

#R-Patient h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 20px;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 1rem;
    text-align: left;
}

.styled-table th,
.styled-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e3e6f0;
}

.styled-table th {
    background-color: #f8f9fc;
    color: #555;
}

.styled-table tr:hover {
    background-color: #f1f5ff;
}

.confirm{
    padding: 8px 12px;
    background-color: #34065f;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
}

.confirm:hover {
    background-color:#34065f;
}

.Rconfirmed {
    color: #28a745;
    font-weight: bold;
}

.prescribe-link {
    color: #34065f;
    text-decoration: none;
    font-weight: bold;
}

.prescribe-link:hover {
    text-decoration: underline;
    cursor: pointer;
}
.R-container {
    max-width: 1300px;
    margin: 50px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.icon {
    width: 40px;
    height: 40px;
    margin-left: 10px;
    vertical-align: middle;
}

.Doctor-info {
    display: flex;
    justify-content: flex-start; 
    align-items: center;
    padding: 15px 20px;
    width:180%;
   
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    gap: 20px; 
}

.Doctor-info div {
    flex: 1 1 200px; 
    display: flex;
    justify-content: flex-start; 
    gap: 20px; 
}
.dvalue {
    font-size: 1rem;
    color: #333;
}
 .footer {
        background-color: #34065f; 
        color: #ffffff;
        padding: 20px 0;
        text-align: center;
        font-size: 14px;
        position: relative; 
        width: 100%;
        margin-top: 500px; 
      }
      
      
    #copy {
color:#ffffff;
      
      }
      .footer a {
        color: #00bcd4;
        text-decoration: none;
        margin: 0 10px;
        font-weight: bold;
        transition: color 0.3s ease;
      }
      
      .footer a:hover {
        color: #00e5ff;
      }

    .f-footer{
        background-color: #34065f; 
        color: #ffffff;
        /*padding: 20px 0;
        bottom: 0px;
        text-align: center;
        font-size: 14px;
        position: fixed; 
        width: 100%;
        margin-top: 0px; /* Adds spacing above the footer */
        text-align: center;
        padding: 20px 10px;
        font-size: 14px;
        position: fixed;
        bottom: 0;
        width: 100%;
      }
      
      .f-footer a {
        color: #00bcd4;
        text-decoration: none;
        margin: 0 10px;
        font-weight: bold;
        transition: color 0.3s ease;
      }
      
      .f-footer a:hover {
        color: #00e5ff;
      }
       </style>
</head>
<body>
<header class="navbar">
    <div class="logo">
        <a href="index.html"><img src="Images/Logo.png" alt="Logo"></a>
        <span>BrightCare</span>
    </div>
    <a href="logout.php"><button class="Log-Out-button">Sign-Out</button></a>
</header>

<section class="Rhero-section">
    <div class="Rcontent">
        <h1 id="welcomm"><span class="welcomes">Welcome!</span><br>Dr. <?= htmlspecialchars($doctorResult['firstName']) ?></h1>
        <div class="Doctor-info">
            <div><strong>First Name:</strong> <?= $doctorResult['firstName'] ?></div>
            <div><strong>Last Name:</strong> <?= $doctorResult['lastName'] ?></div>
            <div><strong>Speciality:</strong> <?= $doctorResult['speciality'] ?></div>
            <div><strong>Email:</strong> <?= $doctorResult['emailAddress'] ?></div>
        </div>
    </div>
    <div class="R-image">
    <img src="<?= htmlspecialchars($doctorResult['uniqueFileName']) ?>" alt="Doctor Photo">
    </div>
</section>

<div class="R-container">
    <h3><img src="Images/sch.png" alt="icon" class="icon" /> Upcoming Appointments</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Date</th><th>Time</th><th>Patient's Name</th><th>Age</th><th>Gender</th><th>Reason</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $apptResult->fetch_assoc()): 
            $birthdate = new DateTime($row['DoB']);
            $age = $birthdate->diff(new DateTime())->y;
            $fullName = $row['patientFirst'] . ' ' . $row['patientLast'];
        ?>
            <tr>
                <td><?= $row['date'] ?></td>
                <td><?= $row['time'] ?></td>
                <td><?= $fullName ?></td>
                <td><?= $age ?></td>
                <td><?= $row['Gender'] ?></td>
                <td><?= $row['reason'] ?></td>
                <td>
                    <?php if ($row['status'] == 'Pending'): ?>
<button class="confirm" data-id="<?= $row['id'] ?>">Confirm</button>
                    <?php elseif ($row['status'] == 'Confirmed'): ?>
                        <a class="prescribe-link" href="prescribe.php?appointment_id=<?= $row['id'] ?>">Prescribe</a>
                    <?php else: ?>
                        <?= $row['status'] ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <br><br>
    <h3><img src="Images/Patients.png" alt="icon" class="icon" /> Your Patients</h3>
    <table class="styled-table">
        <thead>
            <tr><th>Patient's Name</th><th>Age</th><th>Gender</th><th>Medications</th></tr>
        </thead>
        <tbody>
       <?php while ($row = $patientsResult->fetch_assoc()): 
        $age = (new DateTime($row['DoB']))->diff(new DateTime())->y;
        $fullName = $row['firstName'] . ' ' . $row['lastName'];
    ?>
        <tr>
            <td><?= $fullName ?></td>
            <td><?= $age ?></td>
            <td><?= $row['Gender'] ?></td>
            <td><?= $row['medications'] ?? 'No medications' ?></td>
        </tr>
    <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="footer">
    <p id="copy">&copy; 2025 BrightCare. All rights reserved.</p>
    <p>
        <a href="#about">About Us</a> |
        <a href="#services">Services</a> |
        <a href="#contact">Contact</a>
    </p>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.confirm').click(function() {
        const button = $(this);
        const appointmentId = button.data('id');

        $.ajax({
            url: 'ConfirmAppointment.php',
            method: 'POST',
            data: { id: appointmentId },
            success: function(response) {
                if (response.trim() === 'true') {
                    button.replaceWith('<span class="Rconfirmed">Confirmed</span>');
                } else {
                    alert('Confirmation failed.');
                }
            },
            error: function() {
                alert('Error in AJAX request.');
            }
        });
    });
});
</script>

</body>
</html>
