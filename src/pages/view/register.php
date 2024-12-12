<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: /");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
  <link rel="stylesheet" href="/static/register-style.css">
</head>
<body>
<div>
  <h1>Register</h1>
  <form action="/register" method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
      <br>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      <br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      <br>
      <button type="submit">Register</button>
  </form>
</div>
</body>
</html>
