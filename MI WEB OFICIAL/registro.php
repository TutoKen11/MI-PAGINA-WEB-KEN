<?php
session_start();


$host = "sql213.infinityfree.com"; 
$user = "if0_39016029";           
$password = "Kenkendalisay09";         
$dbname = "if0_39016029_urbanproject";


$conn = new mysqli($host, $user, $password, $dbname);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm-password'];
$nombre_completo = trim($_POST['nombre']);


if ($password !== $confirm_password) {
    header("Location: registrov1.html?error=Las contraseñas no coinciden");
    exit();
}

if (strlen($password) < 6) {
    header("Location: registrov1.html?error=La contraseña debe tener al menos 6 caracteres");
    exit();
}

$sql = "SELECT id FROM usuarios WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: registrov1.html?error=El nombre de usuario ya está en uso");
    exit();
}


$sql_email = "SELECT id FROM clientes WHERE correo = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("s", $email);
$stmt_email->execute();
$stmt_email->store_result();

if ($stmt_email->num_rows > 0) {
    header("Location: registrov1.html?error=El correo electrónico ya está registrado");
    exit();
}


$hashed_password = password_hash($password, PASSWORD_DEFAULT);


$conn->begin_transaction();

try {
  
    $insert_user = $conn->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
    $insert_user->bind_param("ss", $username, $hashed_password);
    $insert_user->execute();
    $user_id = $conn->insert_id;
    

    $nombres = explode(' ', $nombre_completo);
    $nombre = $nombres[0];
    $apellidos = count($nombres) > 1 ? implode(' ', array_slice($nombres, 1)) : '';
    
    $insert_cliente = $conn->prepare("INSERT INTO clientes (usuario_id, nombre, apellidos, correo) VALUES (?, ?, ?, ?)");
    $insert_cliente->bind_param("isss", $user_id, $nombre, $apellidos, $email);
    $insert_cliente->execute();
    
  
    $conn->commit();
    
  
    header("Location: exito.html");
    exit();
    
} catch (Exception $e) {
 
    $conn->rollback();
    header("Location: registrov1.html?error=Error al registrar el usuario");
    exit();
}

$stmt->close();
$stmt_email->close();
$conn->close();
?>