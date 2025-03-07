<?php
require '../main_db.php';

try {
    $alumniId = $_POST['alumni_id'];
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];

    $stmt = $mysqli->prepare("SELECT alumni_id FROM alumni WHERE alumni_id_card_no = ? AND last_name = ? AND first_name = ? AND verify = 'unused'");
    $stmt->bind_param("sss", $alumniId, $lastName, $firstName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        $checkUsed = $mysqli->prepare("SELECT verify FROM alumni WHERE alumni_id_card_no = ? AND last_name = ? AND first_name = ? AND verify = 'used'");
        $checkUsed->bind_param("sss", $alumniId, $lastName, $firstName);
        $checkUsed->execute();
        $checkUsed->store_result();

        if ($checkUsed->num_rows > 0) {
            echo json_encode(['exists' => false, 'message' => 'This Alumni ID has already been used for verification.']);
        } else {
            echo json_encode(['exists' => false, 'message' => 'No matching records found.']);
        }
        $checkUsed->close();
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
}
?>