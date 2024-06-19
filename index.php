<?php
session_start();

// Initialize the to-do list if it doesn't exist
if (!isset($_SESSION['todo_list'])) {
    $_SESSION['todo_list'] = [];
}

// Add new Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_todo'], $_POST['priority'])) {
    $new_todo = trim($_POST['new_todo']);
    $priority = $_POST['priority'];
    if ($new_todo !== '') {
        $_SESSION['todo_list'][] = [
            'task' => $new_todo,
            'priority' => $priority,
            'done' => false
        ];
    }
}

// Mark Task done or undone
if (isset($_GET['toggle'])) {
    $index = intval($_GET['toggle']);
    if (isset($_SESSION['todo_list'][$index])) {
        $_SESSION['todo_list'][$index]['done'] = !$_SESSION['todo_list'][$index]['done'];
    }
}

// Delete Task
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($_SESSION['todo_list'][$index])) {
        array_splice($_SESSION['todo_list'], $index, 1);
    }
}

// Sort by priority (high -> medium -> low)
usort($_SESSION['todo_list'], function($a, $b) {
    $priorityOrder = [
        'high' => 3,
        'medium' => 2,
        'low' => 1
    ];
    return $priorityOrder[$b['priority']] <=> $priorityOrder[$a['priority']];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">To-Do List</h1>
    <div class="card">
        <div class="card-body">
            <form action="index.php" method="POST">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="new_todo" placeholder="Add a new task" aria-label="Add a new task" required>
                    <select class="form-select" name="priority">
                        <option value="high" selected>High Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="low">Low Priority</option>
                    </select>
                    <button class="btn btn-primary" type="submit">Add</button>
                </div>
            </form>
            <ul class="list-group">
                <?php foreach ($_SESSION['todo_list'] as $index => $item): ?>
                    <?php
                        $priorityClass = '';
                        switch ($item['priority']) {
                            case 'high':
                                $priorityClass = 'list-group-item-danger';
                                break;
                            case 'medium':
                                $priorityClass = 'list-group-item-warning';
                                break;
                            case 'low':
                                $priorityClass = 'list-group-item-success';
                                break;
                        }
                        $doneClass = $item['done'] ? 'text-decoration-line-through' : '';
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $priorityClass; ?>">
                        <span class="<?php echo $doneClass; ?>"><?php echo htmlspecialchars($item['task']); ?></span>
                        <div>
                            <a href="?toggle=<?php echo $index; ?>" class="btn btn-sm btn-outline-secondary">
                                <?php echo $item['done'] ? 'Undone' : 'Done'; ?>
                            </a>
                            <a href="?delete=<?php echo $index; ?>" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
