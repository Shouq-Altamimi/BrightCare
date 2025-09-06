<?php
session_start();

include 'db_connect.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: login.html");
    exit();
}
 


if (!isset($_GET['appointment_id'])) {
    echo "Missing appointment ID.";
    exit();
}

$appointmentID = $_GET['appointment_id'];

$query = $conn->prepare("
    SELECT p.firstName, p.lastName, p.DoB, p.Gender, a.PatientID
    FROM Appointment a
    JOIN Patient p ON a.PatientID = p.id
    WHERE a.id = ?
");
$query->bind_param("i", $appointmentID);
$query->execute();
$result = $query->get_result()->fetch_assoc();

if (isset($_POST['medications']) && is_array($_POST['medications'])) {
    $medications = $_POST['medications'];
    echo "Selected Medications: <br>";

    foreach ($medications as $medication) {
        echo "Medication: $medication <br>";
    }
}
$age = (new DateTime($result['DoB']))->diff(new DateTime())->y;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prescribe Medication</title>
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
      main {
    max-width: 1000px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 40px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.8em;
    color: #333;
}

form {
    display: flex;
    flex-direction: column;
    gap: 50px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

label {
    font-weight: bold;
    color: #555;
}

input[type="text"],
input[type="number"],

textarea {
    padding: 10px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.gender-options,
.medications-options {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

button.submit-button {
    padding: 10px 20px;
    background-color:#34065f;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 1em;
    border-radius: 20px;


}

#sub-r{

    color:#e3e6f0;
}
button.submit-button:hover {
    background-color: #0056b3; 
}
    </style>
</head>
<body>
<header class="navbar">
    <div class="logo">
        <a href="index.html"><img src="Images/Logo.png" alt="Logo"></a>
        <span>BrightCare</span>
    </div>
</header>

<main>
    <h1>Patient's Medications</h1>
    <form action="submit_prescription.php" method="POST">
        <input type="hidden" name="appointment_id" value="<?= $appointmentID ?>">

        <div class="form-group">
            <label>Patient's Name</label>
            <input type="text" value="<?= $result['firstName'] . ' ' . $result['lastName'] ?>" readonly>
        </div>

        <div class="form-group">
            <label>Age</label>
            <input type="text" value="<?= $age ?>" readonly>
        </div>

        <div class="form-group">
            <label>Gender</label>
            <input type="text" value="<?= $result['Gender'] ?>" readonly>
        </div>

    <!-- Medications (Checkbox) -->
      <div class="form-group">
 <label>Medications</label>
    <div class="medications-options">
      
        <input type="checkbox" name="medications[]" value="1"> Aspirin
        <input type="checkbox" name="medications[]" value="2"> Ibuprofen
        <input type="checkbox" name="medications[]" value="3"> Paracetamol
        <input type="checkbox" name="medications[]" value="4"> Antibiotics
    </div>
    </div>
</div>


        <button type="submit" class="submit-button">Submit</button>
    </form>
</main>

<div class="footer">
    <p id="copy">&copy; 2025 BrightCare. All rights reserved.</p>
    <p><a href="#about">About Us</a> | <a href="#services">Services</a> | <a href="#contact">Contact</a></p>
</div>
</body>
</html>
