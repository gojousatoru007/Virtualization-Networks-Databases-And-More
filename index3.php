<?php

$host = "192.168.61.142";
$database = "myapp";
$user = "myuser";
$password = "mypassword";

// Connect to PostgreSQL database
$conn = pg_connect("host=$host dbname=$database user=$user password=$password");

// Check connection
if (!$conn) {
  die("Connection failed: " . pg_last_error());
}

// If the user has submitted the registration form, insert the record into the database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
  $name = $_POST["name"];
  $age = $_POST["age"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  


  $result = pg_query_params($conn, "INSERT INTO users (name, age, email, password) VALUES ($1, $2, $3, $4)", array($name, $age, $email, $password));
  if (!$result) {
    die("Error: " . pg_last_error());
  }
}

// Display all user names in the table
$result = pg_query($conn, "SELECT name FROM users");
if (!$result) {
  die("Error: " . pg_last_error());
}

echo "<h2>User Names:</h2>";
echo "<ul>";
while ($row = pg_fetch_assoc($result)) {
  echo "<li>" . $row["name"] . "</li>";
}
echo "</ul>";

// iif the user has submitted the forgot password form, retrieve the password from the database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["forgot_password"])) {
  $name = $_POST["name"];
  $age = $_POST["age"];
  $email = $_POST["email"];  
  $result = pg_query_params($conn, "SELECT password FROM users WHERE name = $1 AND age = $2 AND email = $3", array($name, $age, $email));
  
  if(!$result) {
    die("Error: " . pg_last_error());
  }
  
  $num_rows = pg_num_rows($result);
  if ($num_rows > 0) {
    $row = pg_fetch_assoc($result);
    echo "<h2>Your Password:</h2>";
    echo "<p>" . $row["password"] . "</p>";
  } else {
    echo "<h2>User Not Found</h2>";
  }
}

// Close connection
pg_close($conn);

?>

<!-- User Registration Form -->
<h2>Registration:</h2>
<form method="post" action="">
  Name: <input type="text" name="name"><br>
  Age: <input type="number" name="age"><br>
  Email: <input type="text" name="email"><br>
  Password: <input type="password" name="password"><br>
  <input type="submit" name="submit" value="Submit">
</form>

<!-- Forgot Password Form -->
<h2>Forgot Password:</h2>
<form method="post" action="">
  Name: <input type="text" name="name"><br>
  Age: <input type="number" name="age"><br>
  Email: <input type="text" name="email"><br>
  <input type="submit" name="forgot_password" value="Submit">
</form>
