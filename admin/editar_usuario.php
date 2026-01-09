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

    $id = $_GET['id'];

    // 2. PROCESO DE ACTUALIZACIÓN
    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("
            UPDATE tbl_users 
            SET first_name = :fname,
                last_name = :lname,
                email = :email,
                role = :role,
                city = :city
            WHERE member_no = :id
        ");

        $stmt->execute([
            ':fname' => $_POST['fname'],
            ':lname' => $_POST['lname'],
            ':email' => $_POST['email'],
            ':role'  => $_POST['role'],
            ':city'  => $_POST['city'],
            ':id'    => $id
        ]);

        header("location:usuarios.php?msg=updated");
        exit();
    }

    // 3. OBTENER DATOS ACTUALES
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE member_no = :id");
    $stmt->execute([':id' => $id]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$u) {
        header("location:usuarios.php");
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
    <title>Editar Perfil | EmpleaTec</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* ESTILO COHERENTE CON DASHBOARD Y USUARIOS */
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
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 5px;
            text-transform: lowercase;
        }

        /* FORMULARIO ESTILO PERFIL */
        .form-section {
            margin-top: 30px;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        .form-label {
            color: #888;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .form-control, .form-select {
            border: none;
            border-bottom: 1px solid #eee;
            border-radius: 0;
            padding: 10px 0;
            font-size: 1rem;
            color: #333;
            margin-bottom: 25px;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-bottom: 1px solid #e30613;
        }

        /* BOTONES */
        .btn-update {
            background-color: #333;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 4px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-update:hover {
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

        .btn-back:hover {
            background: #f8f9fa;
            color: #333;
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
        <a href="../logout.php">Cerrar sesión</a>
    </div>
</div>

<div class="container-main">
    
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="page-title">editar perfil usuario</h1>
            <p class="text-muted mb-0">Modifica los datos del registro #<?php echo $u['member_no']; ?></p>
        </div>
        <a href="usuarios.php" class="btn-back">CANCELAR</a>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-section shadow-sm">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="fname" class="form-control" value="<?php echo $u['first_name']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="lname" class="form-control" value="<?php echo $u['last_name']; ?>" required>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $u['email']; ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ciudad / Ubicación</label>
                    <input type="text" name="city" class="form-control" value="<?php echo $u['city']; ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tipo de Usuario (Rol)</label>
                    <select name="role" class="form-select">
                        <option value="employee" <?php if($u['role']=='employee') echo 'selected'; ?>>Candidato</option>
                        <option value="employer" <?php if($u['role']=='employer') echo 'selected'; ?>>Empresa</option>
                        <option value="admin" <?php if($u['role']=='admin') echo 'selected'; ?>>Administrador</option>
                    </select>
                </div>

                <div class="col-md-12 mt-4 text-end">
                    <button type="submit" name="update" class="btn-update">
                        <i class="fas fa-save me-2"></i> Actualizar Registro
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

</body>
</html>