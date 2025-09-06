<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $user_type = $_POST['user_type'];

    if ($user_type == 'patient') {
        $gender = ucfirst(strtolower($_POST['gender'])); 
        $dob = $_POST['dob'];

        $check_email = $conn->prepare("SELECT id FROM Patient WHERE emailAddress = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already exists!'); window.location.href = 'signup.php';</script>";
            exit();
        }

        
        $stmt = $conn->prepare("INSERT INTO Patient (firstName, lastName, Gender, DoB, emailAddress, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $gender, $dob, $email, $password);
    } 
    else if ($user_type == 'doctor') {
        $speciality = intval($_POST['speciality']); 

        $photo = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];

        
        $upload_dir = "uploads";


        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

   
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            die("<script>alert('File upload error: " . $_FILES['photo']['error'] . "'); window.location.href = 'signup.php';</script>");
        }

       
        $unique_filename = uniqid() . "_" . basename($photo);
        $photo_path = $upload_dir . "/" . $unique_filename;

       
        if (!move_uploaded_file($photo_tmp, $photo_path)) {
            die("<script>alert('File upload failed!'); window.location.href = 'signup.php';</script>");
        }

        $check_email = $conn->prepare("SELECT id FROM Doctor WHERE emailAddress = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already exists!'); window.location.href = 'signup.php';</script>";
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO Doctor (firstName, lastName, uniqueFileName, SpecialityID, emailAddress, password) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("<script>alert('SQL error: " . $conn->error . "'); window.location.href = 'signup.php';</script>");
        }

        $stmt->bind_param("sssiss", $first_name, $last_name, $photo_path, $speciality, $email, $password);
    }

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_type'] = $user_type;

        if ($user_type == 'patient') {
            header("Location: Patient_HomePage.php");
        } else {
            header("Location: doctor_home.php");
        }
        exit();
    } else {
        echo "<script>alert('Error signing up: " . $stmt->error . "'); window.location.href = 'signup.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Full pages.css">

    <style>
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f8fc;
}
        .body-m {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background-color: #f5f8fc;
        }
        .role-selection-m {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 20px;
            
        }
        .form-container-m {
            display: none;
          
            max-width: 400px;
    margin: 50px auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 2px rgba(114,114,114,1);
   
        }
        .forms-p {
            display: inline-block;
            text-align: left;
            position: relative;
            left: -57px;

        }
        .forms-d {
            display: inline-block;
            text-align: left;
            position: relative;
            left: -40px;

        }
        .labels {
            display: block;
            margin: 10px 0 5px;
            
        }
        .inputs , .selects {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            padding: 10px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius:5px;
        }
        
        .buttons-m {
            width: 100%;
            padding: 10px;
            background-color: #34065f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .buttons-m:hover {
background-color: #0056b3;
        }
       .links-m{
color: white;

       }
       .buttons-p-d{

        padding: 10px 20px;
    background-color: #34065f;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size:16px;
    
       }
        /*HEADAR*/
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
  
  /*FOOTER*/


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
    /* END FOOTER*/
        </style>
</head>
<header class="navbar">
    <div class="logo">
        <a href="index.html">
        <img src="Images/Logo.png" alt="Logo"> </a>
        <span>BrightCare</span>
    </div>

  </header>
<body >
  <div class="body-m">
      <h1 style="font-size: 50px;">Sign Up</h1>
      <div class="role-selection-m">
          <label class="labels" >
            <button class="buttons-p-d" onclick="showForm('patient')">Patient</button>
             
          </label>

          
          <label class="labels">
            <button class="buttons-p-d" onclick="showForm('doctor')">Doctor</button>
              
          </label>
      </div>
  
      <div id="patient-form" class="form-container-m">
    <form class="forms-p" id="patient-form" action="signup.php" method="POST">
        <input type="hidden" name="user_type" value="patient">

        <label class="labels" for="patient-first-name">First Name:</label>
        <input class="inputs" type="text" id="patient-first-name" name="first_name" required>

        <label class="labels" for="patient-last-name">Last Name:</label>
        <input class="inputs" type="text" id="patient-last-name" name="last_name" required>

        <label class="labels" for="patient-id">ID:</label>
        <input class="inputs" type="text" id="patient-id" name="id" required>

        <label class="labels" for="patient-gender">Gender:</label>
        <select class="selects" id="patient-gender" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <label class="labels" for="patient-dob">Date of Birth:</label>
        <input class="inputs" type="date" id="patient-dob" name="dob" required>

        <label class="labels" for="patient-email">Email:</label>
        <input class="inputs" type="email" id="patient-email" name="email" required>

        <label class="labels" for="patient-password">Password:</label>
        <input class="inputs" type="password" id="patient-password" name="password" required>

        <button class="buttons-m" type="submit">Sign Up</button>

        <br><br>
        <div class="links">
            <p>Do you already have an account? <a href="login.html">Login</a></p>
        </div>
    </form>
</div>

  
      <div id="doctor-form" class="form-container-m">
<form class="forms-d" id="doctor-form" action="signup.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_type" value="doctor">

        <label class="labels" for="doctor-first-name">First Name:</label>
        <input class="inputs" type="text" id="doctor-first-name" name="first_name" required>

        <label class="labels" for="doctor-last-name">Last Name:</label>
        <input class="inputs" type="text" id="doctor-last-name" name="last_name" required>

        <label class="labels" for="doctor-id">ID:</label>
        <input class="inputs" type="text" id="doctor-id" name="id" required>

        <label class="labels" for="doctor-photo">Photo:</label>
        <input class="inputs" type="file" id="doctor-photo" name="photo" accept="image/*" required>

        <label class="labels" for="doctor-speciality">Speciality:</label>
        <select class="selects" id="doctor-speciality" name="speciality" required>
            <option value="1">General Practitioner</option>
            <option value="2">Cardiology</option>
            <option value="3">Dermatology</option>
            <option value="4">Neurology</option>
            <option value="5">Pediatrics</option>
        </select>

        <label class="labels" for="doctor-email">Email:</label>
        <input class="inputs" type="email" id="doctor-email" name="email" required>

        <label class="labels" for="doctor-password">Password:</label>
        <input class="inputs" type="password" id="doctor-password" name="password" required>

        <button class="buttons-m" type="submit">Sign Up</button>

        <br><br>
        <p>Do you already have an account? <a href="login.html">Login</a></p>
    </form>
</div>

  
      <script>
          function showForm(role) {
              document.getElementById('patient-form').style.display = role === 'patient' ? 'block' : 'none';
              document.getElementById('doctor-form').style.display = role === 'doctor' ? 'block' : 'none';
          }
          function validateForm(event, formId, redirectUrl) {
      event.preventDefault(); 

      let form = document.getElementById(formId);
      let inputs = form.querySelectorAll("input[required], select[required]");
      let isValid = true;

      inputs.forEach(input => {
          if (!input.value.trim()) {
              isValid = false;
              input.style.border = "2px solid blue"; 
          } else {
              input.style.border = "1px solid #ccc"; 
          }
      });

      if (isValid) {
        
          window.location.href = redirectUrl; 
      } else {
          alert("Please fill in all required fields.");
      }
  }


          
      </script>
      </div>
      </body>
<footer>
    <div class="footer">
        <p id="copy">&copy; 2025 BrightCare. All rights reserved.</p>
        <p>
            <a href="#about">About Us</a> |
            <a href="#services">Services</a> |
            <a href="#contact">Contact</a>
        </p>
      </div>
    </footer>

</html>


