/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.9 - SERVER [mysql]
Source Server Version : 50163
Source Host           : 192.168.1.9:3306
Source Database       : inhalt_modelo

Target Server Type    : MYSQL
Target Server Version : 50163
File Encoding         : 65001

Date: 2017-08-22 16:30:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for perfil
-- ----------------------------
DROP TABLE IF EXISTS `perfil`;
CREATE TABLE `perfil` (
  `id_perfil` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `usuario_cadastro` int(10) unsigned DEFAULT NULL,
  `data_exclusao` datetime DEFAULT NULL,
  `usuario_exclusao` int(10) unsigned DEFAULT NULL,
  `excluido` char(1) DEFAULT '0',
  PRIMARY KEY (`id_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of perfil
-- ----------------------------
INSERT INTO `perfil` VALUES ('1', null, null, null, null, null, '0');
INSERT INTO `perfil` VALUES ('2', 'Teste', '2017-08-22 19:29:44', '1', null, null, '0');

-- ----------------------------
-- Table structure for usuario
-- ----------------------------
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `senha` varchar(100) DEFAULT NULL,
  `cpf` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `usuario_cadastro` int(10) unsigned DEFAULT NULL,
  `data_exclusao` datetime DEFAULT NULL,
  `usuario_exclusao` int(10) unsigned DEFAULT NULL,
  `excluido` char(1) DEFAULT '0',
  `data_ultimo_acesso` datetime DEFAULT NULL,
  `bloqueado` char(1) DEFAULT NULL,
  `data_bloqueado` datetime DEFAULT NULL,
  `usuario_bloqueio` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of usuario
-- ----------------------------
INSERT INTO `usuario` VALUES ('1', 'Administrador', 'admin', '2a057642222a878bc360f52f8e1f0dfd2af93196f123269397423155a4ec4884', '00000000000', 'suporte@inhalt.com.br', '2017-08-17 00:00:00', null, null, null, '0', null, '0', null, null);

-- ----------------------------
-- Table structure for usuario_perfil
-- ----------------------------
DROP TABLE IF EXISTS `usuario_perfil`;
CREATE TABLE `usuario_perfil` (
  `id_perfil` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_perfil`,`id_usuario`),
  KEY `usuario_has_perfil_FKIndex1` (`id_usuario`),
  KEY `usuario_has_perfil_FKIndex2` (`id_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of usuario_perfil
-- ----------------------------
INSERT INTO `usuario_perfil` VALUES ('1', '1');
