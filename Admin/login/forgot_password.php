<?php
include '../comp/dbconnect.php';

// Handle form submission for password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['forgot_password'])) {
        // Check if email exists in the database
        $email = $_POST['email'];

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, show the reset password form
            $resetForm = true;
        } else {
            $message = "No user found with that email.";
        }

        $stmt->close();
    } elseif (isset($_POST['reset_password'])) {
        // Update the password
        $email = $_POST['email'];
        $newPassword = $_POST['password'];

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            $message = "Your password has been successfully updated.";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if (!isset($resetForm)): ?>
            <!-- Forgot Password Form -->
            <div class="form-container">
                <h2>Forgot Password</h2>
                <form id="forgotPasswordForm" action="forgot_password.php" method="POST">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    
                    <button type="submit" name="forgot_password">Send Reset Link</button>
                    <?php if (isset($message)): ?>
                        <p><?php echo htmlspecialchars($message); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        <?php else: ?>
            <!-- Reset Password Form -->
            <div class="form-container">
                <h2>Reset Your Password</h2>
                <form id="resetPasswordForm" action="forgot_password.php" method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required>
                    
                    <button type="submit" name="reset_password">Reset Password</button>
                    <?php if (isset($message)): ?>
                        <p><?php echo htmlspecialchars($message); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
