<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;

$html = "
<h1>Factura CoffeeCol</h1>
<p>Gracias por tu compra</p>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();

$dompdf->stream("factura.pdf");