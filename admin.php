<?php
session_start();
require_once 'database.php';

if (!isAdmin()) redirect('login.php?msg=Admin access required.');

$message = '';
$msg_type = 'success';

// ===== ADD PACKAGE =====
if (isset($_POST['add_package'])) {
    $dest  = clean($conn, $_POST['destination']);
    $desc  = clean($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $dur   = clean($conn, $_POST['duration']);
    $img   = clean($conn, $_POST['image']);

    $stmt = $conn->prepare("INSERT INTO packages (destination, description, price, duration, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $dest, $desc, $price, $dur, $img);
    if ($stmt->execute()) {
        $message = "Package added successfully!";
    } else {
        $message = "Failed to add package.";
        $msg_type = 'danger';
    }
}

// ===== UPDATE PACKAGE =====
if (isset($_POST['update_package'])) {
    $id    = (int)$_POST['package_id'];
    $dest  = clean($conn, $_POST['destination']);
    $desc  = clean($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $dur   = clean($conn, $_POST['duration']);
    $img   = clean($conn, $_POST['image']);

    $stmt = $conn->prepare("UPDATE packages SET destination=?, description=?, price=?, duration=?, image=? WHERE id=?");
    $stmt->bind_param("ssdssi", $dest, $desc, $price, $dur, $img, $id);
    if ($stmt->execute()) {
        $message = "Package updated successfully!";
        // Redirect to clear POST data and prevent form resubmission
        redirect('admin.php?msg=updated');
    } else {
        $message = "Failed to update package.";
        $msg_type = 'danger';
    }
}

// Show message if redirected after update
if (isset($_GET['msg']) && $_GET['msg'] === 'updated') {
    $message = "Package updated successfully!";
}

// ===== DELETE PACKAGE =====
if (isset($_POST['delete_package'])) {
    $pid = (int)$_POST['package_id'];
    $conn->query("DELETE FROM packages WHERE id = $pid");
    $message = "Package deleted.";
}

// ===== UPDATE BOOKING STATUS =====
if (isset($_POST['update_booking'])) {
    $bid    = (int)$_POST['booking_id'];
    $status = clean($conn, $_POST['status']);
    $conn->query("UPDATE bookings SET status = '$status' WHERE id = $bid");
    $message = "Booking status updated.";
}

// ===== FETCH DATA =====
$packages = $conn->query("SELECT * FROM packages ORDER BY id DESC");
$bookings = $conn->query("
    SELECT b.*, u.username, u.email, p.destination
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN packages p ON b.package_id = p.id
    ORDER BY b.created_at DESC
");
$payments = $conn->query("
    SELECT pay.*, b.id as bid, p.destination, u.username
    FROM payments pay
    JOIN bookings b ON pay.booking_id = b.id
    JOIN packages p ON b.package_id = p.id
    JOIN users u ON b.user_id = u.id
    ORDER BY pay.paid_at DESC
");

// Stats
$total_users    = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_bookings = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$total_revenue  = $conn->query("SELECT SUM(amount) as s FROM payments WHERE payment_status='completed'")->fetch_assoc()['s'] ?? 0;
$total_packages = $conn->query("SELECT COUNT(*) as c FROM packages")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – TravelEase</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styling for the search bar and tabs */
        .admin-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .admin-search {
            padding: 10px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            width: 300px;
            outline: none;
            transition: border 0.3s;
        }

        .admin-search:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 119, 182, 0.1);
        }

        .btn-sm-edit {
            background: #2b38cc;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 14px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }

        .btn-sm-edit:hover {
            background: #3935b4;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="logo"><a href="index.php">✈ Travel<span>Ease</span> Admin</a></div>
        <ul class="nav-links">
            <li><a href="index.php">View Site</a></li>
            <li style="color:rgba(255,255,255,0.7); font-size:14px;">👤 <?= htmlspecialchars($_SESSION['admin_username']) ?></li>
            <li><a href="logout.php" class="btn-nav">Logout</a></li>
        </ul>
    </nav>

    <div class="page-header">
        <h1>⚙️ Admin Dashboard</h1>
        <p>Manage packages, bookings, and payments</p>
    </div>

    <div class="admin-container">

        <?php if ($message): ?>
            <div class="alert alert-<?= $msg_type ?>"><?= $message ?></div>
        <?php endif; ?>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $total_users ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_packages ?></h3>
                <p>Tour Packages</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_bookings ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <h3>₹<?= number_format($total_revenue, 0) ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- TABS & SEARCH TOOLBAR -->
        <div class="admin-toolbar">
            <div class="admin-tabs" style="margin-bottom: 0;">
                <button class="tab-btn active" data-tab="tab-packages">📦 Packages</button>
                <button class="tab-btn" data-tab="tab-add" id="add-tab-btn">➕ Add Package</button>
                <button class="tab-btn" data-tab="tab-bookings">📋 Bookings</button>
                <button class="tab-btn" data-tab="tab-payments">💳 Payments</button>
            </div>
            <input type="text" id="searchInput" class="admin-search" placeholder="🔍 Search tables...">
        </div>

        <!-- TAB: PACKAGES -->
        <div id="tab-packages" class="tab-content active">
            <table id="packagesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Destination</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $packages->data_seek(0);
                    while ($p = $packages->fetch_assoc()): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><strong><?= htmlspecialchars($p['destination']) ?></strong></td>
                            <td>&#8377;<?= number_format($p['price'], 2) ?></td>
                            <td><?= htmlspecialchars($p['duration']) ?></td>
                            <td><span class="badge <?= $p['available'] ? 'badge-confirmed' : 'badge-cancelled' ?>"><?= $p['available'] ? 'Active' : 'Inactive' ?></span></td>
                            <td>
                                <!-- Edit Button triggers JS -->
                                <button
                                    class="btn-sm-edit"
                                    onclick="editPackage(
                                <?= $p['id'] ?>, 
                                '<?= htmlspecialchars(addslashes($p['destination'])) ?>', 
                                '<?= htmlspecialchars(addslashes($p['description'])) ?>', 
                                <?= $p['price'] ?>, 
                                '<?= htmlspecialchars(addslashes($p['duration'])) ?>', 
                                '<?= htmlspecialchars(addslashes($p['image'])) ?>'
                            )">Edit</button>

                                <form method="POST" class="delete-form" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                    <input type="hidden" name="package_id" value="<?= $p['id'] ?>">
                                    <button type="submit" name="delete_package" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB: ADD / EDIT PACKAGE -->
        <div id="tab-add" class="tab-content">
            <div style="max-width:600px;">
                <h3 id="formTitle" style="margin-bottom: 20px; color: var(--dark);">Add New Package</h3>

                <form method="POST" id="packageForm">
                    <!-- Hidden ID for updates -->
                    <input type="hidden" name="package_id" id="pkg_id" value="">

                    <div class="form-group">
                        <label>Destination Name *</label>
                        <input type="text" name="destination" id="pkg_dest" placeholder="e.g. Maldives" required>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" id="pkg_desc" rows="4" placeholder="Describe the package..." required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (₹) *</label>
                            <input type="number" name="price" id="pkg_price" placeholder="e.g. 75000" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Duration *</label>
                            <input type="text" name="duration" id="pkg_dur" placeholder="e.g. 5 Days / 4 Nights" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Image Path</label>
                        <input type="text" name="image" id="pkg_img" placeholder="images/maldives.jpg or https://...">
                    </div>

                    <div style="display:flex; gap: 10px; margin-top: 10px;">
                        <button type="submit" name="add_package" id="submitBtn" class="btn btn-primary">Add Package</button>
                        <button type="button" id="cancelEditBtn" class="btn btn-secondary" style="display:none;" onclick="resetForm()">Cancel Edit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB: BOOKINGS -->
        <div id="tab-bookings" class="tab-content">
            <table id="bookingsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Destination / Service</th>
                        <th>Travel Dates</th>
                        <th>Travelers</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($b = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['username']) ?><br><small><?= htmlspecialchars($b['email']) ?></small></td>
                            <td>
                                <strong><?= htmlspecialchars($b['destination']) ?></strong><br>
                                <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:11px; text-transform:uppercase;">
                                    <?= htmlspecialchars(str_replace('_', ' ', $b['service_type'] ?? 'tour_package')) ?>
                                </span>
                            </td>
                            <td>
                                <?= date('d M Y', strtotime($b['booking_date'])) ?>
                                <?php if (!empty($b['check_out_date'])): ?>
                                    <br><small style="color:#64748b;">to <?= date('d M Y', strtotime($b['check_out_date'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $b['travelers'] ?></td>
                            <td>₹<?= number_format($b['total_price'], 2) ?></td>
                            <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                            <td>
                                <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <select name="status" style="padding:5px; border-radius:5px; border:1px solid #ccc; font-size:13px;">
                                        <option value="pending" <?= $b['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_booking" class="btn btn-secondary btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB: PAYMENTS -->
        <div id="tab-payments" class="tab-content">
            <table id="paymentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Destination</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Transaction ID</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pay = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $pay['id'] ?></td>
                            <td><?= htmlspecialchars($pay['username']) ?></td>
                            <td><?= htmlspecialchars($pay['destination']) ?></td>
                            <td><?= ucwords(str_replace('_', ' ', $pay['payment_method'])) ?></td>
                            <td>₹<?= number_format($pay['amount'], 2) ?></td>
                            <td><span class="badge badge-<?= $pay['payment_status'] ?>"><?= ucfirst($pay['payment_status']) ?></span></td>
                            <td style="font-size:12px;"><?= htmlspecialchars($pay['transaction_id']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($pay['paid_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

    <footer>
        <p>© 2025 <span>TravelEase</span> Admin Panel</p>
    </footer>
    <script src="script.js"></script>
    <script>
        // ===== EDIT PACKAGE LOGIC =====
        function editPackage(id, dest, desc, price, duration, image) {
            // Change Tab
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
            document.getElementById('add-tab-btn').classList.add('active');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('tab-add').classList.add('active');

            // Change Titles and Buttons
            document.getElementById('formTitle').textContent = 'Update Package #' + id;
            document.getElementById('submitBtn').textContent = 'Save Changes';
            document.getElementById('submitBtn').name = 'update_package'; // Change form name attribute
            document.getElementById('cancelEditBtn').style.display = 'inline-block';

            // Populate Fields
            document.getElementById('pkg_id').value = id;
            document.getElementById('pkg_dest').value = dest;
            document.getElementById('pkg_desc').value = desc;
            document.getElementById('pkg_price').value = price;
            document.getElementById('pkg_dur').value = duration;
            document.getElementById('pkg_img').value = image;

            // Scroll to top of form
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function resetForm() {
            // Reset Titles and Buttons
            document.getElementById('formTitle').textContent = 'Add New Package';
            document.getElementById('submitBtn').textContent = 'Add Package';
            document.getElementById('submitBtn').name = 'add_package'; // Revert form name attribute
            document.getElementById('cancelEditBtn').style.display = 'none';

            // Clear fields
            document.getElementById('packageForm').reset();
            document.getElementById('pkg_id').value = '';
        }

        // ===== SEARCH FUNCTIONALITY =====
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const activeTab = document.querySelector('.tab-content.active');

            // Only search within the currently visible table
            if (activeTab) {
                const rows = activeTab.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            }
        });
    </script>
</body>

</html>