<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "construction";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from the database
$sql = "SELECT id, name, description, image FROM projects";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .project {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .project:last-child {
            border-bottom: none;
        }

        .project img {
            max-width: 200px;
            margin-right: 20px;
        }

        .project-details {
            display: flex;
            align-items: center;
        }

        .project-info {
            max-width: 800px;
        }

        .project-info h2 {
            margin: 0 0 10px 0;
        }

        .project-info p {
            margin: 5px 0;
        }

        .project-info a {
            color: #5cb85c;
            text-decoration: none;
        }

        .project-info a:hover {
            text-decoration: underline;
        }

        .back-button {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Project List</h1>
        <a href="index.php" class="back-button">Back to Home</a>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="project">';
                echo '<div class="project-details">';
                if ($row['image']) {
                    echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="Project Image">';
                }
                echo '<div class="project-info">';
                echo '<h2><a href="project_view.php?id=' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</a></h2>';
                echo '<p>' . substr(htmlspecialchars($row['description']), 0, 100) . '...</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No projects found.</p>';
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
