<?php
session_start();


$host = "sql213.infinityfree.com"; 
$user = "if0_39016029";           
$password = "Kenkendalisay09";         
$dbname = "if0_39016029_urbanproject";


$conn = new mysqli($host, $user, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$username = $conn->real_escape_string($_POST['username']);
$password = $_POST['password'];


$sql = "SELECT u.id, u.password, c.nombre FROM usuarios u 
        LEFT JOIN clientes c ON u.id = c.usuario_id 
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    
    if (password_verify($password, $row['password'])) {

        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['username'] = $username;
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['last_login'] = time();
        

        echo '<script>
                alert("¡Has iniciado sesión correctamente!");
                window.location.href = "index.html";
              </script>';
        exit();
    } else {

        header("Location: cuenta.html?error=Contraseña incorrecta&username=".urlencode($username));
        exit();
    }
} else {

    header("Location: cuenta.html?error=Usuario no encontrado. Por favor, regístrese.");
    exit();
}

$stmt->close();
$conn->close();
?>