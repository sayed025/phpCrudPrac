<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeedb";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $name = trim($_POST['name']);
    $designation = trim($_POST['designation']);
    $attendance_rate = floatval($_POST['attendance_rate']);
    $average_task_efficiency = floatval($_POST['average_task_efficiency']);

    $errors = [];

    // Validate inputs
    if (empty($name) || strlen($name) > 100) {
        $errors['name'] = "Name is required and should not exceed 100 characters.";
    }
    if (empty($designation) || strlen($designation) > 100) {
        $errors['designation'] = "Designation is required and should not exceed 100 characters.";
    }
    if ($attendance_rate < 0 || $attendance_rate > 100) {
        $errors['attendance_rate'] = "Attendance Rate must be between 0 and 100.";
    }
    if ($average_task_efficiency < 0 || $average_task_efficiency > 100) {
        $errors['average_task_efficiency'] = "Average Task Efficiency must be between 0 and 100.";
    }

    // If there are errors, redirect back with errors
    if (!empty($errors)) {
        header("Location: edit.php?id=$employee_id&errors=" . urlencode(json_encode($errors)) . "&old_values=" . urlencode(json_encode($_POST)));
        exit();
    }

    // Proceed with update
    $sql = "UPDATE employees SET name = ?, designation = ?, attendance_rate = ?, average_task_efficiency = ? WHERE employee_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssddi", $name, $designation, $attendance_rate, $average_task_efficiency, $employee_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: employee.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
