<!DOCTYPE html>
<html>

<head>
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex">
                <form method="GET" class="d-flex">
                    <input type="text" class="form-control me-2" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" name="name" placeholder="Search Event">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openModal()">Create Event</button>
        </div>

        <div class="w-75 mx-auto">
            <h1 class="text-center">Events</h1>
            <?php if (isset($_SESSION['errors'])): ?>
                <?php foreach ($_SESSION['errors'] as $value): ?>
                    <p style="color: red;"><?= $value; ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']) ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['message'])): ?>
                <p style="color: green;"><?= $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']) ?>
            <?php endif; ?>
            <table class="table mt-5">
                <thead>
                    <tr>
                        <th scope="col">
                            <a href="?<?= http_build_query(array_merge($_GET, [
                                'sortOrder' => ($sortOrder == \App\Models\Event::SORT_ASCENDING ? \App\Models\Event::SORT_DESCENDING : \App\Models\Event::SORT_ASCENDING)
                            ])) ?>">#</a>
                        </th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)) { ?>
                        <?php foreach ($events as $row): ?>
                            <tr>
                                <th scope="row"><?= $row['id'] ?></th>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['description'] ?></td>
                                <td>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openModal(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>)">Edit</button>
                                    <form action="/event-management-system/public/event/delete" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    <form action="/event-management-system/public/event/register" method="POST" style="display:inline;">
                                        <input type="hidden" name="event_id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-success">Register</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3" style="color: red; text-align: center;">No data found</td>
                        </tr>
                    <?php  } ?>
                </tbody>
            </table>
            <nav aria-label="Event Pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1]))?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1]))?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action='/event-management-system/public/' method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="eventModalLabel">New Event</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="id" name="id">
                            <div class="mb-3">
                                <label for="name" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Short Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Event Capacity</label>
                                <input type="number" class="form-control" id="capacity" name="capacity">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const openModal = (event = null) => {
            const modalTitle = document.getElementById('eventModalLabel');
            const idInput = document.getElementById('id');
            const nameInput = document.getElementById('name');
            const descriptionInput = document.getElementById('description')
            const capacityInput = document.getElementById('capacity')
            const submitBtn = document.getElementById('submitBtn')

            if (event) {
                modalTitle.textContent = 'Edit event'
                idInput.value = event.id;
                nameInput.value = event.name;
                descriptionInput.value = event.description;
                capacityInput.value = event.capacity;
                submitBtn.textContent = 'Update'
            } else {
                modalTitle.textContent = 'Create event'
                idInput.value = '';
                nameInput.value = '';
                descriptionInput.value = '';
                capacityInput.value = '';
                submitBtn.textContent = 'Create'
            }
        }
    </script>
</body>

</html>