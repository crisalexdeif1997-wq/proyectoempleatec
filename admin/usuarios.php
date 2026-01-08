<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit();
}
require '../constants/db_config.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['del'])) {
    $stmt = $conn->prepare("DELETE FROM tbl_users WHERE member_no = :id");
    $stmt->bindParam(':id', $_GET['del']);
    $stmt->execute();
    header("location:usuarios.php");
}

$usuarios = $conn->query("
    SELECT * FROM tbl_users 
    WHERE role != 'admin' 
    ORDER BY member_no DESC 
    LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios | EmpleaTec</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f8f9fa;
}

/* HEADER */
.header-box{
    background:#ffffff;
    padding:25px 30px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

/* TABLE CARD */
.table-card{
    background:#ffffff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

/* TABLE */
.table thead{
    background:#f1f1f1;
}
.table th{
    font-weight:600;
    color:#333;
    border-bottom:0;
}
.table td{
    vertical-align:middle;
}

/* BADGES */
.badge-role{
    padding:6px 14px;
    border-radius:20px;
    font-size:0.75rem;
    font-weight:600;
}

.role-employer{
    background:rgba(227,6,19,0.12);
    color:#e30613;
}

.role-employee{
    background:rgba(0,0,0,0.08);
    color:#333;
}

/* BUTTONS */
.btn-action{
    width:36px;
    height:36px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:#f1f1f1;
    transition:0.3s;
}

.btn-action.edit{
    color:#e30613;
}
.btn-action.delete{
    color:#dc3545;
}

.btn-action:hover{
    background:#e30613;
    color:#fff;
}

/* AVATAR */
.avatar{
    width:42px;
    height:42px;
}
</style>
</head>

<body>

<div class="container py-5">

    <!-- HEADER -->
    <div class="header-box d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Gestión de Usuarios</h4>
            <p class="text-muted mb-0">Administra empresas y candidatos registrados</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-danger rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4 py-3">Usuario</th>
                    <th>Contacto</th>
                    <th>Rol</th>
                    <th>Ubicación</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['first_name']); ?>&background=e30613&color=fff"
                                 class="rounded-circle avatar me-3">
                            <div>
                                <div class="fw-semibold">
                                    <?php echo $u['first_name']." ".$u['last_name']; ?>
                                </div>
                                <small class="text-muted">ID: <?php echo $u['member_no']; ?></small>
                            </div>
                        </div>
                    </td>

                    <td><?php echo $u['email']; ?></td>

                    <td>
                        <span class="badge-role <?php echo $u['role']=='employer'?'role-employer':'role-employee'; ?>">
                            <?php echo strtoupper($u['role']); ?>
                        </span>
                    </td>

                    <td>
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                        <?php echo $u['city']; ?>
                    </td>

                    <td class="text-end pe-4">
                        <a href="editar_usuario.php?id=<?php echo $u['member_no']; ?>"
                           class="btn-action edit me-2" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="usuarios.php?del=<?php echo $u['member_no']; ?>"
                           class="btn-action delete"
                           onclick="return confirm('¿Seguro que deseas eliminar este usuario?')"
                           title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
