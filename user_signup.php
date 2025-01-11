<?php
session_start();
$title = "User Signup";
require "./template/header.php";
require "./functions/database_functions.php";

$conn = db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input fields
    $firstname = mysqli_real_escape_string($conn, trim($_POST['firstname']));
    $lastname = mysqli_real_escape_string($conn, trim($_POST['lastname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $zipcode = mysqli_real_escape_string($conn, trim($_POST['zipcode']));

    // Check for empty fields
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($address) || empty($city) || empty($zipcode)) {
        header("Location: signup.php?signup=empty");
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup.php?signup=invalidemail");
        exit();
    }

    // Check if the email already exists in the database
    $findUserQuery = "SELECT * FROM customers WHERE email = '$email'";
    $findUserResult = mysqli_query($conn, $findUserQuery);

    if (mysqli_num_rows($findUserResult) == 0) {
        // If the email does not exist, insert the new user
        $insertUserQuery = "INSERT INTO customers (firstname, lastname, email, address, password, city, zipcode) VALUES 
                            ('$firstname', '$lastname', '$email', '$address', '$password', '$city', '$zipcode')";
        $insertUserResult = mysqli_query($conn, $insertUserQuery);

        if (!$insertUserResult) {
            echo "Can't add new user: " . mysqli_error($conn);
            exit();
        }

        // Redirect to the sign-in page
        header("Location: signin.php");
    } else {
        // If the email already exists, redirect to the sign-in page
        header("Location: signin.php");
    }
}

// Close the database connection
if (isset($conn)) {
    mysqli_close($conn);
}

require_once "./template/footer.php";
?>
