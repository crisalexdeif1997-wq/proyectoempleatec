<?php
session_start();

// 1. DIAGNÓSTICO
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. SEGURIDAD
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit();
}

require '../constants/db_config.php';

$total_usuarios = 0;
$total_empleos = 0;
$total_empresas = 0;
$myfname = isset($_SESSION['myfname']) ? $_SESSION['myfname'] : "Administrador";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $total_usuarios = $conn->query("SELECT COUNT(*) FROM tbl_users")->fetchColumn();
    try { $total_empleos = $conn->query("SELECT COUNT(*) FROM tbl_jobs")->fetchColumn(); } catch (Exception $e) { }
    try { $total_empresas = $conn->query("SELECT COUNT(*) FROM tbl_users WHERE role = 'employer'")->fetchColumn(); } catch (Exception $e) { }

} catch(PDOException $e) {
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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* ESTILO BASADO EN TU IMAGEN DE REFERENCIA */
        body { 
            font-family: 'Open Sans', sans-serif; 
            background-color: #ffffff; 
            margin: 0; 
        }

        /* BARRA SUPERIOR NEGRA (Como tu header) */
        .navbar-custom {
            background-color: #000000;
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar-custom h3 { margin: 0; font-size: 1.5rem; font-weight: bold; }
        .navbar-custom h3 span { color: #e30613; } /* Mantengo el estilo de Empleatec */

        .nav-links-top a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-left: 20px;
            letter-spacing: 1px;
            opacity: 0.8;
        }
        .nav-links-top a:hover { opacity: 1; }

        /* CONTENEDOR PRINCIPAL */
        .container-main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 5px;
        }

        .user-welcome {
            color: #666;
            margin-bottom: 40px;
            font-size: 1rem;
        }

        /* TARJETAS ESTILO PERFIL */
        .stat-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #999;
            font-size: 1.5rem;
            border: 1px solid #eee;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #222;
            margin: 5px 0;
        }

        .stat-label {
            color: #888;
            font-size: 0.9rem;
            text-transform: none;
        }

        .btn-logout-small {
            border: 1px solid #fff;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 0.75rem;
            transition: 0.3s;
        }
    </style>
</head>
<body>

<div class="navbar-custom">
    <div>
        <h3>EMPLEA<span>TEC</span></h3>
    </div>
    <div class="nav-links-top d-none d-md-block">
        <a href="dashboard.php">Inicio</a>
        <a href="usuarios.php">Usuarios</a>
        <a href="reportes.php">Reportes</a>
        <a href="../logout.php" class="btn-logout-small">Cerrar sesión</a>
    </div>
</div>

<div class="container-main">
    
    <h1 class="page-title">perfil administrativo</h1>
    <p class="user-welcome">Ultimo ingreso: <?php echo date('d-m-Y H:i A'); ?></p>

    <hr class="mb-5" style="opacity: 0.1;">

    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-circle">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-label">Usuarios Registrados</div>
                <div class="stat-value"><?php echo $total_usuarios; ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-circle">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-label">Empleos Activos</div>
                <div class="stat-value"><?php echo $total_empleos; ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-circle">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-label">Empresas Aliadas</div>
                <div class="stat-value"><?php echo $total_empresas; ?></div>
            </div>
        </div>
    </div>

    <div class="mt-5 p-4 border rounded shadow-sm">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="text-muted small d-block">Administrador en turno</label>
                <div class="border-bottom py-2"><?php echo htmlspecialchars($myfname); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small d-block">Correo Electrónico</label>
             <div class="border-bottom py-2"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : (isset($_SESSION['myemail']) ? $_SESSION['myemail'] : 'admin@int'); ?></div>
            </div>
        </div>
    </div>

</div>

</body>
</html>