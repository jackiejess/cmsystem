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

// Initialize message variable
$message = "";

// Handle form submissions for creating, updating, and deleting users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $role = $_POST['role'];

        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        if ($stmt->execute()) {
            $message = "User created successfully.";
        } else {
            $message = "Error creating user: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        if ($password) {
            $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $username, $email, $role, $password, $id);
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $role, $id);
        }

        if ($stmt->execute()) {
            $message = "User updated successfully.";
        } else {
            $message = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
        } else {
            $message = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Retrieve all users for display
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="comp/css/users.css">
    <style>
        /* Additional CSS for modal and compact containers */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background-image.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .main-container {
            width: 60%;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1, h2 {
            color: #333;
            text-align: center;
        }

        h2 {
            margin-top: 20px;
            font-size: 1.5em;
        }

        form {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            background: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
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
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .btn {
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .update-btn {
            background-color: #28a745;
            color: #fff;
        }

        .update-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .back-btn {
            background-color: #6c757d;
            color: #fff;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal h2 {
            margin-top: 0;
            font-size: 1.25em;
        }

        .modal form {
            padding: 15px;
            background: #f9f9f9;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <h1>Manage Users</h1>

        <div class="form-container">
            <h2>Create New User</h2>
            <form action="user.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value=""></option>
                    <option value="user">User</option>
                </select>

                <!-- Display message if set -->
                <?php if (!empty($message)): ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <button type="submit" name="create" class="btn create-btn">Create User</button>
            </form>
        </div>

        <!-- Display user list -->
        <h2>User List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <button class="btn update-btn" onclick="openModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['username']); ?>', '<?php echo htmlspecialchars($row['email']); ?>', '<?php echo htmlspecialchars($row['role']); ?>')">Update</button>
                        <form action="user.php" method="post" class="action-form" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <button class="btn back-btn" onclick="window.location.href='index.php'">Back to Index</button>
    </div>

    <!-- Modal for updating user -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Update User Details</h2>
            <form id="updateForm" action="user.php" method="post">
                <input type="hidden" id="updateId" name="id">
                <label for="updateUsername">Username:</label>
                <input type="text" id="updateUsername" name="username">

                <label for="updateEmail">Email:</label>
                <input type="email" id="updateEmail" name="email">

                <label for="updatePassword">Password (leave blank to keep current):</label>
                <input type="password" id="updatePassword" name="password">

                <label for="updateRole">Role:</label>
                <select id="updateRole" name="role">
                    <option value=""></option>
                    <option value="user">User</option>
                </select>

                <button type="submit" name="update" class="btn update-btn">Update User</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, username, email, role) {
            document.getElementById('updateId').value = id;
            document.getElementById('updateUsername').value = username;
            document.getElementById('updateEmail').value = email;
            document.getElementById('updateRole').value = role;
            document.getElementById('updateModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            if (event.target === document.getElementById('updateModal')) {
                closeModal();
            }
        }

        // Prevent form resubmission on refresh
        if (window.performance.navigation.type === 1) {
            window.history.replaceState(null, '', window.location.href.split('?')[0]);
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
