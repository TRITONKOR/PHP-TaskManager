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
    <title>Login</title>
  <link rel="stylesheet" href="/static/login-style.css">
</head>
<body>
<div>
  <h1>Login</h1>
  <form method="POST" action="/login">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
      <br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      <br>
      <button type="submit">Login</button>
  </form>
</div>
</body>
</html>
