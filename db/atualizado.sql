CREATE DATABASE  IF NOT EXISTS `cardapio_dinamico` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cardapio_dinamico`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: cardapio_dinamico
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carrinho_compras`
--

DROP TABLE IF EXISTS `carrinho_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrinho_compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` int NOT NULL DEFAULT '1',
  `adicionado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrinho_compras`
--

LOCK TABLES `carrinho_compras` WRITE;
/*!40000 ALTER TABLE `carrinho_compras` DISABLE KEYS */;
INSERT INTO `carrinho_compras` VALUES (1,2,26,2,'2024-09-20 15:50:29'),(2,2,21,1,'2024-09-20 16:10:56'),(3,2,4,3,'2024-09-20 16:24:29');
/*!40000 ALTER TABLE `carrinho_compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enderecos_entrega`
--

DROP TABLE IF EXISTS `enderecos_entrega`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enderecos_entrega` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `pais` varchar(3) NOT NULL DEFAULT 'BRA',
  `cep` varchar(9) NOT NULL,
  `usuario_id` int NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enderecos_entrega`
--

LOCK TABLES `enderecos_entrega` WRITE;
/*!40000 ALTER TABLE `enderecos_entrega` DISABLE KEYS */;
INSERT INTO `enderecos_entrega` VALUES (1,'neymar','1384','q52 l7','Senador Canedo','Goias','GO','BRA','75258831',1,'2024-09-20 13:23:55');
/*!40000 ALTER TABLE `enderecos_entrega` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `categoria` enum('entradas','principais','bebidas','sobremesas') NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (1,'Trem bão','Reservado para (Trem bão).',50.00,'sobremesas','66cf9ad9f22e9.jpg','2024-08-25 17:11:10'),(2,'Nome do produto','Reservado para (Descrição do produto).',2000.00,'principais','66cf99ffbee81.jpg','2024-08-25 19:49:24'),(21,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5f7dde5104.jpg','2024-09-02 17:37:04'),(22,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5f815d8309.jpg','2024-09-02 17:38:29'),(4,'Nome do produto','Reservado para (Descrição do produto).',5000.00,'bebidas','66cf9ac756da5.jpg','2024-08-25 19:57:10'),(25,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5fc4491018.jpg','2024-09-02 17:56:20'),(8,'Nome do produto','Reservado para (Descrição do produto).',5.00,'principais','66cf9b2943687.jpg','2024-08-28 21:48:25'),(24,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5f83a78c3d.jpg','2024-09-02 17:39:06'),(23,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5f8290d19d.jpg','2024-09-02 17:38:49'),(26,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5fc5012d8a.jpg','2024-09-02 17:56:32'),(27,'Nome do Produto','Reservado para (Descrição do produto).',10.00,'entradas','66d5fc638e78d.jpg','2024-09-02 17:56:51');
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transacoes`
--

DROP TABLE IF EXISTS `transacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference_id` varchar(50) NOT NULL,
  `cliente_nome` varchar(255) NOT NULL,
  `cliente_email` varchar(191) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `metodo_pagamento` enum('CREDIT_CARD','BOLETO','PIX') NOT NULL,
  `status` enum('Pendente','Pago','Cancelado') DEFAULT 'Pendente',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `numero_cartao` varchar(20) DEFAULT NULL,
  `exp_mes` varchar(2) DEFAULT NULL,
  `exp_ano` varchar(4) DEFAULT NULL,
  `codigo_seguranca` varchar(4) DEFAULT NULL,
  `titular_cartao` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacoes`
--

LOCK TABLES `transacoes` WRITE;
/*!40000 ALTER TABLE `transacoes` DISABLE KEYS */;
INSERT INTO `transacoes` VALUES (1,'ref-12345','João Silva','joao@example.com',150.50,'CREDIT_CARD','Pendente','2024-09-19 13:27:30','4111111111111111','12','2025','123','João Silva');
/*!40000 ALTER TABLE `transacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(9) DEFAULT NULL,
  `dd` varchar(2) DEFAULT NULL,
  `endereco` text,
  `foto` varchar(255) DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'jose teste','jose@gmail.com','12345678909','$2y$10$sn4AaammqQ.hLnlpXNIqtuRaqCn/xWiX0hELtXyzMKo64aWItTGLy','991098685','62','Rua bv23 Q52 l7','D1.jpeg','2024-09-11 18:57:34'),(2,'Pedro','pedrcandidoenrique@gmail.com',NULL,'$2y$10$93kBEB5u75d8Fd8NHohxKuOXol0.p.4r9fSqZth1kScXijOFeJIxO','629957761',NULL,'','Captura de tela 2024-09-20 122933.png','2024-09-20 15:30:49');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_adm`
--

DROP TABLE IF EXISTS `usuarios_adm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_adm` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_adm`
--

LOCK TABLES `usuarios_adm` WRITE;
/*!40000 ALTER TABLE `usuarios_adm` DISABLE KEYS */;
INSERT INTO `usuarios_adm` VALUES (1,'Administrador','admin@example.com','e7d80ffeefa212b7c5c55700e4f7193e','2024-09-11 22:21:43');
/*!40000 ALTER TABLE `usuarios_adm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendas`
--

DROP TABLE IF EXISTS `vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int DEFAULT NULL,
  `data_venda` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `status` enum('Pendente','Pago','Cancelado') DEFAULT 'Pendente',
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendas`
--

LOCK TABLES `vendas` WRITE;
/*!40000 ALTER TABLE `vendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `vendas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-20 13:36:10
