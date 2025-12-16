<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

require_once "../classes/profile.php";
require_once "../classes/roles.php";

$profileObj = new Profiles();
$roleObj = new Roles();
$search = $role_id = $status = $sex = "";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
    $role_id = isset($_GET["role_id"]) ? trim(htmlspecialchars($_GET["role_id"])) : "";
    $status = isset($_GET["status"]) ? trim(htmlspecialchars($_GET["status"])) : "";
    $sex = isset($_GET["sex"]) ? trim(htmlspecialchars($_GET["sex"])) : "";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profiles</title>
    <link rel="stylesheet" href="../view.css">
</head>

<body>

<header class="page-header">
    <h1 class="page-title">Profiles</h1>
    <div class="header-actions">
        <a class="nav-link" href="../index.php">Back</a>
        <a class="btn btn-primary" href="addProfile.php">Add Profile</a>
    </div>
</header>

    <main class="page-container">
    <section class="form-card">

        <form action="" method="get" class="search-form">
                <label for="search" class="sr-only">Search Profiles</label>
                <input class="search-input" type="search" name="search" id="search" value="<?= $search ?>" placeholder="Search by name, phone, or email">
                <button id="search-submit" class="btn btn-search" type="submit">Search <span id="search-spinner" hidden class="spinner"></span></button>

                <label for="role_id" class="sr-only">Filter by Role</label>
                <select class="filter-select" name="role_id" id="role_id">
                <option value="">All Roles</option>
                <?php 
                $roles = $roleObj->viewRole();
                if (!empty($roles)):
                    foreach ($roles as $r): ?>
                        <option value="<?= $r['role_id'] ?>" <?= ($role_id !== '' && $role_id == $r['role_id']) ? 'selected' : '' ?>><?= htmlspecialchars($r['role_name']) ?></option>
                    <?php endforeach;
                endif; ?>
            </select>

            <label for="sex" class="sr-only">Filter by Sex</label>
            <select class="filter-select" name="sex" id="sex">
                <option value="">All Sex</option>
                <option value="Male" <?= (isset($sex) && $sex == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= (isset($sex) && $sex == 'Female') ? 'selected' : '' ?>>Female</option>
            </select>

            <label for="status" class="sr-only">Filter by Status</label>
            <select class="filter-select" name="status" id="status">
                <option value="">All Statuses</option>
                <option value="Active" <?= (isset($status) && $status == 'Active') ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= (isset($status) && $status == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>

            </div>
        </form>
        <script>
            // show spinner while search form is submitting
            document.querySelector('.search-form').addEventListener('submit', function(){
                document.getElementById('search-spinner').hidden = false;
            });
        </script>

    </section>

    <div class="table-container">
        <table class="data-table" role="table">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Sex</th>
                    <th>DOB</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $rows = $profileObj->viewProfile($search, $role_id, $status, $sex);
                if (empty($rows)):
                ?>
                <tr>
                    <td colspan="13">
                        <div class="table-empty">
                            <div class="table-empty-icon">📭</div>
                            <div class="table-empty-text">No profiles found.</div>
                            <div class="table-empty-subtext">Try adjusting your search or filters.</div>
                        </div>
                    </td>
                </tr>
                <?php
                else:
                    foreach($rows as $profile) {
                        $message = "Are you sure you want to delete this profile?";
                ?>
                <tr tabindex="0">

                    <td><?= $profile['id'] ?></td>

                    <td>
                        <?php 
                            $role = $roleObj->fetchRole($profile['role_id']);
                            echo htmlspecialchars($role['role_name'] ?? 'Unknown');
                        ?>
                    </td>

                    <td title="<?= htmlspecialchars($profile['fname'] . ' ' . $profile['mname'] . ' ' . $profile['lname']) ?>" class="truncate" data-fulltext="<?= htmlspecialchars($profile['fname'] . ' ' . $profile['mname'] . ' ' . $profile['lname']) ?>"><?= htmlspecialchars($profile['fname'] . ' ' . $profile['mname'] . ' ' . $profile['lname']) ?></td>

                    <td title="<?= htmlspecialchars($profile['phone_num']) ?>" data-fulltext="<?= htmlspecialchars($profile['phone_num']) ?>"><?= htmlspecialchars($profile['phone_num']) ?></td>
                    <td title="<?= htmlspecialchars($profile['email']) ?>" class="truncate" data-fulltext="<?= htmlspecialchars($profile['email']) ?>"><?= htmlspecialchars($profile['email']) ?></td>
                    <td title="<?= htmlspecialchars($profile['address']) ?>" class="truncate" data-fulltext="<?= htmlspecialchars($profile['address']) ?>"><?= htmlspecialchars($profile['address']) ?></td>
                    <td><?= htmlspecialchars($profile['sex']) ?></td>
                    <td><?= htmlspecialchars(!empty($profile['dob']) ? date('Y-m-d', strtotime($profile['dob'])) : '') ?></td>
                    <td><?= htmlspecialchars(!empty($profile['join_date']) ? date('Y-m-d', strtotime($profile['join_date'])) : '') ?></td>
                    <?php $s = strtolower($profile['status']); ?>
                    <td><?= htmlspecialchars($profile['status']) ?></td>
                    <td><?= htmlspecialchars($profile['created_at']) ?></td>
                    <td><?= htmlspecialchars($profile['updated_at']) ?></td>

                    <td class="table-actions">
                        <a class="btn btn-action btn-edit" href="editProfile.php?profile_id=<?= $profile['id'] ?>" title="Edit">Edit</a>

                        <?php if ($_SESSION['user']['role'] == 'Admin') { ?>
                            <button class="btn btn-action btn-delete" data-delete-url="removeProfile.php?profile_id=<?= $profile['id'] ?>" data-delete-msg="<?= htmlspecialchars($message) ?>" title="Delete">Delete</button>
                        <?php } ?>
                    </td>

                </tr>
                <?php }
                endif; ?>
            </tbody>

        </table>
    </div>

</main>

</body>
</html>

<script src="../scripts/table-utils.js"></script>

<!-- Delete confirmation modal -->
<div id="confirm-delete-modal" hidden role="dialog" aria-modal="true">
    <div>
        <h3>Confirm delete</h3>
        <p id="confirm-delete-msg">Are you sure you want to delete this item?</p>
        <div>
            <button id="confirm-delete-cancel">Cancel</button>
            <a id="confirm-delete-go" href="#">Delete</a>
        </div>
    </div>
</div>

<script>
    // Wire delete buttons to confirmation modal (use hidden attribute instead of inline styles)
    document.querySelectorAll('button[data-delete-url]').forEach(function(btn){
        btn.addEventListener('click', function(e){
            var url = btn.getAttribute('data-delete-url');
            var msg = btn.getAttribute('data-delete-msg') || 'Are you sure?';
            var modal = document.getElementById('confirm-delete-modal');
            document.getElementById('confirm-delete-msg').textContent = msg;
            var go = document.getElementById('confirm-delete-go');
            go.setAttribute('href', url);
            modal.hidden = false;
        });
    });
    document.getElementById('confirm-delete-cancel').addEventListener('click', function(){ document.getElementById('confirm-delete-modal').hidden = true; });
    document.getElementById('confirm-delete-go').addEventListener('click', function(){ /* link will perform delete */ });
</script>
