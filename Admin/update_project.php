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

// Get project ID from URL
$id = (int)$_GET['id'];

// Fetch project data
$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update project details
    $name = $_POST['name'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    
    // Handle image upload
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = '../uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        $image = $_POST['existing_image'] ?: $project['image'];
    }

    // Handle resources
    $resources = [];
    if (isset($_POST['resource_type']) && isset($_POST['resource_amount'])) {
        for ($i = 0; $i < count($_POST['resource_type']); $i++) {
            $resources[] = [
                'type' => $_POST['resource_type'][$i],
                'amount' => $_POST['resource_amount'][$i]
            ];
        }
    }

    // Add new resources
    if (isset($_POST['new_resource_type']) && isset($_POST['new_resource_amount'])) {
        for ($i = 0; $i < count($_POST['new_resource_type']); $i++) {
            if (!empty($_POST['new_resource_type'][$i]) && !empty($_POST['new_resource_amount'][$i])) {
                $resources[] = [
                    'type' => $_POST['new_resource_type'][$i],
                    'amount' => $_POST['new_resource_amount'][$i]
                ];
            }
        }
    }

    // Convert resources to JSON
    $resources_json = json_encode($resources);

    // Update the project
    $updateSql = "UPDATE projects SET name = ?, description = ?, budget = ?, image = ?, resources = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssissi", $name, $description, $budget, $image, $resources_json, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the manage projects page
    header("Location: manage_project.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project</title>
    <style>
        /* General Styles */
/* General Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
    margin: 0;
    color: #333;
}

/* Container Styles */
.container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Form Styles */
form {
    display: flex;
    flex-direction: column;
}

label {
    margin-top: 10px;
    font-weight: bold;
    color: #555;
}

input[type="text"], input[type="number"], textarea {
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

textarea {
    resize: vertical;
}

/* Resource Group Styles */
.resource-group {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    align-items: center;
}

.resource-group input[type="text"] {
    flex: 1;
    margin-right: 10px;
}

.resource-group input:last-child {
    margin-right: 0;
}

.resource-group label {
    flex: 0 0 150px;
    padding-right: 10px;
}

/* Button Styles */
.btn {
    padding: 10px;
    background-color: #3498db;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.2s;
    text-align: center;
}

.btn:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

.remove-btn {
    background-color: #e74c3c;
    margin-left: 10px;
}

.remove-btn:hover {
    background-color: #c0392b;
}

/* Image Section Styles */
.image-section {
    margin-top: 20px;
    position: relative;
}

.image-preview {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.image-preview:hover {
    transform: scale(1.05);
}

.image-upload-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
}

.image-upload-wrapper input[type="file"] {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
    background-color: #3498db;
    color: #fff;
}

.image-upload-wrapper input[type="file"]:hover {
    background-color: #2980b9;
}

/* Back Button Styles */
.btn-back {
    background-color: #2ecc71;
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 20px;
    font-size: 16px;
    text-align: center;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-back:hover {
    background-color: #27ae60;
    transform: scale(1.05);
}

    </style>
</head>
<body>
    <div class="container">
        <a href="manage_project.php" class="btn btn-back">Back to Manage Projects</a>
        <h1>Update Project</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="name">Project Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($project['name']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($project['description']); ?></textarea>

            <label for="budget">Budget (KSH):</label>
            <input type="number" id="budget" name="budget" value="<?php echo htmlspecialchars($project['budget']); ?>" required>

            <div class="image-section">
                <label for="image">Image (optional):</label>
                <div class="image-upload-wrapper">
                    <input type="file" id="image" name="image">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($project['image']); ?>">
                    <?php if (!empty($project['image'])): ?>
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="Project Image" class="image-preview">
                    <?php endif; ?>
                </div>
            </div>

            <h3>Resources</h3>
            <?php
            $resources = json_decode($project['resources'], true);
            if (is_array($resources)) {
                foreach ($resources as $index => $resource) {
                    ?>
                    <div class="resource-group">
                        <label>Resource Type:</label>
                        <input type="text" name="resource_type[]" value="<?php echo htmlspecialchars($resource['type']); ?>" required>
                        <label>Resource Amount:</label>
                        <input type="text" name="resource_amount[]" value="<?php echo htmlspecialchars($resource['amount']); ?>" required>
                        <button type="button" class="btn remove-btn" onclick="removeResource(this)">Remove</button>
                    </div>
                    <?php
                }
            }
            ?>

            <h3>Add New Resources</h3>
            <div id="new-resources-container">
                <div class="resource-group">
                    <label>New Resource Type:</label>
                    <input type="text" name="new_resource_type[]" required>
                    <label>New Resource Amount:</label>
                    <input type="text" name="new_resource_amount[]" required>
                    <button type="button" class="btn remove-btn" onclick="removeResource(this)">Remove</button>
                </div>
            </div>

            <button type="button" class="btn" onclick="addNewResource()">Add Another Resource</button>
            <button type="submit" class="btn">Update Project</button>
        </form>
    </div>

    <script>
        function addNewResource() {
            var container = document.getElementById('new-resources-container');
            var newResourceDiv = document.createElement('div');
            newResourceDiv.className = 'resource-group';
            newResourceDiv.innerHTML = `
                <label>New Resource Type:</label>
                <input type="text" name="new_resource_type[]" required>
                <label>New Resource Amount:</label>
                <input type="text" name="new_resource_amount[]" required>
                <button type="button" class="btn remove-btn" onclick="removeResource(this)">Remove</button>
            `;
            container.appendChild(newResourceDiv);
        }

        function removeResource(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>
