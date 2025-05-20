<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');


$data = json_decode(file_get_contents("php://input"), true);
$nombre = trim($data['nombre'] ?? '');
$total = floatval($data['total'] ?? 0);


if (!$nombre || $total <= 0) {
    echo json_encode(['success' => false, 'message' => 'Nombre o total inválido']);
    exit;
}


$host = "sql213.infinityfree.com";
$user = "if0_39016029";
$password = "Kenkendalisay09";
$dbname = "if0_39016029_urbanproject";

$conn = new mysqli($host, $user, $password, $dbname);


if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}


$stmt = $conn->prepare("SELECT id FROM clientes WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if ($cliente) {
    $cliente_id = $cliente['id'];
} else {
 
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, apellidos, correo) VALUES (?, '', '')");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $cliente_id = $stmt->insert_id;
}


$stmt = $conn->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
$stmt->bind_param("id", $cliente_id, $total);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar el pedido']);
}

$conn->close();
?>
