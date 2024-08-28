<?php
// Redirect to login page if not logged in
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login/login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Construction Management System</title>
    <link rel="stylesheet" href="comp/css/admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            display: inline;
            margin: 0 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        main {
            padding: 20px;
        }
        .container {
            width: 23%; /* Adjust width as needed */
            margin: 10px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .button:hover {
            background: #0056b3;
        }
        .container-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="project.php">Projects</a></li>
                <li><a href="user.php">Users</a></li>
                <li><a href="view_feedback.php">Feedback</a></li>
                <li><a href="progress_resources.php">progress</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="container-group">
            <div class="container">
                <section>
                    <h2>Create Projects</h2>
                    <p>Click the button below to manage projects.</p>
                    <a href="project.php" class="button">Projects</a>
                </section>
            </div>

            <div class="container">
                <section>
                    <h2>Manage Users</h2>
                    <p>Click the button below to manage users.</p>
                    <a href="user.php" class="button">Users</a>
                </section>
            </div>

            <div class="container">
                <section>
                    <h2>Manage Projects</h2>
                    <p>Click the button below to manage projects.</p>
                    <a href="manage_project.php" class="button">Manage Projects</a>
                </section>
            </div>

            <div class="container">
                <section>
                    <h2>Logout</h2>
                    <form action="login/logout.php" method="post">
                        <button type="submit" class="button">Logout</button>
                    </form>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
