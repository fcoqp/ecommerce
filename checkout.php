<?php
session_start();

require 'vendor/autoload.php';

// Configura clave secreta de Stripe
\Stripe\Stripe::setApiKey('sk_test_51SlAbyHLwGM8egU0vqWtUQgmU2IgV8YRDrDsm8DvbJUkxAys2U3VtiLfG7OuQmKEFuWV4D8nmSvEoJbc5tt1DsV900igzkRiIr');

include 'includes/conectar_db.php';
$con = conectar();

// Verificar que el carrito no esté vacío
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
if (empty($carrito)) {
    die('Tu carrito está vacío.');
}

// Calcular el total del carrito
$total = 0;
foreach ($carrito as $id_producto => $producto) {
    $producto_info = $con->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $producto_info->execute([$id_producto]);
    $producto_info = $producto_info->fetch(PDO::FETCH_ASSOC);

    $precio_original = $producto_info['precio'];
    $descuento = $producto_info['descuento'];
    $precio_con_descuento = $precio_original - ($precio_original * ($descuento / 100));
    $subtotal = $precio_con_descuento * $producto['cantidad'];
    $total += $subtotal;
}

// Crear la sesión de pago en Stripe Checkout
try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Compra de productos',
                    ],
                    'unit_amount' => $total * 100,
                ],
                'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
        'success_url' => 'https://luzaromatica.infinityfreeapp.com/gracias.php',
        'cancel_url' => 'https://luzaromatica.infinityfreeapp.com/index.php',
    ]);

    // Redirigir al usuario a Stripe Checkout
    http_response_code(303);
    header("Location: " . $checkout_session->url);

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo "Error al crear la sesión de Stripe: " . $e->getMessage();
}
?>