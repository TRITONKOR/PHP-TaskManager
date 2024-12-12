<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

use Alex\TaskManagerApp\Entities\User;

$userId = $_SESSION['user_id'];
$user = User::getById($userId);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  <link rel="stylesheet" href="/static/profile-style.css">
</head>
<body>
<div class="profile-container">
  <h1>Your Profile</h1>
  <p>Username: <?= htmlspecialchars($user->getUsername()) ?></p>
  <p>Email: <?= htmlspecialchars($user->getEmail()) ?></p>

    <?php if (isset($successMessage)): ?>
      <p style="color: green;"><?= htmlspecialchars($successMessage) ?></p>
    <?php elseif (isset($errorMessage)): ?>
      <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

  <form method="POST" action="/profile">
    <label for="current_password">Current Password:</label>
    <input type="password" id="current_password" name="current_password" required>
    <br>
    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <br>
    <label for="confirm_password">Confirm New Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required>
    <br>
    <button type="submit">Change Password</button>
  </form>

  <!-- Logout Button -->
  <form method="POST" action="/logout">
    <button type="submit">Logout</button>
  </form>
</div>
</body>
</html>
