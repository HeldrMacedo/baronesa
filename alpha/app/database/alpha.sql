CREATE TABLE IF NOT EXISTS `regiao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `unit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_regiao_unit_id` (`unit_id`),
  CONSTRAINT `fk_regiao_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `system_unit` (`id`)
);

CREATE TABLE IF NOT EXISTS `gerentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `regiao_id` int NOT NULL,
  `user_id` int NOT NULL,
  `unit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_userIDRegiao` (`user_id`),
  KEY `FK_RegiaoIdUser` (`regiao_id`),
  KEY `FK_unitGerente` (`unit_id`),
  CONSTRAINT `FK_RegiaoIdUser` FOREIGN KEY (`regiao_id`) REFERENCES `regiao` (`id`),
  CONSTRAINT `FK_unitGerente` FOREIGN KEY (`unit_id`) REFERENCES `system_unit` (`id`),
  CONSTRAINT `FK_userIDRegiao` FOREIGN KEY (`user_id`) REFERENCES `system_user` (`id`)
);

CREATE TABLE IF NOT EXISTS `cambistas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regiao_id` int NOT NULL,
  `gerente_id` int NOT NULL,
  `nome` varchar(200) NOT NULL,
  `comissao` int NOT NULL DEFAULT '0',
  `pode_cancelar` char(1) NOT NULL,
  `pode_cancelar_tempo` time DEFAULT NULL,
  `limite_venda` decimal(15,2) DEFAULT NULL,
  `exibe_comissao` char(1) NOT NULL,
  `usuario_id` int NOT NULL,
  `pode_reimprimir` char(1) NOT NULL,
  `unit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_comissao_regiao_id` (`regiao_id`),
  KEY `FK_comissao_gerente_id` (`gerente_id`),
  KEY `FK_comissao_usuario_if` (`usuario_id`),
  KEY `FK_comissao_unit_id` (`unit_id`),
  CONSTRAINT `FK_comissao_gerente_id` FOREIGN KEY (`gerente_id`) REFERENCES `gerentes` (`id`),
  CONSTRAINT `FK_comissao_regiao_id` FOREIGN KEY (`regiao_id`) REFERENCES `regiao` (`id`),
  CONSTRAINT `FK_comissao_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `system_unit` (`id`),
  CONSTRAINT `FK_comissao_usuario_if` FOREIGN KEY (`usuario_id`) REFERENCES `system_users` (`id`)
); 

CREATE TABLE IF NOT EXISTS `extracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  `abreviacao` varchar(10) DEFAULT NULL,
  `hora_limite` time NOT NULL,
  `segunda` char(1) DEFAULT 'N',
  `terca` char(1) DEFAULT 'N',
  `quarta` char(1) DEFAULT 'N',
  `quinta` char(1) DEFAULT 'N',
  `sexta` char(1) DEFAULT 'N',
  `sabado` char(1) DEFAULT 'N',
  `domingo` char(1) DEFAULT 'N',
  `premiacao_maxima` int NOT NULL,
  `ultimo_sorteio_numero` int ,
  `ativo` char(1) DEFAULT 'S',
  `data_primeiro_sorteio` date DEFAULT NULL,
  `limite_palpite` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;
