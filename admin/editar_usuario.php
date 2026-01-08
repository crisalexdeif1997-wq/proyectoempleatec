<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit();
}
require '../constants/db_config.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'];

// ACTUALIZAR
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
}

// DATOS ACTUALES
$stmt = $conn->prepare("SELECT * FROM tbl_users WHERE member_no = :id");
$stmt->execute([':id' => $id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Usuario | EmpleaTec</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body{
    background:#f8f9fa;
    font-family:'Poppins',sans-serif;
}

/* CARD */
.edit-card{
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,0.1);
    border:none;
}

/* HEADER */
.profile-header{
    background:#e30613;
    padding:40px;
    text-align:center;
    color:#fff;
}

.profile-img{
    width:120px;
    height:120px;
    border-radius:50%;
    border:5px solid #fff;
    margin-bottom:15px;
}

/* FORM */
.form-label{
    font-weight:600;
    font-size:0.9rem;
    color:#555;
}

.form-control,
.form-select{
    border-radius:12px;
    padding:12px;
    border:1px solid #ddd;
}

.form-control:focus,
.form-select:focus{
    border-color:#e30613;
    box-shadow:0 0 0 0.15rem rgba(227,6,19,0.15);
}

/* BUTTON */
.btn-empleatec{
    background:#e30613;
    border:none;
    color:#fff;
}

.btn-empleatec:hover{
    background:#c20510;
}

/* BACK */
.back-link{
    color:#555;
    font-weight:600;
    text-decoration:none;
}

.back-link:hover{
    color:#e30613;
}
</style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="mb-4">
                <a href="usuarios.php" class="back-link">
                    <i class="fas fa-arrow-left me-2"></i> Volver a usuarios
                </a>
            </div>

            <div class="card edit-card">
                <div class="profile-header">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['first_name']); ?>&background=ffffff&color=e30613"
                         class="profile-img">
                    <h3 class="mb-1"><?php echo $u['first_name']." ".$u['last_name']; ?></h3>
                    <small>ID Usuario #<?php echo $u['member_no']; ?></small>
                </div>

                <div class="card-body p-5">
                    <form method="POST">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="fname" class="form-control"
                                       value="<?php echo $u['first_name']; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="lname" class="form-control"
                                       value="<?php echo $u['last_name']; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?php echo $u['email']; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="city" class="form-control"
                                       value="<?php echo $u['city']; ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Rol</label>
                                <select name="role" class="form-select">
                                    <option value="employee" <?php if($u['role']=='employee') echo 'selected'; ?>>
                                        Candidato
                                    </option>
                                    <option value="employer" <?php if($u['role']=='employer') echo 'selected'; ?>>
                                        Empresa
                                    </option>
                                    <option value="admin" <?php if($u['role']=='admin') echo 'selected'; ?>>
                                        Administrador
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-12 mt-4">
                                <button type="submit" name="update"
                                        class="btn btn-empleatec w-100 py-3 rounded-pill fw-bold">
                                    <i class="fas fa-save me-2"></i> Guardar cambios
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
