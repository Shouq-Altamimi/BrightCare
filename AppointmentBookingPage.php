<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: HomePage.php");
    exit();
}
 
include 'db_connect.php'; 

$specialities = [];
$specialityQuery = $conn->query("SELECT id, speciality FROM Speciality");
while ($row = $specialityQuery->fetch_assoc()) {
    $specialities[] = $row;
}

$doctors = [];
$selectedSpeciality = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSpeciality = $_POST['speciality'] ?? '';

    if (!empty($selectedSpeciality)) {
        $stmt = $conn->prepare("
            SELECT Doctor.id, Doctor.firstName, Doctor.lastName, Speciality.speciality
            FROM Doctor
            JOIN Speciality ON Doctor.SpecialityID = Speciality.id
            WHERE SpecialityID = ?
        ");
        $stmt->bind_param("i", $selectedSpeciality);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("
            SELECT Doctor.id, Doctor.firstName, Doctor.lastName, Speciality.speciality
            FROM Doctor
            JOIN Speciality ON Doctor.SpecialityID = Speciality.id
        ");
    }
} else {
    $result = $conn->query("
        SELECT Doctor.id, Doctor.firstName, Doctor.lastName, Speciality.speciality
        FROM Doctor
        JOIN Speciality ON Doctor.SpecialityID = Speciality.id
    ");
}

while ($doc = $result->fetch_assoc()) {
    $doctors[] = $doc;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Homepage</title>
    <link rel="stylesheet" href="Full pages.css">
<style>  /*Patient_HomePage*/
.WelcomeAndPI {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.welcomeP h1 {
    font-size: 2.8rem;
    text-align: left;
    color: #34065f;
}

.welcomeP h1 span {
    color: #34065f;
    text-align: left;
}


.patient-info {
    display: flex;
    justify-content: flex-start; 
    align-items: center;
    padding: 15px 20px;
    background-color: #f5f8fc;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    gap: 20px; 
}

.patient-info div {
    flex: 1 1 200px; 
    display: flex;
    justify-content: flex-start; 
    gap: 5px; 
}

.value {
    font-size: 1rem;
    color: black;
}

strong {
    color: #34065f;
    font-weight: bold;
}

.appointment {
	text-align:center;
	max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#book {
    font-size: 14px; 
    padding: 8px 12px; 
    background-color: #34065f; 
    color: white; 
    text-decoration: none; 
    border-radius: 5px; 
    border: none; 
	float: right;
}

#book:hover {
    background-color: #0056b3; 
    cursor: pointer;
    text-decoration: underline;
}
.appointment .cancel {
    color: #34065f;
	text-decoration: none;
	
}

.appointment .cancel:hover {
text-decoration: underline;
cursor: pointer;	
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background-color: #ffffff;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

table th, table td {
    padding: 12px;
    text-align: center;
    font-size: 15px;
    border-bottom: 1px solid #f0f0f0;
}

table td {
    color:black;
}

table th {
    background-color: #f5f8fc;
    color: #34065f;
    font-weight: bold;
	font-size: 1rem;
}


table tr:hover {
    background-color: #f1f1f1;
}

table img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #34065f;
}

#Pen
{
	color: red;
	
}

#Confirm
{
	color: green;
	font-weight:bold;
}

/* Appointment Booking Page */

.bookApp {
    font-size: 38px;
    font-weight: bold;
    text-align: center;
    color: #34065f; 
    margin: 25px 0;
}

.form-container {
    max-width: 60%;
    margin: 30px auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    border-left: 5px solid #34065f; 
}

.form-label {
    margin-bottom: 3px;
}

form select, 
form input[type="date"], 
form input[type="time"], 
form textarea {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #aaa;
    border-radius: 8px;
    font-size: 16px;
    background-color: #f4f7fc;
    display: block;
}


form textarea {
    resize: none;
    height: 50px;
}

.submit-btn {
    background-color: #34065f; 
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 1rem;
    width: 100%;
}

.submit-btn:hover {
    background-color: #0056b3; 
    cursor: pointer;
}         /*HEADAR*/
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
      }</style>
</head>
<body>
    <header class="navbar">
            <div class="logo">
                <a href="index.html">
                <img src="Images/Logo.png" alt="Logo"> </a>
                <span>BrightCare</span>
            </div>
           
    </header>
    <h1 class="bookApp">Book an Appointment</h1>
<br>

<!-- First Form: Speciality Filter -->
<div class="form-container">
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-label">
            <label for="specialty">Select Specialty:</label><br><br>
            <select id="specialty" name="speciality">
                <option value="">-- Select Specialty --</option>
                <?php foreach ($specialities as $row): ?>
                    <option value="<?= $row['id'] ?>" <?= ($row['id'] == $selectedSpeciality ? 'selected' : '') ?>>
                        <?= htmlspecialchars($row['speciality']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
    </form>
</div>


<!-- Second Form: Book Appointment -->
<?php if ($_SERVER['REQUEST_METHOD'] === 'GET' || isset($_POST['speciality'])): ?>
    <div class="form-container">
        <form method="post" action="ConfirmAppointment.php">
            <div class="form-label">
                <label for="doctor">Select Doctor:</label><br><br>
                <!-- 
                    Doctor ID is sent through the <select> input, which meets the requirement. 
                    A hidden input is not needed since the doctor is selected manually by the user.
                -->

                <select id="doctor" name="doctor" required>
                    <option value="">-- Select Doctor --</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?= $doc['id'] ?>">
                            Dr. <?= htmlspecialchars($doc['firstName'] . ' ' . $doc['lastName']) ?> - <?= htmlspecialchars($doc['speciality']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-label">
                <label for="date">Select Date:</label><br><br>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-label">
                <label for="time">Select Time:</label><br><br>
                <input type="time" id="time" name="time" required>
            </div>

            <div class="form-label">
                <label for="reason">Reason for Visit:</label><br><br>
                <textarea id="reason" name="reason" placeholder="Describe your symptoms or reason for visit" required></textarea>
            </div>

            <div class="sub-container">
            <button type="submit" class="submit-btn">Submit</button>
        </div>
        </form>
    </div>
<?php endif; ?>
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
$('#specialty').change(function () {
    const specialityId = $(this).val();

    if (specialityId !== "") {
        $.ajax({
            url: 'get_doctors_by_speciality.php',
            type: 'POST',
            data: { speciality_id: specialityId },
            dataType: 'json',
            success: function (doctors) {
                $('#doctor').empty().append('<option value="">-- Select Doctor --</option>');
                doctors.forEach(function (doc) {
                    $('#doctor').append(`<option value="${doc.id}">Dr. ${doc.firstName} ${doc.lastName}</option>`);
                });
            },
            error: function () {
                alert('Error retrieving doctors.');
            }
        });
    } else {
        $('#doctor').empty().append('<option value="">-- Select Doctor --</option>');
    }
});
</script>

</body>
</html>
