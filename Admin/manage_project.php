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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $deleteSql = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_project.php");
    exit();
}

// Fetch project data
$sql = "SELECT id, name, description, resources, budget, image FROM projects";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 8px 12px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 5px;
            display: inline-block;
            text-align: center;
        }
        .btn-delete {
            background-color: #e74c3c;
        }
        .btn-update {
            background-color: #3498db;
        }
        .btn-back {
            background-color: #2ecc71;
            margin-bottom: 20px;
            display: inline-block;
        }
        .resources-list {
            list-style-type: none;
            padding-left: 0;
        }
        .resources-list li {
            margin-bottom: 5px;
        }
        .image-preview {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .image-preview:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-back">Back to Homepage</a>
        <h1>Manage Projects</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Resources</th>
                    <th>Budget</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                            <td>
                                <?php
                                // Decode the JSON resources data
                                $resources = json_decode($row['resources'], true);
                                if (is_array($resources)) {
                                    echo '<ul class="resources-list">';
                                    foreach ($resources as $resource) {
                                        echo '<li>' . htmlspecialchars($resource['type']) . ': ' . htmlspecialchars($resource['amount']) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo 'No resources listed';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['budget']); ?> KSH</td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Project Image" class="image-preview">
                                <?php else: ?>
                                    No image available
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="update_project.php?id=<?php echo $row['id']; ?>" class="btn btn-update">Update</a>
                                <a href="manage_project.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No projects found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
