<div class="sidebar">
  <h4 class="text-center mb-4">☕ CoffeeWare</h4>

  <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-people"></i> Users
  </a>

  <!-- Dropdown Menu -->
  <a class="d-flex justify-content-between align-items-center" 
     data-bs-toggle="collapse" 
     href="#menuDropdown" 
     role="button" 
     aria-expanded="false" 
     aria-controls="menuDropdown">
    <span><i class="bi bi-cup-hot"></i> Menu</span>
    <i class="bi bi-caret-down-fill small"></i>
  </a>

  <div class="collapse submenu" id="menuDropdown">
    <a href="menus.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == '') ? 'active fw-bold text-primary' : '' ?>">
      All Menu
    </a>
    <a href="menus.php?category=coffee" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'coffee') ? 'active fw-bold text-primary' : '' ?>">
      Coffee
    </a>
    <a href="menus.php?category=non-coffee" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'non-coffee') ? 'active fw-bold text-primary' : '' ?>">
      Non-Coffee
    </a>
    <a href="menus.php?category=main course" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'main course') ? 'active fw-bold text-primary' : '' ?>">
      Main Course
    </a>
    <a href="menus.php?category=snack" class="<?= (basename($_SERVER['PHP_SELF']) == 'menus.php' && ($_GET['category'] ?? '') == 'snack') ? 'active fw-bold text-primary' : '' ?>">
      Snacks
    </a>
  </div>

  <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi bi-cart4"></i> Orders
  </a>

  <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active fw-bold text-primary' : '' ?>">
    <i class="bi-file-earmark-text"></i> Reports
  </a>
</div>