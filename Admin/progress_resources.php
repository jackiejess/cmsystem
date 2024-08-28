<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Updates by Project</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
        }
        .project-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }
        .project-buttons button {
            padding: 12px 20px;
            margin: 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .project-buttons button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }
        th {
            background-color: #f8f9fa;
            color: #495057;
        }
        tr:nth-child(even) {
            background-color: #f1f3f5;
        }
        .back-button {
            text-align: center;
            margin-top: 20px;
        }
        .back-button a {
            display: inline-block;
            padding: 12px 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .back-button a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Resource Updates by Project</h2>

        <div class="project-buttons">
            <?php
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "construction";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch all unique projects with their names
            $sql = "SELECT DISTINCT p.id, p.name 
                    FROM projects p 
                    JOIN resource_updates r ON p.id = r.project_id 
                    ORDER BY p.name ASC";
            $result = $conn->query($sql);

            // Check if query was successful
            if ($result === false) {
                die("Error in SQL query: " . $conn->error);
            }

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<button onclick='showProjectResources(" . $row["id"] . ")'>" . $row["name"] . "</button>";
                }
            } else {
                echo "No projects found.";
            }
            ?>
        </div>

        <table id="resourceTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Project Name</th>
                    <th>Resource Type</th>
                    <th>Amount Used</th>
                    <th>Date Updated</th>
                </tr>
            </thead>
            <tbody id="resourceBody">
                <?php
                if (isset($_GET['project_id'])) {
                    $project_id = intval($_GET['project_id']);
                    $sql = "SELECT r.id, p.name, r.resource_type, r.amount_used, r.updated_at 
                            FROM resource_updates r 
                            JOIN projects p ON r.project_id = p.id 
                            WHERE r.project_id = $project_id 
                            ORDER BY r.updated_at DESC";
                    $result = $conn->query($sql);

                    // Check if query was successful
                    if ($result === false) {
                        die("Error in SQL query: " . $conn->error);
                    }

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["id"] . "</td>
                                    <td>" . $row["name"] . "</td>
                                    <td>" . $row["resource_type"] . "</td>
                                    <td>" . $row["amount_used"] . "</td>
                                    <td>" . $row["updated_at"] . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No records found for this project.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Select a project to view resources.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <div class="back-button">
            <a href="index.php">Back to Index</a>
        </div>
    </div>

    <script>
        function showProjectResources(project_id) {
            window.location.href = "?project_id=" + project_id;
        }
    </script>
</body>
</html>
