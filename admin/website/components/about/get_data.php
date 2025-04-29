<?php
require_once 'main_db.php';

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to get institutional information using existing $mysqli connection
function getInstitutionalInfo($mysqli) {
    $result = [];

    $query = "SELECT * FROM institutional_info ORDER BY display_order";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $result[$row['category']] = $row;
        }

        $stmt->close();
    } else {
        die("Prepare failed: " . $mysqli->error);
    }

    return $result;
}

// Function to get core values using existing $mysqli connection
function getCoreValues($mysqli) {
    $result = [];

    $query = "SELECT * FROM core_values ORDER BY display_order";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->execute();
        $res = $stmt->get_result();

        $result = $res->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
    } else {
        die("Prepare failed: " . $mysqli->error);
    }

    return $result;
}

// Get the data
$institutionalInfo = getInstitutionalInfo($mysqli);
$coreValues = getCoreValues($mysqli);

// You can now use $institutionalInfo and $coreValues as needed

// Close connection when done (optional here, since script ends)
$mysqli->close();
?>
