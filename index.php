<?php
// Redirect to login page if not logged in
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Management System</title>
    <link rel="stylesheet" href="includes/css/style.css">
</head>
<body>
    <header>
        <h1>users dashboard</h1>
        <h1>Construction Management System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Homepage</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="intro">
            <h2>Welcome to the Construction Management System</h2>
            <p>This is the place where you can manage all your construction projects effectively.</p>
            <p>Explore our features and start managing your projects today!</p>
        </section>

        <section class="info">
            <h2>What We Offer</h2>
            <p>Our system provides comprehensive tools to help you with project management, including tracking progress, managing resources, and communication.</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Construction Management System</p>
    </footer>
</body>
</html>
