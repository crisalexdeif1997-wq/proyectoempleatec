<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit();
}
require '../constants/db_config.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* EXPORTAR CSV */
if (isset($_GET['export'])) {
    $tipo = $_GET['export'];
    $filename = "reporte_" . $tipo . "_" . date('Ymd') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes | EmpleaTec</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body{
    background:#f8f9fa;
    font-family:'Poppins',sans-serif;
}

/* HEADER */
.header-box{
    background:#ffffff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

/* CARD */
.report-card{
    background:#ffffff;
    border-radius:22px;
    padding:30px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    transition:0.3s;
    border-bottom:5px solid transparent;
    height:100%;
}

.report-card:hover{
    transform:translateY(-8px);
    border-bottom:5px solid #e30613;
}

/* ICON */
.icon-box{
    width:65px;
    height:65px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:26px;
    background:rgba(227,6,19,0.15);
    color:#e30613;
    margin:0 auto 20px;
}

/* BUTTONS */
.btn-empleatec{
    background:#e30613;
    color:#fff;
    border:none;
}

.btn-empleatec:hover{
    background:#c20510;
    color:#fff;
}

.btn-outline-empleatec{
    border:2px solid #e30613;
    color:#e30613;
}

.btn-outline-empleatec:hover{
    background:#e30613;
    color:#fff;
}

/* INFO */
.info-box{
    background:#ffffff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}
</style>
</head>

<body>

<div class="container py-5">

    <!-- HEADER -->
    <div class="header-box mb-5 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Centro de Reportes</h3>
            <p class="text-muted mb-0">
                Genera y descarga información de la plataforma en Excel / CSV
            </p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-empleatec rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <!-- CARDS -->
    <div class="row g-4 justify-content-center">

        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div class="icon-box">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="fw-bold">Usuarios Totales</h5>
                <p class="text-muted small">
                    Nombres, correos, roles y ubicación de los usuarios registrados.
                </p>
                <a href="reportes.php?export=usuarios"
                   class="btn btn-outline-empleatec w-100 rounded-pill mt-3">
                    <i class="fas fa-download me-2"></i> Descargar CSV
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div class="icon-box">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h5 class="fw-bold">Vacantes Publicadas</h5>
                <p class="text-muted small">
                    Listado completo de empleos y categorías registradas.
                </p>
                <a href="reportes.php?export=empleos"
                   class="btn btn-outline-empleatec w-100 rounded-pill mt-3">
                    <i class="fas fa-download me-2"></i> Descargar CSV
                </a>
            </div>
        </div>



    </div>

    <!-- INFO -->
    <div class="info-box mt-5 d-flex align-items-center">
        <i class="fas fa-info-circle text-danger fs-2 me-3"></i>
        <div>
            <h6 class="fw-bold mb-1">Formato de archivos</h6>
            <p class="text-muted small mb-0">
                Los reportes se generan en CSV UTF-8 compatibles con Excel,
                Google Sheets y LibreOffice.
            </p>
        </div>
    </div>

</div>

</body>
</html>
