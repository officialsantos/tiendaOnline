-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-06-2025 a las 20:29:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecomm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `duration_days` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `duration_days`) VALUES
(7, 9, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cat_slug` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `category`
--

INSERT INTO `category` (`id`, `name`, `cat_slug`) VALUES
(1, 'Paquetes Individuales', 'paquetes-individuales'),
(2, 'Paquetes Familiares', 'paquetes-familiares'),
(3, 'Paquetes Grupales', 'paquetes-grupales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `details`
--

CREATE TABLE `details` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `slug` varchar(200) NOT NULL,
  `price` double NOT NULL,
  `photo` varchar(200) NOT NULL,
  `date_view` date NOT NULL,
  `counter` int(11) NOT NULL,
  `duration_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `slug`, `price`, `photo`, `date_view`, `counter`, `duration_days`) VALUES
(1, 1, 'Aventura en la Patagonia', '<p><strong>Programa individual a Patagonia Austral</strong>, diseñado para pasajeros que viajan de forma autónoma y desean una experiencia personalizada en contacto con la naturaleza.</p>\r\n<ul>\r\n  <li><strong>Duración:</strong> 5 días / 4 noches</li>\r\n  <li><strong>Incluye:</strong> traslados in/out aeropuerto-hotel, alojamiento en establecimiento de categoría turista con desayuno</li>\r\n  <li><strong>Excursiones:</strong> Navegación regular por Lago Argentino con visita al Glaciar Upsala y trekking en El Chaltén (servicio compartido)</li>\r\n  <li><strong>Asistencia al viajero</strong> durante toda la estadía</li>\r\n</ul>', 'aventura-patagonia', 850, 'blank.png', '2025-06-28', 12, 0),
(2, 1, 'Descanso en Termas de Río Hondo', '<p><strong>Programa termal de bienestar y relax</strong>, ideal para el cuidado físico y mental, orientado a pasajeros que buscan descanso activo y servicios terapéuticos.</p>\r\n<ul>\r\n  <li><strong>Duración:</strong> 3 días / 2 noches</li>\r\n  <li><strong>Incluye:</strong> alojamiento en hotel termal con acceso ilimitado a piscinas mineromedicinales</li>\r\n  <li><strong>Régimen de comidas:</strong> media pensión (desayuno buffet + cena menú fijo)</li>\r\n  <li><strong>Servicios complementarios:</strong> uso de gimnasio, circuito hídrico y actividades de relajación</li>\r\n</ul>', 'termas-rio-hondo', 420, 'blank.png', '2025-06-28', 4, 0),
(3, 2, 'Diversión en Bariloche', '<p><strong>Paquete familiar nieve y naturaleza</strong> diseñado para grupos familiares con menores, combinando recreación, naturaleza y confort en un entorno seguro.</p>\r\n<ul>\r\n  <li><strong>Duración:</strong> 4 días / 3 noches</li>\r\n  <li><strong>Incluye:</strong> alojamiento en complejo familiar con habitaciones cuádruples, traslados regulares y asistencia local</li>\r\n  <li><strong>Excursiones:</strong> actividades guiadas en centros de nieve (trineo, caminatas, mini esquí para niños)</li>\r\n  <li><strong>Régimen de comidas:</strong> desayuno buffet incluido</li>\r\n</ul>', 'bariloche-familia', 1200, 'blank.png', '2025-06-28', 9, 0),
(4, 2, 'Aventura en Cataratas', '<p><strong>Programa Iguazú en familia</strong>, enfocado en la interpretación ambiental y el turismo de naturaleza con servicios adaptados a familias.</p>\r\n<ul>\r\n  <li><strong>Duración:</strong> 4 días / 3 noches</li>\r\n  <li><strong>Incluye:</strong> alojamiento en hotel con infraestructura para niños, traslados aeropuerto-hotel-aeropuerto</li>\r\n  <li><strong>Excursiones:</strong> circuito regular a Cataratas lado argentino con guía especializado y Tren de la Selva</li>\r\n  <li><strong>Régimen:</strong> pensión completa (desayuno, almuerzo, cena)</li>\r\n</ul>', 'cataratas-familia', 1400, 'blank.png', '2025-06-28', 24, 0),
(5, 3, 'Escape a Mendoza con Amigos', '<p><strong>Tour grupal enogastronómico a Mendoza</strong>, ideal para grupos de afinidad o turismo corporativo, enfocado en la cultura del vino y el turismo de experiencias.</p>\r\n<ul>\r\n  <li><strong>Duración:</strong> 4 días / 3 noches</li>\r\n  <li><strong>Incluye:</strong> traslados grupales, alojamiento en base doble/múltiple en hostería turística</li>\r\n  <li><strong>Actividades:</strong> visitas a bodegas con cata dirigida, city tour en circuito regular y jornada libre para actividades opcionales</li>\r\n  <li><strong>Coordinador permanente</strong> y cobertura de asistencia al viajero</li>\r\n</ul>', 'mendoza-grupo', 980, 'blank.png', '2025-06-28', 4, 0),
(6, 3, 'Tour Norte Argentino', '<p><strong>Circuito norte andino para grupos reducidos</strong>, con foco en el patrimonio cultural, paisajístico y gastronómico de las provincias de Salta y Jujuy.</p>\r\n<ul>\r\n  <li><strong>Duración:</strong> 5 días / 4 noches</li>\r\n  <li><strong>Incluye:</strong> alojamiento en hosterías con desayuno, traslados internos y excursiones en combi privada</li>\r\n  <li><strong>Itinerario:</strong> Salta capital, Cafayate, Cachi, Purmamarca y Quebrada de Humahuaca</li>\r\n  <li><strong>Servicios:</strong> guía regional, seguro médico y coordinación en destino</li>\r\n</ul>', 'norte-argentino-grupo', 1100, 'blank.png', '2025-06-26', 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int(11) DEFAULT 1,
  `reserved_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `expire_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `product_id`, `quantity`, `price`, `duration_days`, `reserved_at`, `status`, `expire_at`) VALUES
