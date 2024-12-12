<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

use Alex\TaskManagerApp\Services\TaskManager;
use Alex\TaskManagerApp\Entities\User;

$taskManager = new TaskManager();

$userId = $_SESSION['user_id'];
$user = User::getById($userId);

$tasks = $taskManager->getAllTasksByUser($userId);

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Задачі</title>
    <link rel="stylesheet" href="/static/tasks-style.css">
</head>
<body>
<div class="profile-container">
  <a href="/profile" class="profile-button">Профіль</a>
</div>
<div class="tasks-container">
    <h2>Задачі користувача: <?php
        echo htmlspecialchars($user->getUsername()); ?></h2>

    <?php
    if (empty($tasks)): ?>
        <p>Не є.</p>
    <?php
    else: ?>
        <ul class="tasks-list">
            <?php
            foreach ($tasks as $task): ?>
                <li class="task-item">
                    <div class="task-details" style="display: flex; gap: 30px">
                        <h3><?php
                            echo htmlspecialchars($task->getTitle()); ?></h3>
                        <p><?php
                            echo htmlspecialchars($task->getDescription()); ?></p>
                        <p class="task-status">Статус: <?php
                            echo htmlspecialchars($task->getStatus()->value); ?></p>
                        <div class="task-dates">
                            <p><small>Створено: <?php
                                    echo $task->getCreatedAt()->format('Y-m-d H:i'); ?></small></p>
                            <p><small>Оновлено: <?php
                                    echo $task->getUpdatedAt()->format('Y-m-d H:i'); ?></small></p>
                        </div>
                    </div>
                    <div class="task-actions" data-task-id="<?= $task->getId(); ?>">
                        <button class="edit-task-action">Редагувати</button>
                        <button class="delete-task-action">Видалити</button>
                    </div>
                </li>
            <?php
            endforeach; ?>
        </ul>
    <?php
    endif; ?>

  <div id="add-task-form">
    <h3>Створити нову задачу</h3>
    <form method="POST" action="/tasks">
      <label for="title">Назва:</label>
      <input type="text" name="title" id="title" required>

      <label for="description">Опис:</label>
      <textarea name="description" id="description" required></textarea>

      <button type="submit">Створити задачу</button>
    </form>
  </div>
</div>

<div id="edit-task-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3>Редагувати задачу</h3>
        <form id="edit-task-form">
            <input type="hidden" id="edit-task-id" name="task_id">
            <label for="edit-title">Назва:</label>
            <input type="text" id="edit-title" name="title" required>
            <label for="edit-description">Опис:</label>
            <textarea id="edit-description" name="description" required></textarea>
            <label for="edit-status">Статус:</label>
            <select id="edit-status" name="status">
              <option value="pending">Не виконано</option>
              <option value="in_progress">В процесі</option>
              <option value="completed">Виконано</option>
            </select>
            <button type="submit">Зберегти</button>
        </form>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-task-action');
    const editButtons = document.querySelectorAll('.edit-task-action');
    const editTaskModal = document.getElementById('edit-task-modal');
    const editTaskForm = document.getElementById('edit-task-form');
    const closeBtn = editTaskModal.querySelector('.close-btn');

    // Видалення задач
    deleteButtons.forEach(button => {
      button.addEventListener('click', async () => {
        const taskId = button.closest('.task-actions').dataset.taskId;

        if (confirm('Ви дійсно хочете видалити цю задачу?')) {
          try {
            const response = await fetch('/tasks', {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({ task_id: taskId }),
            });

            const result = await response.json();

            if (result.status === 'success') {
              // Видаляємо задачу з DOM
              button.closest('.task-item').remove();
            } else {
              alert('Не вдалося видалити задачу.');
            }
          } catch (error) {
            console.error('Помилка видалення:', error);
            alert('Сталася помилка. Спробуйте пізніше.');
          }
        }
      });
    });

    // Редагування задач
    editButtons.forEach(button => {
      button.addEventListener('click', () => {
        const taskItem = button.closest('.task-item');
        const taskId = taskItem.querySelector('.task-actions').dataset.taskId;

        // Заповнення форми модального вікна
        document.getElementById('edit-task-id').value = taskId;
        document.getElementById('edit-title').value = taskItem.querySelector('h3').innerText;
        document.getElementById('edit-description').value = taskItem.querySelector('p').innerText;
        document.getElementById('edit-status').value = taskItem.querySelector('.task-status').innerText.split(': ')[1];

        // Відображення модального вікна
        editTaskModal.style.display = 'block';
      });
    });

    closeBtn.addEventListener('click', () => {
      editTaskModal.style.display = 'none';
    });

    editTaskForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      const taskId = document.getElementById('edit-task-id').value;
      const title = document.getElementById('edit-title').value;
      const description = document.getElementById('edit-description').value;
      const status = document.getElementById('edit-status').value;

      try {
        const response = await fetch('/tasks', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            task_id: taskId,
            title,
            description,
            status,
          }),
        });

        const result = await response.json();

        if (result.status === 'success') {
          // Оновлення сторінки або задачі
          window.location.href = result.redirect;
        } else {
          alert('Не вдалося оновити задачу.');
        }
      } catch (error) {
        console.error('Помилка оновлення:', error);
        alert('Сталася помилка. Спробуйте пізніше.');
      }
    });
  });
</script>
</body>
</html>
