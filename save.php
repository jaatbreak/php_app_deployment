<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve database connection details from environment variables
$server = getenv('DB_HOST') . ':' . getenv('DB_PORT');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Create connection
$con = mysqli_connect($server, $username, $password, $dbname);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve form data
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate that required fields are not empty
if (empty($name) || empty($email) || empty($phone) || empty($city) || empty($password)) {
    header("Location: error.php?message=" . urlencode("All fields are required."));
    exit();
}

// Check for duplicate email
$check_duplicate_sql = "SELECT * FROM `loginform2` WHERE `email` = ?";
$stmt = $con->prepare($check_duplicate_sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Redirect to error page with duplicate data message
    header("Location: error.php?message=" . urlencode("Error occurred: Email already exists."));
    exit();
}

// Insert data into database
$insert_sql = "INSERT INTO `loginform2` (`name`, `email`, `phone`, `city`, `password`) VALUES (?, ?, ?, ?, ?)";
$stmt = $con->prepare($insert_sql);
$stmt->bind_param('sssss', $name, $email, $phone, $city, $password);

if ($stmt->execute()) {
    // Redirect to success page
    header("Location: success.php");
    exit();
} else {
    // Redirect to error page with the MySQL error message
    header("Location: error.php?message=" . urlencode("Error occurred: " . $stmt->error));
    exit();
}

// Close statement and connection
$stmt->close();
$con->close();

?>

