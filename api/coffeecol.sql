-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2026 a las 02:10:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.5.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `coffeecol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `usuario`, `password`) VALUES
(1, 'admin', '$2y$10$wH9vYQ8bYfZK6z0zWlYw6eGZK8FhP9gHqk7W8QyZrWZfX1FJZr8eK'),
(3, 'admin2', '$2y$10$E.HPtT3ED18errWAoTJLsOWlhf3YsUqPw/n1OPoxHtiVHif1pBYc6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente',
  `firebase_uid` varchar(128) DEFAULT NULL,
  `numero_pedido` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_items`
--

CREATE TABLE `pedido_items` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `precio_antes` decimal(10,2) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `rating_valor` decimal(2,1) DEFAULT 5.0,
  `rating_cantidad` int(11) DEFAULT 0,
  `unidad` varchar(100) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `badge_tipo` varchar(50) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `precio_antes`, `categoria`, `icono`, `rating_valor`, `rating_cantidad`, `unidad`, `badge`, `badge_tipo`, `imagen`) VALUES
(1, 'Aurora Claro Huila', 'Caf? de tueste claro cultivado en las laderas del Macizo Colombiano. Notas florales de jazm?n, acidez brillante y postgusto limpio con toques de durazno.', 42000.00, NULL, 'tueste-claro', NULL, 4.8, 124, '250 g', 'Nuevo', 'nuevo', 'https://images.unsplash.com/photo-1559496417-e7f25cb247f3?w=300&h=300&fit=crop&q=80'),
(2, 'Cima Nari?o Claro', 'Grano lavado de altura, tueste claro que conserva toda la acidez c?trica de Nari?o. Ideal para V60 o chemex.', 38500.00, NULL, 'tueste-claro', NULL, 4.7, 89, '250 g', NULL, NULL, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=300&h=300&fit=crop&q=80'),
(3, 'Brisa de Popay?n', 'Blend de fincas del Cauca con tueste claro. Notas de t? verde, lima y manzana verde. Muy apreciado por amantes del caf? de especialidad.', 45000.00, 52000.00, 'tueste-claro', NULL, 4.9, 211, '500 g', '-13%', 'oferta', 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=300&h=300&fit=crop&q=80'),
(4, 'Claro del Valle', 'Caf? del norte del Valle procesado en honey. Dulzor natural de ca?a panelera, acidez suave y cuerpo medio. Perfecto para prensa francesa.', 36000.00, NULL, 'tueste-claro', NULL, 4.6, 57, '250 g', NULL, NULL, 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=300&h=300&fit=crop&q=80'),
(5, 'Pico Nevado Claro', 'Microlote de altura (1.900 msnm) de la Sierra Nevada de Santa Marta. Tueste claro con notas herbales y frutales ?nicas.', 58000.00, NULL, 'tueste-claro', NULL, 5.0, 43, '200 g', 'Popular', 'popular', 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=300&h=300&fit=crop&q=80'),
(6, 'Selecci?n Supremo Medio', 'Caf? excelso supremo con tueste medio. Equilibrio perfecto entre acidez y cuerpo. Notas de chocolate amargo, caramelo y nuez.', 32000.00, NULL, 'tueste-medio', NULL, 4.7, 340, '500 g', 'Popular', 'popular', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=300&h=300&fit=crop&q=80'),
(7, 'Monta?a Verde Medio', 'Blend de Antioquia y Huila tostado al punto medio para un perfil redondo y vers?til. Excelente en espresso y americano.', 29500.00, 34000.00, 'tueste-medio', NULL, 4.5, 178, '500 g', '-13%', 'oferta', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300&h=300&fit=crop&q=80'),
(8, 'Dulce de Moka Medio', 'Procesado natural, tueste medio que potencia la dulzura del grano. Notas de panela, ciruela pasa y cacao. Ideal para cappuccino.', 39000.00, NULL, 'tueste-medio', NULL, 4.8, 95, '250 g', 'Especial', 'especial', 'https://images.unsplash.com/photo-1485808191679-5e86023773a8?w=300&h=300&fit=crop&q=80'),
(9, 'Caf? Org?nico Tierra Viva', 'Certificado org?nico de fincas agroecol?gicas del Cauca. Tueste medio, cuerpo cremoso, notas de mora y miel de abejas.', 47000.00, NULL, 'tueste-medio', NULL, 4.9, 132, '250 g', 'Nuevo', 'nuevo', 'https://images.unsplash.com/photo-1464983953574-0892a716854b?w=300&h=300&fit=crop&q=80'),
(10, 'Cumbre Dorada Medio', 'Caf? de Santander con tueste al punto justo. Textura aterciopelada, acidez moderada y postgusto a almendras tostadas.', 31000.00, NULL, 'tueste-medio', NULL, 4.6, 220, '500 g', NULL, NULL, 'https://images.unsplash.com/photo-1542879379-a3220ae30d25?w=300&h=300&fit=crop&q=80'),
(11, 'Espresso Intenso Caf?Col', 'Blend oscuro para espresso. Alta concentraci?n, crema densa y persistente, con notas de cacao amargo, tabaco suave y regaliz.', 34000.00, NULL, 'tueste-oscuro', NULL, 4.7, 415, '500 g', 'Popular', 'popular', 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=300&h=300&fit=crop&q=80'),
(12, 'Noche de Huila Oscuro', 'Tueste oscuro con coraz?n del Huila. Acidez residual sorprendente para ser oscuro. Ideal para moka y espresso italiano.', 30000.00, 36000.00, 'tueste-oscuro', NULL, 4.5, 188, '500 g', '-17%', 'oferta', 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=300&h=300&fit=crop&q=80'),
(13, 'Gran Reserva Col?n', 'Tueste oscuro premium, a?ejado 30 d?as en silo antes de tostar. Cuerpo pleno, notas de madera noble, vainilla y chocolate fondant.', 55000.00, NULL, 'tueste-oscuro', NULL, 4.9, 67, '250 g', 'Especial', 'especial', 'https://images.unsplash.com/photo-1507133750040-4a8f57021571?w=300&h=300&fit=crop&q=80'),
(14, 'Madrugada Negra', 'La mayor intensidad disponible. Tueste oscuro profundo, casi sin acidez. Notas de cacao puro y especias. Perfecto con leche.', 28500.00, NULL, 'tueste-oscuro', NULL, 4.4, 302, '500 g', NULL, NULL, 'https://images.unsplash.com/photo-1534040385115-33dcb3acba5b?w=300&h=300&fit=crop&q=80'),
(15, 'Robusto Caribe', 'Mezcla robusta y ar?bica colombiano, tostado oscuro. Ideal para m?quina de espresso dom?stica. Crema estable y sabor intenso.', 26000.00, NULL, 'tueste-oscuro', NULL, 4.3, 256, '1 kg', NULL, NULL, 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=300&h=300&fit=crop&q=80'),
(16, 'C?psulas Intenso x10', 'Compatible Nespresso?. Intensidad 10/10. Caf? colombiano de tueste oscuro, sabor potente y crema perfecta en cada taza.', 24000.00, NULL, 'capsulas', NULL, 4.6, 531, 'Caja x 10 ud', 'Popular', 'popular', 'https://images.unsplash.com/photo-1523006096513-8914b1b41501?w=300&h=300&fit=crop&q=80'),
(17, 'C?psulas Lungo Suave x10', 'Lungo largo y arom?tico. Intensidad 5/10. Compatible Nespresso?. Notas de frutos secos y cereales tostados.', 22000.00, 26000.00, 'capsulas', NULL, 4.4, 213, 'Caja x 10 ud', '-15%', 'oferta', 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=300&h=300&fit=crop&q=80'),
(18, 'C?psulas Decaf Colombiano x10', 'Todo el sabor colombiano sin cafe?na. Descafeinado suizo con agua. Intensidad 6/10. Compatible Nespresso?.', 25000.00, NULL, 'capsulas', NULL, 4.5, 147, 'Caja x 10 ud', 'Nuevo', 'nuevo', 'https://images.unsplash.com/photo-1498804103079-a6351b050096?w=300&h=300&fit=crop&q=80'),
(19, 'C?psulas Ar?bica Especial x10', 'Microlote de ar?bica de Nari?o en c?psula. Intensidad 7/10. Notas florales y c?tricas bien conservadas. Compatible Nespresso?.', 28000.00, NULL, 'capsulas', NULL, 4.8, 98, 'Caja x 10 ud', 'Especial', 'especial', 'https://images.unsplash.com/photo-1579888944880-d98341245702?w=300&h=300&fit=crop&q=80'),
(20, 'C?psulas Cappuccino Listo x8', 'C?psula doble: caf? y leche en polvo. Solo a?ade agua caliente. Sin vaporizador. Sabor cremoso y equilibrado.', 27000.00, NULL, 'capsulas', NULL, 4.3, 175, 'Caja x 8 ud', NULL, NULL, 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=300&h=300&fit=crop&q=80'),
(21, 'Geisha Huila Washed', 'Varietal Geisha de microlote en El Pital, Huila. Procesado lavado. Bergamota, jazm?n y frambuesa. Puntuado 90+ SCA.', 89000.00, NULL, 'origen-especial', NULL, 5.0, 38, '100 g', 'Especial', 'especial', 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=300&h=300&fit=crop&q=80'),
(22, 'Pink Bourbon Cauca Natural', 'Pink Bourbon procesado natural de finca boutique en Popay?n. Intensamente afrutado: fresa, maracuy? y mel?n. Edici?n limitada.', 76000.00, NULL, 'origen-especial', NULL, 5.0, 22, '100 g', 'Especial', 'especial', 'https://images.unsplash.com/photo-1611854779393-1b2da9d400fe?w=300&h=300&fit=crop&q=80'),
(23, 'Bourbon Rojo Nari?o Honey', 'Varietal Bourbon Rojo procesado honey en finca de 2.050 msnm. La Uni?n, Nari?o. Uva pasa, cuerpo sedoso y acidez malic.', 64000.00, 72000.00, 'origen-especial', NULL, 4.9, 54, '150 g', '-11%', 'oferta', 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=300&h=300&fit=crop&q=80'),
(24, 'Tabi Anaerobic Huila', 'Varietal Tabi en fermentaci?n anaer?bica 72 h, Huila. Complejo y ex?tico: notas de whisky, ciruela y lavanda.', 95000.00, NULL, 'origen-especial', NULL, 4.9, 19, '100 g', 'Nuevo', 'nuevo', 'https://images.unsplash.com/photo-1604881988758-f76ad2f7aac1?w=300&h=300&fit=crop&q=80'),
(25, 'Castillo Caturra Blend Especial', 'Mezcla de Castillo y Caturra de tres fincas del Eje Cafetero. Procesos mixtos: manzana, miel y almendra.', 52000.00, NULL, 'origen-especial', NULL, 4.7, 71, '200 g', 'Popular', 'popular', 'https://images.unsplash.com/photo-1549213783-8284d0336c4f?w=300&h=300&fit=crop&q=80');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_historial`
--

CREATE TABLE `puntos_historial` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `puntos` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puntos_historial`
--

INSERT INTO `puntos_historial` (`id`, `usuario_id`, `descripcion`, `puntos`, `fecha`) VALUES
(1, 1, '🎂 Puntos de cumpleaños', 100, '2026-05-11 03:15:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `firebase_uid` varchar(128) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `puntos` int(11) NOT NULL DEFAULT 0,
  `puntos_cumple_otorgados` year(4) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `firebase_uid`, `nombre`, `email`, `foto`, `fecha_nacimiento`, `puntos`, `puntos_cumple_otorgados`, `fecha_registro`) VALUES
(1, 'lNvjG4GcYZMwuYS6QnToQJXkjAc2', 'Juxn Mxthixs', 'juanmathiasp@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocJ21DZFnnZdok_hIxTzZeK8VWrfVvu4h8giuFdr0QaIOVBVTvpn=s96-c', '2008-05-11', 100, '2026', '2026-05-11 03:13:19'),
(4, 'oYcQhYYfNnSdnvKJJuvjG0loobl1', 'juan mathias palacios', 'juanmathias119@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocKJOoTTc372Ms1bWzN7vkUIxU8X9Su_QrEw-Q_aXf66EStddQ=s96-c', NULL, 0, NULL, '2026-05-11 16:31:50'),
(13, '62QiDkHgzHRQxc8ZTNZOS9Qd1aC2', 'Las Fijas Del Bicho', 'lasfijasdelbicho@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocJGIyxKsvOUCqhFEsa6IoH5lk7RLkM8fzXTQS6vbUkn2sukDA=s96-c', NULL, 0, NULL, '2026-05-12 23:36:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_direcciones`
--

CREATE TABLE `usuario_direcciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL DEFAULT 'Casa',
  `direccion` varchar(255) NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_favoritos`
--

CREATE TABLE `usuario_favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_favoritos`
--

INSERT INTO `usuario_favoritos` (`id`, `usuario_id`, `producto_id`, `fecha`) VALUES
(2, 1, 25, '2026-05-12 22:05:02'),
(3, 1, 24, '2026-05-12 22:05:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `puntos_historial`
--
ALTER TABLE `puntos_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `firebase_uid` (`firebase_uid`);

--
-- Indices de la tabla `usuario_direcciones`
--
ALTER TABLE `usuario_direcciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuario_favoritos`
--
ALTER TABLE `usuario_favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico` (`usuario_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `puntos_historial`
--
ALTER TABLE `puntos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `usuario_direcciones`
--
ALTER TABLE `usuario_direcciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario_favoritos`
--
ALTER TABLE `usuario_favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `puntos_historial`
--
ALTER TABLE `puntos_historial`
  ADD CONSTRAINT `puntos_historial_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_direcciones`
--
ALTER TABLE `usuario_direcciones`
  ADD CONSTRAINT `usuario_direcciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_favoritos`
--
ALTER TABLE `usuario_favoritos`
  ADD CONSTRAINT `usuario_favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_favoritos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
