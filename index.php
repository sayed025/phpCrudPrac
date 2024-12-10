<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeedb";

$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if a search term is entered
$search_term = isset($_POST['search']) ? $_POST['search'] : '';

// Get sorting parameters
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'name';
$sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc' ? 'desc' : 'asc';

// Pagination parameters
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Modify the SQL query based on the search term and sorting
if ($search_term) {
    $sql = "SELECT employee_id, name, designation, attendance_rate, average_task_efficiency FROM employees WHERE name LIKE ? ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
    $stmt = mysqli_prepare($conn, $sql);
    $search_param = "%" . $search_term . "%";
    mysqli_stmt_bind_param($stmt, "s", $search_param);
} else {
    $sql = "SELECT employee_id, name, designation, attendance_rate, average_task_efficiency FROM employees ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
    $stmt = mysqli_prepare($conn, $sql);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get the total number of employees for pagination
$total_sql = "SELECT COUNT(*) as total FROM employees";
$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .search-container {
            width: 80%;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-container input[type="text"] {
            padding: 8px;
            width: 80%;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-container button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        .employee-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .employee-table th,
        .employee-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .employee-table th {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            position: relative;
        }

        .employee-table td {
            background-color: #f9f9f9;
        }

        .employee-table tr:hover td {
            background-color: #f1f1f1;
        }

        .edit-btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            padding: 8px 12px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .delete-btn:hover {
            background-color: #e53935;
        }

        .employee-table th a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .sort-arrow {
            margin-left: 5px;
            font-size: 12px;
        }

        .sorted-asc .sort-arrow:before {
            content: "▲";
        }

        .sorted-desc .sort-arrow:before {
            content: "▼";
        }

        .employee-table th a:hover {
            background-color: #45a049;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .pagination button:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }

        .pagination button:hover:not(:disabled) {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <!-- Search Bar -->

    <form action="index.php" method="POST">
        <div class="search-container">
            <input type="text" name="search" placeholder="Search by Name" value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit">Search</button>
        </div>
    </form>


    <!-- Employee  -->
    <?php
    if (mysqli_num_rows($result) > 0) {

        echo "<table class='employee-table'>";
        echo "<thead>";
        echo "<tr>";
        // Add sorting links with arrows
        echo "<th class='" . ($sort_column == 'employee_id' ? 'sorted-' . $sort_order : '') . "'><a href='index.php?sort_column=employee_id&sort_order=" . ($sort_column == 'employee_id' && $sort_order == 'asc' ? 'desc' : 'asc') . "'>ID<span class='sort-arrow'></span></a></th>";
        echo "<th class='" . ($sort_column == 'name' ? 'sorted-' . $sort_order : '') . "'><a href='index.php?sort_column=name&sort_order=" . ($sort_column == 'name' && $sort_order == 'asc' ? 'desc' : 'asc') . "'>Name<span class='sort-arrow'></span></a></th>";
        echo "<th class='" . ($sort_column == 'designation' ? 'sorted-' . $sort_order : '') . "'><a href='index.php?sort_column=designation&sort_order=" . ($sort_column == 'designation' && $sort_order == 'asc' ? 'desc' : 'asc') . "'>Designation<span class='sort-arrow'></span></a></th>";
        echo "<th class='" . ($sort_column == 'attendance_rate' ? 'sorted-' . $sort_order : '') . "'><a href='index.php?sort_column=attendance_rate&sort_order=" . ($sort_column == 'attendance_rate' && $sort_order == 'asc' ? 'desc' : 'asc') . "'>Attendance Rate<span class='sort-arrow'></span></a></th>";
        echo "<th class='" . ($sort_column == 'average_task_efficiency' ? 'sorted-' . $sort_order : '') . "'><a href='index.php?sort_column=average_task_efficiency&sort_order=" . ($sort_column == 'average_task_efficiency' && $sort_order == 'asc' ? 'desc' : 'asc') . "'>Average Task Efficiency<span class='sort-arrow'></span></a></th>";
        echo "<th>Edit</th>";
        echo "<th>Delete</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        // Output 
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row["employee_id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["designation"] . "</td>";
            echo "<td>" . $row["attendance_rate"] . "%</td>";
            echo "<td>" . $row["average_task_efficiency"] . "%</td>";
            echo "<td><a href='edit.php?id=" . $row["employee_id"] . "' class='edit-btn'>Edit</a></td>";
            echo "<td><a href='delete.php?id=" . $row["employee_id"] . "' class='delete-btn' onclick='return confirmDelete()'>Delete</a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "No employees found.";
    }


    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ?>

    <!-- Pagination-->
    <div class="pagination">
        <button <?php if ($page <= 1) echo 'disabled'; ?> onclick="window.location.href='index.php?page=<?php echo $page - 1; ?>&sort_column=<?php echo $sort_column; ?>&sort_order=<?php echo $sort_order; ?>'">Previous</button>
        <button <?php if ($page >= $total_pages) echo 'disabled'; ?> onclick="window.location.href='index.php?page=<?php echo $page + 1; ?>&sort_column=<?php echo $sort_column; ?>&sort_order=<?php echo $sort_order; ?>'">Next</button>
    </div>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this employee?");
        }
    </script>
</body>

</html>