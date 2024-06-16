-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 03-05-2024 a las 15:45:40
-- Versión del servidor: 10.4.21-MariaDB-1:10.4.21+maria~focal
-- Versión de PHP: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `impuestos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `id_historial` int(11) NOT NULL,
  `nombre_imp_hist` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `costo_imp_hist` decimal(10,0) DEFAULT NULL,
  `fecha_pago_hist` date DEFAULT NULL,
  `ruta_factura_hist` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_usu2` int(11) NOT NULL,
  `id_imp2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imp`
--

CREATE TABLE `imp` (
  `id_imp` int(11) NOT NULL,
  `nombre_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `proveedor_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_vencimiento_imp` date DEFAULT NULL,
  `costo_imp` decimal(10,2) DEFAULT NULL,
  `ruta_comprobante_imp` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `imp`
--

INSERT INTO `imp` (`id_imp`, `nombre_imp`, `proveedor_imp`, `fecha_vencimiento_imp`, `costo_imp`, `ruta_comprobante_imp`, `tipo_imp`, `estado_imp`) VALUES
(38, 'Agua Potable', 'Aguas RN', '2024-04-10', '13228.87', '../facturas/202404082134551280001pdf.pdf', 'AGUA', 'PAGADO'),
(39, 'Luz - Electricidad', 'Cooperativa CEB', '2024-04-26', '71587.23', '../facturas/17684mar2024pdf.pdf', 'LUZ', NULL),
(40, 'GAS', 'Camuzzi', '2024-04-10', '4998.09', '../facturas/facturaperiodo0224pdf.pdf', 'GAS', NULL),
(41, 'Internet', 'AVC ', '2024-04-10', '16900.00', '../facturas/facturaavc-8pdf.pdf', 'INTERNET', NULL),
(42, 'Impuesto Inmobiliario', 'Agencia R. Tributaria', '2024-04-15', '24537.64', '../facturas/informedeudainmobiliariopdf.pdf', 'IMPUESTO INMOBILIARIO', NULL),
(43, 'Tasas Municipales', 'Municipio Bariloche', '1111-11-11', '0.00', '../facturas/2024-04-10-153644-pdf.pdf', 'TASAS MUNICIPALES', NULL),
(44, 'Tasas Municipales', 'Municipio Bariloche', '2024-05-10', '14512.45', '../facturas/boleta-638502469949323383pdf.pdf', 'TASAS MUNICIPALES', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `pago` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `pago`) VALUES
(3, 'Adri', NULL),
(4, 'Primo', NULL),
(5, 'Ana', NULL),
(6, 'Carlos', NULL),
(7, 'Cris y Dani', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usu_imp`
--

CREATE TABLE `usu_imp` (
  `id_usu_imp` int(11) NOT NULL,
  `id_usu1` int(11) NOT NULL,
  `id_imp1` int(80) NOT NULL,
  `monto` decimal(10,0) NOT NULL,
  `estado_pago` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usu_imp`
--

INSERT INTO `usu_imp` (`id_usu_imp`, `id_usu1`, `id_imp1`, `monto`, `estado_pago`, `fecha_pago`) VALUES
(12, 3, 38, '2646', NULL, NULL),
(13, 4, 38, '2646', NULL, NULL),
(14, 5, 38, '2646', NULL, NULL),
(15, 6, 38, '2646', NULL, NULL),
(16, 7, 38, '2646', NULL, NULL),
(17, 3, 39, '14317', NULL, NULL),
(18, 4, 39, '14317', NULL, NULL),
(19, 5, 39, '14317', NULL, NULL),
(20, 6, 39, '14317', NULL, NULL),
(21, 7, 39, '14317', NULL, NULL),
(22, 4, 40, '1250', NULL, NULL),
(23, 5, 40, '1250', NULL, NULL),
(24, 6, 40, '1250', NULL, NULL),
(25, 7, 40, '1250', NULL, NULL),
(26, 3, 41, '4225', NULL, NULL),
(27, 5, 41, '4225', NULL, NULL),
(28, 6, 41, '4225', NULL, NULL),
(29, 7, 41, '4225', NULL, NULL),
(30, 3, 42, '4908', NULL, NULL),
(31, 4, 42, '4908', NULL, NULL),
(32, 5, 42, '4908', NULL, NULL),
(33, 6, 42, '4908', NULL, NULL),
(34, 7, 42, '4908', NULL, NULL),
(35, 3, 43, '0', NULL, NULL),
(36, 4, 43, '0', NULL, NULL),
(37, 5, 43, '0', NULL, NULL),
(38, 6, 43, '0', NULL, NULL),
(39, 7, 43, '0', NULL, NULL),
(40, 3, 44, '2902', NULL, NULL),
(41, 4, 44, '2902', NULL, NULL),
(42, 5, 44, '2902', NULL, NULL),
(43, 6, 44, '2902', NULL, NULL),
(44, 7, 44, '2902', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_usu2` (`id_usu2`,`id_imp2`),
  ADD KEY `id_imp2` (`id_imp2`);

--
-- Indices de la tabla `imp`
--
ALTER TABLE `imp`
  ADD PRIMARY KEY (`id_imp`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `usu_imp`
--
ALTER TABLE `usu_imp`
  ADD PRIMARY KEY (`id_usu_imp`),
  ADD KEY `id_usu1` (`id_usu1`),
  ADD KEY `id_imp1` (`id_imp1`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imp`
--
ALTER TABLE `imp`
  MODIFY `id_imp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usu_imp`
--
ALTER TABLE `usu_imp`
  MODIFY `id_usu_imp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial`
--
ALTER TABLE `historial`
  ADD CONSTRAINT `historial_ibfk_1` FOREIGN KEY (`id_usu2`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usu_imp`
--
ALTER TABLE `usu_imp`
  ADD CONSTRAINT `usu_imp_ibfk_1` FOREIGN KEY (`id_usu1`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usu_imp_ibfk_2` FOREIGN KEY (`id_imp1`) REFERENCES `imp` (`id_imp`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
