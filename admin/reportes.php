<?php
session_start();

// 1. SEGURIDAD
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit();
}

require '../constants/db_config.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* EXPORTAR CSV */
    if (isset($_GET['export'])) {
        $tipo = $_GET['export'];
        $filename = "reporte_" . $tipo . "_" . date('Ymd') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        // BOM para que Excel reconozca tildes y eñes
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if ($tipo == 'usuarios') {
            fputcsv($output, ['ID','Nombre','Apellido','Email','Rol','Ciudad']);
            $query = $conn->query("SELECT member_no, first_name, last_name, email, role, city FROM tbl_users");
        } else {
            fputcsv($output, ['ID Empleo','Título','Ciudad','Categoría','Fecha']);
            $query = $conn->query("SELECT job_id, title, city, category, date_posted FROM tbl_jobs");
        }

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    }

} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | EmpleaTec</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* ESTILO COHERENTE CON TU IMAGEN DE REFERENCIA */
        body { 
            font-family: 'Open Sans', sans-serif; 
            background-color: #ffffff; 
            margin: 0; 
        }

        /* BARRA SUPERIOR NEGRA */
        .navbar-custom {
            background-color: #000000;
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar-custom h3 { margin: 0; font-size: 1.5rem; font-weight: bold; }
        .navbar-custom h3 span { color: #e30613; }

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

        /* CONTENEDOR CENTRAL */
        .container-main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 5px;
            text-transform: lowercase;
        }

        /* TARJETAS DE REPORTE ESTILO PERFIL */
        .report-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }

        .report-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-color: #333;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #333;
            font-size: 1.8rem;
            border: 1px solid #eee;
        }

        .btn-download {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 4px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: 0.3s;
        }

        .btn-download:hover {
            background-color: #000;
            color: #fff;
        }

        .btn-back {
            border: 1px solid #ddd;
            color: #666;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.3s;
        }

        /* PIE DE PÁGINA INFO */
        .info-footer {
            margin-top: 50px;
            padding: 20px;
            background-color: #fcfcfc;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 0.85rem;
            border-radius: 4px;
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
        <a href="reportes.php" style="border-bottom: 2px solid #e30613;">Reportes</a>
        <a href="../logout.php">Cerrar sesión</a>
    </div>
</div>

<div class="container-main">
    
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h1 class="page-title">centro de reportes</h1>
            <p class="text-muted mb-0">Generación de archivos descargables para auditoría.</p>
        </div>
        <a href="dashboard.php" class="btn-back">VOLVER</a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="report-card">
                <div class="icon-circle">
                    <i class="fas fa-file-csv"></i>
                </div>
                <h5 class="fw-bold mb-3">Base de Datos Usuarios</h5>
                <p class="text-muted small mb-4">
                    Exporta la lista completa de candidatos y empresas con sus correos y ciudades de registro.
                </p>
                <a href="reportes.php?export=usuarios" class="btn-download">
                    <i class="fas fa-download me-2"></i> Generar CSV
                </a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="report-card">
                <div class="icon-circle">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h5 class="fw-bold mb-3">Historico de Vacantes</h5>
                <p class="text-muted small mb-4">
                    Descarga el consolidado de ofertas laborales publicadas y sus categorías correspondientes.
                </p>
                <a href="reportes.php?export=empleos" class="btn-download">
                    <i class="fas fa-download me-2"></i> Generar CSV
                </a>
            </div>
        </div>
    </div>

    <div class="info-footer">
        <i class="fas fa-info-circle me-2"></i> 
        Los archivos se generan bajo el estándar <strong>UTF-8</strong>. Si tiene problemas al abrirlos en Excel, use la función "Obtener datos desde texto/CSV".
    </div>

</div>

</body>
</html>