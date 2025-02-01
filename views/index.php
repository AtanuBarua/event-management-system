<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg bg-dark navbar-dark mb-4 p-3 rounded">
            <a class="navbar-brand" href="#">Event Management</a>
            <div class="ms-auto">
                <form action="<?=dirname($_SERVER['SCRIPT_NAME'])?>/logout" method="POST" style="display: inline;">
                    <button class="btn btn-danger">Logout</button>
                </form>
            </div>
        </nav>

        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex">
                <input type="text" class="form-control me-2" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" name="name" placeholder="Search Event">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openModal()">Create Event</button>
        </div>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $value): ?>
                    <p><?= $value; ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <p><?= $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']) ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-hover mt-4">
            <thead class="table-primary">
                <tr>
                    <th scope="col">
                        <a href="?<?= http_build_query(array_merge($_GET, [
                            'sortOrder' => ($sortOrder == \App\Models\Event::SORT_ASCENDING ? \App\Models\Event::SORT_DESCENDING : \App\Models\Event::SORT_ASCENDING)
                        ])) ?>">#</a>
                    </th>                    
                    <th scope="col">Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Capacity</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['description'] ?></td>
                            <td><?= $row['registered'].'/'.$row['capacity'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openModal(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>)">Edit</button>
                                <form action="<?=dirname($_SERVER['SCRIPT_NAME'])?>/event/delete" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <?php if (!in_array($row['id'], $userRegisteredEvents)): ?>
                                    <form action="<?=dirname($_SERVER['SCRIPT_NAME'])?>/event/register" method="POST" style="display:inline;">
                                    <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
                                    <button onclick="return confirm('Sure to register?')" type="submit" class="btn btn-success btn-sm">Register</button>
                                </form>
                                <?php endif ?>
                                <?php if ((new \App\Models\User)->isAdmin($_SESSION['user_type'] ?? 0)): ?>
                                    <a href="<?=dirname($_SERVER['SCRIPT_NAME'])?>/event-attendees/export?event_id=<?=$row['id']?>" class="btn btn-warning btn-sm">Export</a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-danger">No events found</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>

        <nav aria-label="Event Pagination">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Prev</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next &raquo;</a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <form action='<?=dirname($_SERVER['SCRIPT_NAME'])?>/' method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalLabel">New Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Event Capacity</label>
                            <input type="text" class="form-control" id="capacity" name="capacity">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const openModal = (event = null) => {            
            document.getElementById('eventModalLabel').textContent = event ? 'Edit Event' : 'Create Event';
            document.getElementById('id').value = event ? event.id : '';
            document.getElementById('name').value = event ? event.name : '';
            document.getElementById('description').value = event ? event.description : '';
            document.getElementById('capacity').value = event ? event.capacity : '';
            document.getElementById('submitBtn').textContent = event ? 'Update' : 'Create';
        };
    </script>
</body>

</html>
