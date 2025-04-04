<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <input type="checkbox" id="showAllTasks" class="me-2">
                <label for="showAllTasks" class="mb-0">Show All Tasks</label>
            </div>
            <div class="input-group mb-3">
                <input type="text" id="title" class="form-control" placeholder="Project # To Do">
                <button id="addTask" class="btn btn-success">Add</button>
            </div>

            <ul id="taskList" class="list-group"></ul>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editTaskForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editTaskId">
                <div class="mb-3">
                    <input type="text" id="editTitle" class="form-control" placeholder="Task Name" required>
                </div>
                <div class="mb-3">
                    <textarea id="editObjective" class="form-control" placeholder="Task Objective"></textarea>
                </div>
                <div class="mb-3">
                    <input type="datetime-local" id="editTaskTime" class="form-control">
                </div>
                <div class="mb-3">
                    <input type="number" id="editDuration" class="form-control" placeholder="Task Duration (minutes)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Task</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    function renderTask(task) {
        return `
            <li class="list-group-item d-flex justify-content-between align-items-center p-3 shadow-sm rounded" data-id="${task.id}">
                <span class="d-flex align-items-center">
                    <input type="checkbox" class="task-checkbox me-2" data-id="${task.id}" ${task.is_completed ? 'checked' : ''}>
                    ${task.image ? `<img src="/storage/${task.image}" width="40px" height="40px" class="rounded-circle me-2">` : ''}
                    <label class="task-title ${task.is_completed ? 'text-decoration-line-through text-muted' : ''}">
                        ${task.title} <span class="badge bg-primary ms-2">${task.duration} min</span>
                    </label>
                </span>
                <div>
                    <button class="btn btn-warning btn-sm me-2 edit-task" data-id="${task.id}" data-title="${task.title}" data-objective="${task.objective}" data-duration="${task.duration}" data-task_time="${task.task_time}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                </div>
            </li>`;
    }

    function loadTasks() {
        $("#taskList").html(`
            ${renderTask({id: 1, title: 'Task 1', duration: 30, is_completed: false})}
            ${renderTask({id: 2, title: 'Task 2', duration: 45, is_completed: true})}
        `);
    }

    loadTasks();

    $(document).on('click', '#addTask', function() {
        let title = $('#title').val().trim();
        if (!title) return;
        $("#taskList").append(renderTask({id: Date.now(), title: title, duration: 60, is_completed: false}));
        $('#title').val('');
    });

    $(document).on('click', '.edit-task', function() {
        $('#editTaskId').val($(this).data('id'));
        $('#editTitle').val($(this).data('title'));
        $('#editObjective').val($(this).data('objective'));
        $('#editTaskTime').val($(this).data('task_time'));
        $('#editDuration').val($(this).data('duration'));

        $('#editTaskModal').modal('show');
    });

    $('#editTaskForm').submit(function(e) {
        e.preventDefault();
        $('#editTaskModal').modal('hide');
    });

    $(document).on('click', '.delete-task', function() {
        $(this).closest('li').remove();
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
