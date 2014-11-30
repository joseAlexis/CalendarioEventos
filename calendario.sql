-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 05-06-2014 a las 23:28:09
-- Versión del servidor: 5.5.37
-- Versión de PHP: 5.4.4-14+deb7u9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `calendario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE IF NOT EXISTS `evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `Detalle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Volcado de datos para la tabla `evento`
--

INSERT INTO `evento` (`id`, `fecha`, `horaInicio`, `horaFin`, `Detalle`) VALUES
(12, '2014-06-12', '01:30:00', '23:30:00', ' Prueba v2 '),
(13, '2014-09-22', '01:00:00', '23:59:00', 'Cumple Jose!...    '),
(17, '2014-05-31', '18:30:00', '23:50:00', 'Fiesta Abuela  '),
(19, '2014-06-27', '00:00:00', '00:00:00', '    Hola    ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_persona`
--

CREATE TABLE IF NOT EXISTS `evento_persona` (
  `persona` int(11) NOT NULL,
  `evento` int(11) NOT NULL,
  PRIMARY KEY (`persona`,`evento`),
  KEY `evento` (`evento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `evento_persona`
--

INSERT INTO `evento_persona` (`persona`, `evento`) VALUES
(201230456, 12),
(207170384, 13),
(201230456, 17),
(207170384, 17),
(207170384, 19);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE IF NOT EXISTS `persona` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id`, `nombre`) VALUES
(201230456, 'Paola Lopez'),
(207170384, 'Jose Bolanos ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL,
  `clave` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `clave`) VALUES
(201230456, '72a86026abb289634ec64d7f3b544f0c'),
(207170384, '662eaa47199461d01a623884080934ab');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `evento_persona`
--
ALTER TABLE `evento_persona`
  ADD CONSTRAINT `evento_persona_ibfk_1` FOREIGN KEY (`evento`) REFERENCES `evento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evento_persona_ibfk_2` FOREIGN KEY (`persona`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
