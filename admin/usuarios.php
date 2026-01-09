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

    // 2. ELIMINAR USUARIO
    if (isset($_GET['del'])) {
        $stmt = $conn->prepare("DELETE FROM tbl_users WHERE member_no = :id");
        $stmt->bindParam(':id', $_GET['del']);
        $stmt->execute();
        header("location:usuarios.php");
        exit();
    }

    // 3. OBTENER USUARIOS (Excepto admins)
    $usuarios = $conn->query("
        SELECT * FROM tbl_users 
        WHERE role != 'admin' 
        ORDER BY member_no DESC 
        LIMIT 50
    ")->fetchAll();

} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios | EmpleaTec</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* ESTILO BASADO EN TU REFERENCIA */
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

        /* CONTENEDOR */
        .container-main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 5px;
            text-transform: lowercase; /* Como en tu imagen */
        }

        /* TABLA MINIMALISTA */
        .table-container {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 30px;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: #666;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #444;
            border-bottom: 1px solid #eee;
        }

        /* BADGES */
        .badge-role {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-employer { background: #fee2e2; color: #dc2626; }
        .role-employee { background: #f1f5f9; color: #475569; }

        /* ACCIONES */
        .btn-action {
            color: #999;
            margin-left: 10px;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-action:hover { color: #e30613; }
        .btn-delete:hover { color: #dc3545; }

        .btn-back {
            border: 1px solid #333;
            color: #333;
            padding: 5px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #333;
            color: #fff;
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
        <a href="usuarios.php" style="border-bottom: 2px solid #e30613;">Usuarios</a>
        <a href="reportes.php">Reportes</a>
        <a href="../logout.php">Cerrar sesión</a>
    </div>
</div>

<div class="container-main">
    
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="page-title">gestión de usuarios</h1>
            <p class="text-muted mb-0">Listado de candidatos y empresas registradas en el sistema.</p>
        </div>
        <a href="dashboard.php" class="btn-back">VOLVER AL PANEL</a>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Rol / Tipo</th>
                    <th>Ciudad</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['first_name']); ?>&background=f1f1f1&color=333" 
                                 class="rounded-circle me-3" width="35">
                            <div>
                                <strong><?php echo $u['first_name']." ".$u['last_name']; ?></strong><br>
                                <small class="text-muted"><?php echo $u['member_no']; ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?php echo $u['email']; ?></td>
                    <td>
                        <span class="badge-role <?php echo $u['role']=='employer'?'role-employer':'role-employee'; ?>">
                            <?php echo $u['role'] == 'employer' ? 'Empresa' : 'Candidato'; ?>
                        </span>
                    </td>
                    <td>
                        <small><i class="fas fa-map-marker-alt text-muted me-1"></i> <?php echo $u['city'] ?: 'No definida'; ?></small>
                    </td>
                    <td class="text-end">
                        <a href="editar_usuario.php?id=<?php echo $u['member_no']; ?>" class="btn-action" title="Editar">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="usuarios.php?del=<?php echo $u['member_no']; ?>" 
                           class="btn-action btn-delete" 
                           onclick="return confirm('¿Está seguro de eliminar este registro?')"
                           title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
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