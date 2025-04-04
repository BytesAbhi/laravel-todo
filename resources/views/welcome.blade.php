<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 9 To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #taskContainer {
            display: none;
            margin-top: 20px;
        }

        #enterButtonContainer {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .btn-success {
            height: 50px;
            width: 150px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 25px;
            background-color: #28a745;
            border: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .task-item {
            background-color: #f8f9fa;
            transition: background 0.3s, box-shadow 0.3s;
        }

        .task-item:hover {
            background-color: #e9ecef;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .task-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #0d6efd;
            cursor: pointer;
        }

        .task-title {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .task-title.text-decoration-line-through {
            color: #6c757d !important;
        }

        .badge {
            font-size: 12px;
            padding: 5px 8px;
        }
    </style>
</head>

<body class="container mt-4">
    <h2 class="text-center">To-Do List</h2>
    <div id="enterButtonContainer" class="text-center mb-3">

        <button id="enterButton" class="btn btn-success">Enter</button>
    </div>

    <div id="taskContainer" class="container-fluid">
        <form id="taskForm" class="task-list-containe">
            <div class="mb-3">
                <input type="text" id="title" class="form-control" placeholder="Task Name" required>
            </div>
            <div class="mb-3">
                <textarea id="objective" class="form-control" placeholder="Task Objective"></textarea>
            </div>
            <div class="mb-3">
                <input type="file" id="image" class="form-control">
            </div>
            <div class="mb-3">
                <input type="datetime-local" id="task_time" class="form-control">
            </div>
            <div class="mb-3">
                <input type="number" id="duration" class="form-control" placeholder="Task Duration (minutes)">
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>

        <div class="task-list-container">
            <div class="my-3">
                <input type="checkbox" id="showAllTasks">
                <label for="showAllTasks">Show All Tasks</label>
            </div>
            <ul id="taskList" class="list-group" style="gap:10px;"></ul>
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
                        <input type="number" id="editDuration" class="form-control"
                            placeholder="Task Duration (minutes)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#taskContainer').hide();
            $('#enterButton').click(function() {
                $('#taskContainer').fadeIn();
                $(this).hide();
            });

            loadTasks();

            function loadTasks() {
                let showAll = $('#showAllTasks').is(':checked');
                $.get('/tasks', function(tasks) {
                    $('#taskList').html('');
                    tasks.forEach(task => {
                        if (showAll || !task.is_completed) {
                            $('#taskList').append(`
                                <li class="list-group-item d-flex justify-content-between align-items-center task-item p-3 shadow-sm rounded" data-id="${task.id}">
                                    <span class="d-flex align-items-center">
                                        <input type="checkbox" class="task-checkbox me-2 custom-checkbox" data-id="${task.id}" ${task.is_completed ? 'checked' : ''}>
                                        ${task.image ? `<img src="/storage/${task.image}" width="75px" height="75px" style="border-radius:5px; margin-right:10px;" class="ms-2">` : ''}
                                        <label class="task-title ${task.is_completed ? 'text-decoration-line-through text-muted' : ''}" for="task-${task.id}">
                                            ${task.title} <span class="badge bg-primary ms-2">${task.duration} min</span>
                                        </label>
                                    </span>
                                    <div>
                                        <button class="btn btn-warning btn-sm me-2 edit-task" data-id="${task.id}" data-title="${task.title}" data-objective="${task.objective}" data-duration="${task.duration}" data-task_time="${task.task_time}">Edit Task</button>
                                        <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                                    </div>
                                </li>
                            `);
                        }
                    });
                });
            }

            $('#editTaskForm').submit(function(e) {
    e.preventDefault();

    console.log("Edit form submitted"); // Debug log

    let taskId = $('#editTaskId').val();
    let taskTime = $('#editTaskTime').val();
    let formattedTaskTime = taskTime ? taskTime.replace('T', ' ') + ':00' : null;

    let data = {
        title: $('#editTitle').val(),
        objective: $('#editObjective').val(),
        task_time: formattedTaskTime,
        duration: $('#editDuration').val(),
        _token: $('meta[name="csrf-token"]').attr('content'),
        _method: 'PUT'
    };

    console.log("Data to send:", data);

    $.ajax({
        url: `/tasks/${taskId}`,
        type: 'POST',
        data: data,
        success: function() {
            console.log("Update success");

            const modalEl = document.getElementById('editTaskModal');
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal.getInstance) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();
            } else {
                $('#editTaskModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }

            loadTasks();
        },
        error: function(xhr) {
            console.error("Update failed", xhr.responseText);
            alert('Failed to update task.\n' + xhr.responseText);
        }
    });
});





            // $('#taskForm').submit(function(e) {
            //     e.preventDefault();
            //     let formData = new FormData();
            //     formData.append('title', $('#title').val());
            //     formData.append('objective', $('#objective').val());
            //     formData.append('image', $('#image')[0].files[0]);

            //     let taskTime = $('#task_time').val();
            //     let formattedTaskTime = taskTime ? taskTime.replace('T', ' ') + ':00' : null;
            //     formData.append('task_time', formattedTaskTime);
            //     formData.append('duration', $('#duration').val());
            //     formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            //     $.ajax({
            //         url: '/tasks',
            //         type: 'POST',
            //         data: formData,
            //         processData: false,
            //         contentType: false,
            //         success: function() {
            //             $('#taskForm')[0].reset();
            //             loadTasks();
            //         }
            //     });
            // });

            $('#taskForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData();
                formData.append('title', $('#title').val());
                formData.append('objective', $('#objective').val());
                formData.append('image', $('#image')[0]?.files[0] || '');

                let taskTime = $('#task_time').val();
                let formattedTaskTime = taskTime ? taskTime.replace('T', ' ') + ':00' : null;
                formData.append('task_time', formattedTaskTime);
                formData.append('duration', $('#duration').val());
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    url: '/tasks',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert('Task added successfully!');
                        $('#taskForm')[0].reset(); // ✅ Reset only on success
                        loadTasks();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Laravel validation error
                            let errors = xhr.responseJSON.errors;
                            let messages = Object.values(errors).flat().join('\n');
                            alert("Validation Error:\n" + messages);
                        } else if (xhr.status === 409) {
                            // Conflict (duplicate task)
                            alert("Duplicate Task:\n" + (xhr.responseJSON?.error ||
                                'A task with the same title already exists.'));
                        } else {
                            alert("Something went wrong:\n" + xhr.statusText);
                        }
                    }
                });
            });




            $(document).on('change', '.task-checkbox', function() {
                let taskId = $(this).data('id');
                let isCompleted = $(this).is(':checked') ? 1 : 0;
                $.ajax({
                    url: `/tasks/${taskId}/complete`,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        is_completed: isCompleted
                    },
                    success: function() {
                        if (isCompleted) {
                            $(`li[data-id="${taskId}"]`).fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.delete-task', function() {
                if (confirm("Are you sure to delete this task?")) {
                    let taskId = $(this).data('id');
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            $(`li[data-id="${taskId}"]`).fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }
                    });
                }
            });

            $('#showAllTasks').on('change', function() {
                loadTasks();
            });

            $(document).on('click', '.edit-task', function() {
                $('#editTaskId').val($(this).data('id'));
                $('#editTitle').val($(this).data('title'));
                $('#editObjective').val($(this).data('objective'));
                $('#editDuration').val($(this).data('duration'));
                $('#editTaskTime').val($(this).data('task_time'));
                const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                modal.show();
            });





            $('#editTaskForm').submit(function(e) {
                e.preventDefault();
                const taskId = $('#editTaskId').val();
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        title: $('#editTitle').val(),
                        objective: $('#editObjective').val(),
                        task_time: $('#editTaskTime').val(),
                        duration: $('#editDuration').val()
                    },
                    success: function() {
                        $('#editTaskModal').modal('hide');
                        loadTasks();
                    }
                });
            });
        });
    </script>
</body>

</html>
