-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 11-02-2025 a las 15:02:27
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `final_eventos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id_eventos` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_evento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_eventos`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_eventos`, `nombre_evento`) VALUES
(1, 'Boda'),
(2, 'Quince Años'),
(3, 'Banquetes'),
(4, 'Bautizo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id_pago` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuarios` int UNSIGNED NOT NULL,
  `id_paquete` int UNSIGNED NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `tipo_pago` enum('contado','plazos') NOT NULL,
  `fecha_pago` date NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_usuarios` (`id_usuarios`),
  KEY `id_paquete` (`id_paquete`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_usuarios`, `id_paquete`, `monto_total`, `tipo_pago`, `fecha_pago`) VALUES
(1, 2, 1, 360000.00, 'contado', '2024-12-11'),
(2, 2, 2, 28000.00, 'plazos', '2030-04-01'),
(3, 4, 1, 2500.00, 'plazos', '2024-12-27'),
(4, 2, 1, 2500.00, 'plazos', '2024-12-07'),
(5, 2, 1, 2500.00, 'plazos', '2024-12-25'),
(6, 2, 1, 2500.00, 'plazos', '2024-12-25'),
(7, 2, 2, 0.00, 'contado', '2025-01-03'),
(8, 2, 2, 0.00, 'plazos', '2025-01-04'),
(9, 2, 1, 2500.00, 'contado', '2024-12-28'),
(10, 2, 1, 2500.00, 'plazos', '2025-01-04'),
(11, 2, 1, 2500.00, 'plazos', '2024-12-28'),
(12, 2, 1, 2500.00, 'plazos', '2024-12-20'),
(13, 2, 1, 2500.00, 'plazos', '2024-12-07'),
(14, 2, 1, 2500.00, 'plazos', '2024-12-28'),
(15, 2, 1, 2500.00, 'plazos', '2024-12-28'),
(16, 2, 1, 2500.00, 'plazos', '2024-12-23'),
(17, 2, 1, 2500.00, 'plazos', '2024-12-23'),
(18, 2, 1, 2500.00, 'plazos', '2025-01-03'),
(19, 2, 1, 2500.00, 'plazos', '2025-01-02'),
(20, 2, 1, 2500.00, 'plazos', '2025-01-06'),
(21, 2, 1, 2500.00, 'plazos', '2024-12-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_plazos`
--

DROP TABLE IF EXISTS `pagos_plazos`;
CREATE TABLE IF NOT EXISTS `pagos_plazos` (
  `id_pago_plazo` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pago` int UNSIGNED NOT NULL,
  `numero_plazo` int NOT NULL,
  `monto_plazo` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `estado_pago` enum('pendiente','pagado') DEFAULT 'pendiente',
  PRIMARY KEY (`id_pago_plazo`),
  KEY `id_pago` (`id_pago`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pagos_plazos`
--

INSERT INTO `pagos_plazos` (`id_pago_plazo`, `id_pago`, `numero_plazo`, `monto_plazo`, `fecha_pago`, `estado_pago`) VALUES
(1, 2, 5, 5000.00, '2025-01-04', 'pendiente'),
(2, 3, 3, 500.36, '2025-01-01', 'pendiente'),
(3, 4, 15, 5000.00, '2025-01-04', 'pendiente'),
(4, 5, 4, 500.00, '2024-12-31', 'pendiente'),
(5, 6, 4, 500.00, '2024-12-31', 'pendiente'),
(6, 8, 80, 500.00, '2024-12-31', 'pendiente'),
(7, 10, 5, 500.00, '2024-12-28', 'pendiente'),
(8, 11, 25, 100.00, '2025-01-04', 'pendiente'),
(9, 12, 2500, 1.00, '2025-01-04', 'pendiente'),
(10, 13, 85, 29.41, '2025-01-04', 'pendiente'),
(11, 14, 2, 1250.00, '2024-11-30', 'pendiente'),
(12, 15, 15, 166.67, '2025-01-17', 'pendiente'),
(13, 16, 25, 100.00, '2024-11-30', 'pendiente'),
(14, 17, 25, 100.00, '2024-11-30', 'pendiente'),
(15, 18, 9999999, 0.00, '2024-12-16', 'pendiente'),
(16, 19, 15, 166.67, '2024-12-28', 'pendiente'),
(17, 20, 10, 250.00, '2025-01-20', 'pendiente'),
(18, 21, 12, 208.33, '2024-12-27', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes`
--

DROP TABLE IF EXISTS `paquetes`;
CREATE TABLE IF NOT EXISTS `paquetes` (
  `id_paquete` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_eventos` int UNSIGNED NOT NULL,
  `id_usuarios` int UNSIGNED DEFAULT NULL,
  `nombre_paquete` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_imagen1` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_imagen2` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_imagen3` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_paquete`),
  KEY `id_eventos` (`id_eventos`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes`
--

INSERT INTO `paquetes` (`id_paquete`, `id_eventos`, `id_usuarios`, `nombre_paquete`, `ruta_imagen`, `descripcion`, `ruta_imagen1`, `ruta_imagen2`, `ruta_imagen3`) VALUES
(1, 1, NULL, 'Paquete Boda Completo', '../../img/boda1.jpg', 'Celebra el amor en un día inolvidable con nuestro exclusivo Paquete de Boda Soñada. Diseñado para parejas que desean una experiencia mágica y sin preocupaciones, este paquete incluye todo lo necesario para hacer de tu boda un evento memorable.', '../../img/boda2.jpg', '../../img/boda3.jpg', '../../img/boda`1.jpg'),
(2, 2, NULL, 'Paquete Quinceañera Tradicional', '../../img/quince1.jpg', 'Los XV años son una tradición auténtica de nuestro país y un acontecimento importante en la vida social de cualquier jovencita. Permite que los mejores organizadores de fiestas 15 años te ayuden en la selección de todos los detalles para esta fecha tan especial y haz que la celebración sea distinta y la lleve para siempre en su memoria. Este listado muestra 46 empresas de Fiestas 15 años en Cancún', '../../img/quince2.jpg', '../../img/quince3.jpg', '../../img/quince4.jpg'),
(3, 3, NULL, 'Banquetes', '../../img/banquete1.jpg', 'Nuestro servicio de banquete está diseñado para deleitar a tus invitados con una experiencia culinaria única e inolvidable. Ofrecemos una amplia variedad de platillos preparados con ingredientes frescos y de la más alta calidad, adaptándonos a tus preferencias y necesidades. Desde entradas elegantes hasta postres irresistibles, cada detalle está cuidadosamente planeado para complementar la temática de tu evento.', '../../img/banquete2.jpg', '../../img/banquete3.jpg', '../../img/banquete4.jpg'),
(4, 4, NULL, 'Bautizos', 'carruselPastor.jpeg', 'Bautizos especiales', 'carrusel-chocolate.jpeg', 'carruselCake.jpeg', 'pastorCard.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_servicio`
--

DROP TABLE IF EXISTS `paquete_servicio`;
CREATE TABLE IF NOT EXISTS `paquete_servicio` (
  `id_paquete` int UNSIGNED NOT NULL,
  `id_servicio` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_paquete`,`id_servicio`),
  KEY `id_servicio` (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_servicio`
--

INSERT INTO `paquete_servicio` (`id_paquete`, `id_servicio`) VALUES
(1, 1),
(4, 1),
(1, 2),
(4, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

DROP TABLE IF EXISTS `servicios`;
CREATE TABLE IF NOT EXISTS `servicios` (
  `id_servicio` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_servicio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `precio_servicio` double NOT NULL,
  PRIMARY KEY (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `descripcion`, `nombre_servicio`, `precio_servicio`) VALUES
(1, 'Servicio de banquete buffet para eventos', 'Banquete Buffet', 1000),
(2, 'Servicio de música para eventos', 'Música en vivo', 1500),
(3, 'Decoracion sencillo, globos, cintas, arcos de globo.', 'Decoracion', 750);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjetas`
--

DROP TABLE IF EXISTS `tarjetas`;
CREATE TABLE IF NOT EXISTS `tarjetas` (
  `id_tarjeta` int NOT NULL AUTO_INCREMENT,
  `id_usuarios` int UNSIGNED NOT NULL,
  `nombre_titular` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `numero_tarjeta` varchar(16) NOT NULL,
  `fecha_vencimiento` VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;,
  `cvv` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_tarjeta`),
  KEY `id_usuarios` (`id_usuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarjetas`
--

INSERT INTO `tarjetas` (`id_tarjeta`, `id_usuarios`, `nombre_titular`, `numero_tarjeta`, `fecha_vencimiento`, `cvv`) VALUES
(1, 2, 'Erik garcia Rafael', 1234567890, '2024-12-11', '123'),
(2, 2, 'Erik garcia Rafael', 1234567890, '2024-12-11', '123'),
(3, 2, 'Erik garcia Rafael', 1234567890, '2024-12-11', '123'),
(4, 2, 'Erik garcia Rafael', 1234567890, '2024-12-11', '124'),
(5, 2, 'josefina Isabel', 405464, '2025-08-24', '546'),
(6, 2, 'josefina Isabel', 405464, '2025-08-24', '546'),
(7, 2, 'josefina Isabel', 124, '2024-11-01', '162'),
(8, 2, 'josefina Isabel', 124, '2024-11-01', '162'),
(9, 2, 'josefina Isabel', 1242, '2024-10-08', '298'),
(10, 2, 'pedrpo alcantara', 618, '2025-06-30', '987'),
(11, 2, 'Dylan Deceano Rafael', 899718, '2024-11-19', '698'),
(12, 2, 'pacoWEBOS', 963294, '2030-01-14', '846'),
(13, 2, 'erik santiagfo Gar', 6187987, '2024-10-08', '986'),
(14, 2, 'pedro', 18787, '2023-11-11', '879'),
(15, 2, 'pedro', 157171, '2024-11-01', '498'),
(16, 2, 'patoPErro', 1687678, '2024-11-08', '698'),
(17, 2, 'bjdbfhsbdhfb', 18787, '2024-03-22', '999'),
(18, 2, 'pepe', 41872, '2024-11-12', '124'),
(19, 2, 'ufuifysgfyu', 5445454, '2024-11-23', '678'),
(20, 2, 'pedro', 123456, '2024-11-01', '123'),
(21, 2, 'pedro', 123456, '2024-11-01', '123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_user`
--

DROP TABLE IF EXISTS `tipo_user`;
CREATE TABLE IF NOT EXISTS `tipo_user` (
  `id_tipo_user` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_tipo_user`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_user`
--

INSERT INTO `tipo_user` (`id_tipo_user`, `tipo_user`) VALUES
(1, 'Cliente'),
(2, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuarios` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `numero_telefono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_tipo_user` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_usuarios`),
  KEY `id_tipo_user` (`id_tipo_user`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuarios`, `nombre`, `apellido`, `correo`, `numero_telefono`, `password`, `id_tipo_user`) VALUES
(1, 'Juan', 'Pérez', 'juanperez@email.com', '1234567890', 'password123', 1),
(2, 'Erik', 'Garcia', 'santigard07793@gmail.com', '9981724619', '2013460', 2);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD CONSTRAINT `paquetes_ibfk_1` FOREIGN KEY (`id_eventos`) REFERENCES `eventos` (`id_eventos`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `paquete_servicio`
--
ALTER TABLE `paquete_servicio`
  ADD CONSTRAINT `paquete_servicio_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id_paquete`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `paquete_servicio_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarjetas`
--
ALTER TABLE `tarjetas`  
  ADD CONSTRAINT `tarjetas_ibfk_1` FOREIGN KEY (`id_usuarios`) REFERENCES `usuarios` (`id_usuarios`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_tipo_user`) REFERENCES `tipo_user` (`id_tipo_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
