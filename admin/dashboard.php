<?php
session_start();

// 1. ACTIVAR ERRORES PARA DIAGNÓSTICO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. VERIFICACIÓN DE SESIÓN (Mantenemos la seguridad)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit();
}

require '../constants/db_config.php';

// Variables por defecto para que el diseño no se rompa
$total_usuarios = 0;
$total_empleos = 0;
$total_empresas = 0;
$myfname = isset($_SESSION['myfname']) ? $_SESSION['myfname'] : "Admin";

try {
    // 3. CONEXIÓN A LA BASE DE DATOS
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. CONSULTAS PROTEGIDAS (Si una tabla no existe, el resto sigue funcionando)
    
    // Conteo de Usuarios
    $stmt1 = $conn->query("SELECT COUNT(*) FROM tbl_users");
    $total_usuarios = $stmt1->fetchColumn();

    // Conteo de Empleos (Protegido por si la tabla tbl_jobs no existe aún)
    try {
        $stmt2 = $conn->query("SELECT COUNT(*) FROM tbl_jobs");
        $total_empleos = $stmt2->fetchColumn();
    } catch (Exception $e) { $total_empleos = 0; }

    // Conteo de Empresas
    try {
        $stmt3 = $conn->query("SELECT COUNT(*) FROM tbl_users WHERE role = 'employer'");
        $total_empresas = $stmt3->fetchColumn();
    } catch (Exception $e) { $total_empresas = 0; }

} catch(PDOException $e) {
    // Si la conexión falla, mostramos el error pero permitimos que cargue el HTML
    $error_db = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | EmpleaTec</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body{ font-family:'Poppins',sans-serif; background:#f8f9fa; display:flex; min-height:100vh; margin:0; }
        .sidebar{ width:260px; background:#ffffff; border-right:1px solid #e0e0e0; padding:25px; position:fixed; height:100vh; }
        .sidebar h3{ font-weight:700; }
        .sidebar h3 span{ color:#e30613; }
        .nav-link{ color:#333; padding:12px 15px; border-radius:8px; margin-bottom:8px; font-weight:500; text-decoration:none; display: block; }
        .nav-link:hover, .nav-link.active{ background:#e30613; color:#fff !important; }
        .main-content{ margin-left:260px; padding:40px; width:calc(100% - 260px); }
        .header-box{ background:#ffffff; padding:20px 30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.05); }
        .stat-card{ background:#ffffff; border-radius:18px; padding:25px; box-shadow:0 10px 30px rgba(0,0,0,0.08); position:relative; overflow:hidden; }
        .stat-card i{ font-size:2.8rem; color:#e30613; }
        .stat-title{ color:#777; font-size:0.95rem; }
        .stat-value{ font-size:2.2rem; font-weight:700; }
        .chart-container{ background:#ffffff; padding:30px; border-radius:20px; box-shadow:0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-5">
        <h3>EMPLEA<span>TEC</span></h3>
        <small class="text-muted">ADMINISTRADOR</small>
    </div>

    <nav class="nav flex-column">
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-chart-pie me-2"></i> Dashboard</a>
        <a href="usuarios.php" class="nav-link"><i class="fas fa-users me-2"></i> Usuarios</a>
        <a href="reportes.php" class="nav-link"><i class="fas fa-file-alt me-2"></i> Reportes</a>
        <div class="mt-5">
            <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión</a>
        </div>
    </nav>
</div>

<div class="main-content">

    <?php if(isset($error_db)): ?>
        <div class="alert alert-danger"><b>Error de conexión:</b> <?php echo $error_db; ?></div>
    <?php endif; ?>

    <div class="header-box d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Vista General</h4>
        <div class="d-flex align-items-center">
            <span class="me-3 text-muted">Hola, <strong><?php echo htmlspecialchars($myfname); ?></strong></span>
            <img src="https://ui-avatars.com/api/?name=Admin&background=e30613&color=fff" class="rounded-circle" width="45">
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-users mb-3"></i>
                <div class="stat-title">Usuarios Registrados</div>
                <div class="stat-value"><?php echo $total_usuarios; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-briefcase mb-3"></i>
                <div class="stat-title">Empleos Activos</div>
                <div class="stat-value"><?php echo $total_empleos; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-building mb-3"></i>
                <div class="stat-title">Empresas Aliadas</div>
                <div class="stat-value"><?php echo $total_empresas; ?></div>
            </div>
        </div>
    </div>


</div>


</body>
</html>