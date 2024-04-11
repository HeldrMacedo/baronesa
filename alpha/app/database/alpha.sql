CREATE TABLE IF NOT EXISTS `regiao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `unit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_regiao_unit_id` (`unit_id`),
  CONSTRAINT `fk_regiao_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `system_unit` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;