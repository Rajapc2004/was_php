<?php
// Database configuration
$host = "localhost";
$dbname = "was";
$username = "root";
$password = "";

// Establish database connection with charset
$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Database connection failed");
}

$mess = ""; // Message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Validate email format and check for SQL injection
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || 
        preg_match("/(UNION|SELECT|INSERT|DELETE|UPDATE|DROP|WHERE|--|\bOR\b|\bAND\b)/i", $name . $email . $message)) {
        $mess = "<p class='error'>Invalid input detected!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_form (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $mess = "<p class='success'>Message sent successfully!</p>";
        } else {
            $mess = "<p class='error'>Invalid input detected!</p>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Contact Form</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

label {
    display: block;
    text-align: left;
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
}

input, textarea {
    width: 100%;
    padding: 12px;
    margin: 8px 0 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

input[type="email"] {
    height: 40px;
}

textarea {
    height: 120px;
    resize: vertical;
}

button {
    background-color:rgb(89, 28, 210);
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #218838;
}

.success {
    color: green;
    font-weight: bold;
    margin-top: 15px;
}

.error {
    color: red;
    font-weight: bold;
    margin-top: 15px;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Contact Us</h2>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="message">Feedback:</label>
            <textarea id="message" name="message" required></textarea>
            <button type="submit">Submit</button>
        </form>
        <?php
        if ($mess) {
            echo $mess;
        }
        ?>
    </div>
</body>
</html>
