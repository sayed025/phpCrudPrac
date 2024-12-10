<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeedb";


$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$employee_id = $_POST['employee_id'];
$name = $_POST['name'];
$designation = $_POST['designation'];
$attendance_rate = $_POST['attendance_rate'];
$average_task_efficiency = $_POST['average_task_efficiency'];


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
