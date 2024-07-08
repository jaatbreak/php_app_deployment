<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$con = mysqli_connect($server, $username, $password, $dbname);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve form data
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone  = isset($_POST['phone']) ? $_POST['phone'] : '';
$city  = isset($_POST['city']) ? $_POST['city'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate that required fields are not empty
if (empty($name) || empty($email) || empty($phone) || empty($city) || empty($password)) {
    header("Location: error.php?message=" . urlencode("All fields are required."));
    exit();
}

// Check for duplicate email
$check_duplicate_sql = "SELECT * FROM `loginform2` WHERE `email` = '$email'";
$check_duplicate_result = mysqli_query($con, $check_duplicate_sql);

if (mysqli_num_rows($check_duplicate_result) > 0) {
    // Redirect to error page with duplicate data message
    header("Location: error.php?message=" . urlencode("Error occurred: Email already exists."));
    exit();
}

// Insert data into database
$sql = "INSERT INTO `loginform2` (`name`, `email`, `phone`, `city`, `password`) VALUES ('$name', '$email', '$phone', '$city', '$password');";

$result = mysqli_query($con, $sql);

if ($result) {
    // Redirect to success page
    header("Location: success.php");
    exit();
} else {
    // Redirect to error page with the MySQL error message
    header("Location: error.php?message=" . urlencode("Error occurred: " . mysqli_error($con)));
    exit();
}

// Close connection
mysqli_close($con);

?>
