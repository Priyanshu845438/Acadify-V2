<?php
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

try {
    $pdo = getDatabaseConnection();
    
    // Get contact entries
    $contact_stmt = $pdo->prepare("SELECT * FROM contact_entries ORDER BY created_at DESC");
    $contact_stmt->execute();
    $contact_entries = $contact_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get partner entries
    $partner_stmt = $pdo->prepare("SELECT * FROM partner_entries ORDER BY created_at DESC");
    $partner_stmt->execute();
    $partner_entries = $partner_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get quote requests
    $quote_stmt = $pdo->prepare("SELECT * FROM quote_requests ORDER BY created_at DESC");
    $quote_stmt->execute();
    $quote_requests = $quote_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get counts
    $contact_count = count($contact_entries);
    $partner_count = count($partner_entries);
    $quote_count = count($quote_requests);
    
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ideovent Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #0d3269 0%, #1a4582 100%);
            min-height: 100vh;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            border-radius: 8px;
            margin: 5px 0;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white !important;
        }
        .content-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .stats-card {
            background: linear-gradient(135deg, #0d3269 0%, #1a4582 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center pb-3 mb-3 border-bottom border-secondary">
                        <h5 class="text-white">Admin Panel</h5>
                        <small class="text-white-50">Ideovent Technologies</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-tab="overview">
                                <i class="bi bi-speedometer2"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-tab="contacts">
                                <i class="bi bi-envelope"></i> Contact Entries (<?php echo $contact_count; ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-tab="partners">
                                <i class="bi bi-handshake"></i> Partner Entries (<?php echo $partner_count; ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-tab="quotes">
                                <i class="bi bi-calculator"></i> Quote Requests (<?php echo $quote_count; ?>)
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="?logout=1">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="content-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
                    </div>
                </div>

                <!-- Overview Tab -->
                <div id="overview-tab" class="tab-content">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-envelope display-4 mb-2"></i>
                                    <h3><?php echo $contact_count; ?></h3>
                                    <p class="mb-0">Contact Entries</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-handshake display-4 mb-2"></i>
                                    <h3><?php echo $partner_count; ?></h3>
                                    <p class="mb-0">Partner Entries</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calculator display-4 mb-2"></i>
                                    <h3><?php echo $quote_count; ?></h3>
                                    <p class="mb-0">Quote Requests</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Entries Tab -->
                <div id="contacts-tab" class="tab-content" style="display: none;">
                    <h3>Contact Entries</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contact_entries as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['id']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['phone'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($entry['message'] ?? '', 0, 50)) . (strlen($entry['message'] ?? '') > 50 ? '...' : ''); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($entry['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($contact_entries)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No contact entries found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Partner Entries Tab -->
                <div id="partners-tab" class="tab-content" style="display: none;">
                    <h3>Partner Entries</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Business Type</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($partner_entries as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['id']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['contact_person']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['phone'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($entry['business_type'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($entry['message'] ?? '', 0, 50)) . (strlen($entry['message'] ?? '') > 50 ? '...' : ''); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($entry['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($partner_entries)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No partner entries found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quote Requests Tab -->
                <div id="quotes-tab" class="tab-content" style="display: none;">
                    <h3>Quote Requests</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Company</th>
                                    <th>Service</th>
                                    <th>Budget</th>
                                    <th>Timeline</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quote_requests as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['id']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['phone'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($entry['company'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($entry['service_type']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['project_budget'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($entry['project_timeline'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($entry['project_description'] ?? '', 0, 50)) . (strlen($entry['project_description'] ?? '') > 50 ? '...' : ''); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($entry['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($quote_requests)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No quote requests found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab switching functionality
        document.querySelectorAll('[data-tab]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                document.querySelectorAll('[data-tab]').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
                
                // Show selected tab
                const tabId = this.getAttribute('data-tab') + '-tab';
                const tabElement = document.getElementById(tabId);
                if (tabElement) {
                    tabElement.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>