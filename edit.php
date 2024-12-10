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

$sql = "SELECT * FROM employees WHERE employee_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $employee_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) > 0) {
    $employee = mysqli_fetch_assoc($result);
} else {
    die("Employee not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Employee</h2>
        <form action="update.php" method="POST">
            <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="designation">Designation:</label>
                <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($employee['designation']); ?>" required>
            </div>

            <div class="form-group">
                <label for="attendance_rate">Attendance Rate (%):</label>
                <input type="number" id="attendance_rate" name="attendance_rate" value="<?php echo htmlspecialchars($employee['attendance_rate']); ?>" step="0.01" min="0" max="100" required>
            </div>

            <div class="form-group">
                <label for="average_task_efficiency">Average Task Efficiency (%):</label>
                <input type="number" id="average_task_efficiency" name="average_task_efficiency" value="<?php echo htmlspecialchars($employee['average_task_efficiency']); ?>" step="0.01" min="0" max="100" required>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>

</html>
<?php

mysqli_close($conn);
?>