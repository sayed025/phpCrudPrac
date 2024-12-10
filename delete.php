<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeedb";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$employee_id = $_GET['id'];

if (isset($employee_id) && is_numeric($employee_id)) {
    
    $sql = "DELETE FROM employees WHERE employee_id = $employee_id";

    if (mysqli_query($conn, $sql)) {

        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid employee ID.";
}


mysqli_close($conn);
