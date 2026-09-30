<?php

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "myapp";

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $contact_number = trim($_POST["contact_number"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($contact_number)) {
        $errors[] = "Contact number is required.";
    } else {
        $contact_number = preg_replace('/[\s\-]/', '', $contact_number);

        if (preg_match('/^09\d{9}$/', $contact_number)) {
            $contact_number = "+63" . substr($contact_number, 1);
        } elseif (preg_match('/^(\+63|63)9\d{9}$/', $contact_number)) {
            $contact_number = "+63" . substr($contact_number, -10);
        } else {
            $errors[] = "Please enter a valid Philippine mobile number (e.g. 09171234567).";
        }
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "An account with that email already exists.";
        }
        $stmt->close();
    }

    if (empty($errors)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, contact_number, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $contact_number, $hashed_password);

        if ($stmt->execute()) {
            echo "Registration successful! You can now log in.";
        } else {
            echo "Something went wrong. Please try again.";
        }

        $stmt->close();

    } else {
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
}

$conn->close();
//ayaw macommit