(20, 29, 2, 2, 420.00, 1, '2025-06-28 14:11:35', 'pending', '2025-06-30 14:11:35'),
(21, 29, 1, 3, 850.00, 1, '2025-06-28 14:11:55', 'pending', '2025-06-30 14:11:55'),
(22, 29, 3, 2, 1200.00, 1, '2025-06-28 14:14:07', 'pending', '2025-06-30 14:14:07'),
(23, 29, 1, 2, 850.00, 2, '2025-06-28 14:15:30', 'pending', '2025-06-30 14:15:30'),
(24, 29, 3, 2, 1200.00, 2, '2025-06-28 14:21:41', 'pending', '2025-06-30 14:21:41'),
(25, 29, 4, 7, 1400.00, 3, '2025-06-28 14:23:37', 'pending', '2025-06-30 14:23:37'),
(26, 29, 1, 1, 850.00, 7, '2025-06-28 15:24:22', 'pending', '2025-06-30 15:24:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pay_id` varchar(50) NOT NULL,
  `sales_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(60) NOT NULL,
  `type` int(1) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `status` int(1) NOT NULL,
  `activate_code` varchar(15) NOT NULL,
  `reset_code` varchar(15) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `type`, `firstname`, `lastname`, `address`, `contact_info`, `photo`, `status`, `activate_code`, `reset_code`, `created_on`) VALUES
(1, 'admin@admin.com', '$2y$10$0ZUfLNcZ3RWTa9xi1gSmh.yy.vcdW/kKq/6ei4PwLrH/xRJy0NQ2O', 1, 'Neovic', 'Devierte', '', '', 'facebook-profile-image.jpeg', 1, '', '', '2018-05-01'),
(9, 'ndevierte@gmail.com', '$2y$10$V9QYhuCerNIIprq7WTPkqOTOid83VghciRlCHT.rBxbUHJGtmfHjC', 0, 'Neovic', 'Devierte', 'Silay City, Negros Occidental', '09092735719', 'facebook-profile-image.jpeg', 1, 'k8FBpynQfqsv', 'wzPGkX5IODlTYHg', '2018-05-09'),
(11, 'test@gmail.com', '$2y$10$dvV7onY2bPSb9GBENwR57OixbBy3veerLtRt/FqnpoeyzV1h8x48K', 0, 'test', 'test', 'test', 'test', '', 1, '', '', '2018-05-11'),
(15, 'test2@gmail.com', '$2y$10$38kNj6H42FBtwHXuoA2EnesKLEYUFc4sduX0j0P4sspKteGFSIz/.', 0, 'test2', 'test2', 'test2', 'test2', '', 1, '', '', '2025-06-23'),
(16, 'test3@gmail.com', '$2y$10$/FtZUx/kCSooIbDI9RVTjODqtavlvMQnu8NpWRZxT.YwHsNpSt6dm', 0, 'test3', 'test3', '', '', '', 1, '', '', '2025-06-23'),
(28, 'sanchuap@gmail.com', '$2y$10$TLTskFMZ.05Qak7UYg/gAecubS.BoFNnl51ppPYMr5g.5m5Wvbwhu', 0, 'asd', 'asd', '', '', '', 1, 'UWoOr2RI8tZN', '', '2025-06-24'),
(29, 'benjaminnahuelepul@gmail.com', '$2y$10$rT1sAqTlYVBh9jz5FfJNY.qfP/DrGQWdT8Myvq87NnR98lM09pTJG', 0, 'benjita', 'epul', '', '', '', 1, 'Le4KCrDfEzWx', '', '2025-06-25'),
(32, 'agustin500cm@gmail.com', '$2y$10$Wx73vnohzXFJ8uwlIG6Yi.c4CRwdphxqeZC.4oDePFFzBI9d977bi', 0, 'Jean', 'Lobos', '', '', '', 0, 'ahDmB6TX3Jz4', '', '2025-06-25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `details`
--
ALTER TABLE `details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
