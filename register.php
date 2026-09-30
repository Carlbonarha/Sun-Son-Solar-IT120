<?php
// ============================================
// register.php - handles the registration form
// ============================================

// STEP 1: Connect to the database
// Change these 4 values to match your own database setup.
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "myapp";

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// STEP 2: Only continue if the form was actually submitted (POST request)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Grab the values the user typed into the form
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $contact_number = trim($_POST["contact_number"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $errors = []; // we'll collect any problems here

    // STEP 3: Validate the input

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
        // Remove spaces/dashes so "0917 123 4567" and "0917-123-4567" also work
        $contact_number = preg_replace('/[\s\-]/', '', $contact_number);

        // Valid Philippine mobile formats:
        //   09171234567   (11 digits, starts with 09)
        //   +639171234567 (starts with +63 9, then 9 more digits)
        //   639171234567  (same as above without the +)
        if (preg_match('/^09\d{9}$/', $contact_number)) {
            // e.g. 09171234567 -> normalize to +639171234567 for storage
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

    // STEP 4: Check if the email is already registered
    // We use a "prepared statement" (with ?) instead of pasting $email
    // directly into the SQL string, so the database can't be tricked
    // by malicious input (this protects against "SQL injection").
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

    // STEP 5: If everything looks good, save the new user
    if (empty($errors)) {

        // NEVER store plain-text passwords. This scrambles it securely.
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
        // STEP 6: Show the errors back to the user
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
}

$conn->close();