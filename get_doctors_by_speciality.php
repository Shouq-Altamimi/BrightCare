<?php
include 'db_connect.php';

if (isset($_POST['speciality_id'])) {
    $specialityId = $_POST['speciality_id'];

    $stmt = $conn->prepare("SELECT id, firstName, lastName FROM Doctor WHERE SpecialityID = ?");
    $stmt->bind_param("i", $specialityId);
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode($doctors);
}
?>
