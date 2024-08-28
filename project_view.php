<?php
session_start(); // Start session for storing messages

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

// Fetch project data
$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT id, name, description, resources, available_resources, budget, image FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resourceTypes = $_POST['resource_type'] ?? [];
    $resourceAmounts = $_POST['resource_amount'] ?? [];

    $conn->begin_transaction();

    try {
        foreach ($resourceTypes as $index => $type) {
            $amountUsed = isset($resourceAmounts[$index]) ? (float)$resourceAmounts[$index] : 0;
            if (!empty($type) && $amountUsed > 0) {
                // Insert resource update
                $stmt = $conn->prepare("INSERT INTO resource_updates (project_id, resource_type, amount_used) VALUES (?, ?, ?)");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("isd", $projectId, $type, $amountUsed);
                $stmt->execute();
                $stmt->close();

                // Update available_resources
                $availableResources = json_decode($project['available_resources'], true) ?? [];

                $updated = false;
                foreach ($availableResources as &$resource) {
                    if ($resource['type'] === $type) {
                        $resource['amount'] -= $amountUsed;
                        $updated = true;
                        break;
                    }
                }

                if (!$updated) {
                    $availableResources[] = ['type' => $type, 'amount' => -$amountUsed];
                }

                $newAvailableResources = json_encode($availableResources);
                $stmt = $conn->prepare("UPDATE projects SET available_resources = ? WHERE id = ?");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("si", $newAvailableResources, $projectId);
                $stmt->execute();
                $stmt->close();
            }
        }

        $conn->commit();
        $_SESSION['message'] = 'Resources updated successfully!';
        // Redirect to prevent form resubmission on refresh
        header("Location:project_view.php?id=" . $projectId);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = 'Failed to update resources: ' . $e->getMessage();
    }
}

$conn->close();

// Check for success message
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['name']); ?></title>
    <link rel="stylesheet" href="includes/css/projects_view.css">
    <script>
        function addResourceField() {
            const container = document.getElementById('resourceFields');
            const resourceItem = document.createElement('div');
            resourceItem.classList.add('resource-item');
            resourceItem.innerHTML = `
                <div>
                    <label for="resourceType[]">Resource Type:</label>
                    <input type="text" name="resource_type[]" required>
                </div>
                <div>
                    <label for="resourceAmount[]">Amount Used:</label>
                    <textarea name="resource_amount[]" rows="3" placeholder="e.g., 30 bags, 700 tonnes, 120000 bricks, 900 wheelbarrows" required></textarea>
                </div>
                <button type="button" class="delete-resource-btn" onclick="removeResourceField(this)">Delete</button>
            `;
            container.appendChild(resourceItem);
        }

        function removeResourceField(button) {
            const container = document.getElementById('resourceFields');
            container.removeChild(button.parentElement);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($project['name']); ?></h1>
        <div class="project-details">
            <?php if ($project['image']) { ?>
                <img src="uploads/<?php echo htmlspecialchars($project['image']); ?>" alt="Project Image">
            <?php } ?>
            <div class="project-info">
                <h2>Description</h2>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <h2>Resources</h2>
                <div class="resources">
                    <?php
                    // Decode JSON resources and display
                    $resources = json_decode($project['resources'], true);
                    if ($resources) {
                        echo '<ul>';
                        foreach ($resources as $resource) {
                            echo '<li>' . htmlspecialchars($resource['type']) . ': ' . htmlspecialchars($resource['amount']) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No resources available.</p>';
                    }
                    ?>
                </div>
                <h2>Budget</h2>
                <p class="budget"><?php echo htmlspecialchars($project['budget']); ?> KSH</p>
            </div>
        </div>

        <!-- Form to update resources used -->
        <div class="update-resources-form">
            <h2>Update Resources Used</h2>
            <?php if ($message) { ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php } ?>
            <form method="post" action="">
                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
                <div id="resourceFields">
                    <div class="resource-item">
                        <div>
                            <label for="resourceType[]">Resource Type:</label>
                            <input type="text" name="resource_type[]" required>
                        </div>
                        <div>
                            <label for="resourceAmount[]">Amount Used:</label>
                            <textarea name="resource_amount[]" rows="3" placeholder="e.g., 30 bags, 700 tonnes, 120000 bricks, 900 wheelbarrows" required></textarea>
                        </div>
                        <button type="button" class="delete-resource-btn" onclick="removeResourceField(this)">Delete</button>
                    </div>
                </div>
                <button type="button" class="add-resource-btn" onclick="addResourceField()">Add Resource</button>
                <button type="submit">Update Resources</button>
            </form>
        </div>
        <div class="back-link">
            <a href="projects.php">Back to Projects List</a>
        </div>
    </div>
</body>
</html>
