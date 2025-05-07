<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: login.php");
    exit();
}

// Get current page for active menu highlight
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile sidebar toggle button that remains visible -->
<div class="mobile-sidebar-toggle" id="mobile-sidebar-toggle">
    <i class="fas fa-bars"></i>
</div>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>WildAlert</h3>
        <div class="sidebar-toggle" id="sidebar-toggle">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <div class="sidebar-user">
        <div class="user-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="user-info">
            <span>Administrator</span>
            <small><?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Admin'; ?></small>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?php echo (in_array($current_page, ['species.php', 'add_species.php', 'edit_species.php'])) ? 'active' : ''; ?>">
            <a href="#" class="menu-toggle">
                <i class="fas fa-leaf"></i>
                <span>Species</span>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li class="<?php echo ($current_page == 'species.php') ? 'active' : ''; ?>">
                    <a href="species.php">View All</a>
                </li>
                <li class="<?php echo ($current_page == 'add_species.php') ? 'active' : ''; ?>">
                    <a href="add_species.php">Add Species</a>
                </li>
            </ul>
        </li>
        <li class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
            <a href="reports.php">
                <i class="fas fa-flag"></i>
                <span>Reports</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'audit_trail.php') ? 'active' : ''; ?>">
            <a href="audit_trail.php">
                <i class="fas fa-history"></i>
                <span>Audit Trail</span>
            </a>
        </li>
        <li>
            <a href="../components/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</div>

<style>
/* Mobile sidebar toggle button styles */
.mobile-sidebar-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1000;
    background-color: #4CAF50;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 4px;
    text-align: center;
    line-height: 40px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

/* Add responsive styles */
@media screen and (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .mobile-sidebar-toggle {
        display: block;
    }
    
    .content-wrapper {
        margin-left: 0;
        width: 100%;
        transition: margin-left 0.3s ease;
    }
    
    .content-wrapper.with-sidebar {
        margin-left: 250px; /* or whatever your sidebar width is */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle submenu
    const menuToggles = document.querySelectorAll('.menu-toggle');
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });

    // Mobile sidebar toggle
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    
    // Close sidebar (inside the sidebar)
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('active');
            if (contentWrapper) {
                contentWrapper.classList.add('with-sidebar');
            }
        });
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.remove('active');
            if (contentWrapper) {
                contentWrapper.classList.remove('with-sidebar');
            }
        });
    }

    // Auto-open current submenu if parent is active
    const activeSubmenuItem = document.querySelector('.submenu .active');
    if (activeSubmenuItem) {
        activeSubmenuItem.closest('li.active').classList.add('open');
    }
    
    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInside = sidebar.contains(event.target) || 
                             mobileSidebarToggle.contains(event.target);
                             
        if (!isClickInside && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            if (contentWrapper) {
                contentWrapper.classList.remove('with-sidebar');
            }
        }
    });
});
</script>