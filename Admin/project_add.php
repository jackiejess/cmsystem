<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectName = $_POST['projectName'];
    $projectDescription = $_POST['projectDescription'];
    
    // Collect and encode resources
    $resourceTypes = $_POST['resource_type'] ?? [];
    $resourceAmounts = $_POST['resource_amount'] ?? [];

    $resources = [];
    foreach ($resourceTypes as $index => $type) {
        $amount = isset($resourceAmounts[$index]) ? $resourceAmounts[$index] : '';
        if (!empty($type) && !empty($amount)) {
            $resources[] = ['type' => $type, 'amount' => $amount];
        }
    }
    $resourcesJson = json_encode($resources);

    $projectBudget = $_POST['projectBudget'];

    // Handle the image upload if a file is provided
    if (isset($_FILES['projectImage']) && $_FILES['projectImage']['error'] == 0) {
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($_FILES["projectImage"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check file size (5MB max)
        if ($_FILES["projectImage"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit;
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }
        
        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES["projectImage"]["tmp_name"], $targetFile)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    } else {
        $targetFile = null;
    }

    // Save project data to the database
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

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO projects (name, description, resources, budget, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $projectName, $projectDescription, $resourcesJson, $projectBudget, $targetFile);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New project created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>
