<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Dinas Sosial & Pemberdayaan Masyarakat Kota Tarakan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="logo">
                    <img src="assets/images/logo-tarakan.png" alt="Logo Kota Tarakan">
                    <div class="logo-text">
                        <h1>DINSOS & PM</h1>
                        <p>Kota Tarakan</p>
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?php echo $_SESSION['nama'] ?? 'Administrator'; ?></span>
                        <span class="user-role"><?php echo $_SESSION['jabatan'] ?? 'Super Admin'; ?></span>
                    </div>
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-container"></div>