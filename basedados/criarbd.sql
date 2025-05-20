-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/05/2025 às 02:55
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `felixbus`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alerta`
--

CREATE TABLE `alerta` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bilhete`
--

CREATE TABLE `bilhete` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `viagem_id` int(11) NOT NULL,
  `codigo_validacao` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carteira`
--

CREATE TABLE `carteira` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carteira`
--

INSERT INTO `carteira` (`id`, `utilizador_id`, `saldo`) VALUES
(1, 1, 0.00),
(2, 2, 0.00),
(4, NULL, 0.00),
(5, 4, 0.00),
(6, 5, 0.00),
(7, 6, 0.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentocarteira`
--

CREATE TABLE `movimentocarteira` (
  `id` int(11) NOT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `valor` decimal(10,2) NOT NULL,
  `tipo` enum('adicionar','retirar','transferencia') NOT NULL,
  `carteira_origem` int(11) DEFAULT NULL,
  `carteira_destino` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil`
--

CREATE TABLE `perfil` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `perfil`
--

INSERT INTO `perfil` (`id`, `nome`) VALUES
(1, 'cliente'),
(2, 'funcionario'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rota`
--

CREATE TABLE `rota` (
  `id` int(11) NOT NULL,
  `origem` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `capacidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `utilizador`
--

CREATE TABLE `utilizador` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `telefone` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `utilizador`
--

INSERT INTO `utilizador` (`id`, `nome`, `email`, `password`, `perfil_id`, `ativo`, `telefone`) VALUES
(1, 'cliente', 'cliente@felixbus.com', '4983a0ab83ed86e0e7213c8783940193', 1, 1, 0),
(2, 'funcionario', 'funcionario@felixbus.com', 'cc7a84634199040d54376793842fe035', 2, 1, 0),
(4, 'Silva', 'ewerton@mail.com', '$2y$10$lZ.aV8LGpMDtkVydHK410.GYoq1/iV9.fpXx6ttUVgaGfgmV3ErZe', 3, 1, 123),
(5, 'Joao', 'joao@mail.com', '$2y$10$a4bR.gECHjF3ny4w3tW5O.26UJBWJSISENuh/oWvPBYVS/VUDvNzy', 1, 1, 0),
(6, 'admin', 'admin@felixbus.com', '$2y$10$Wp2Qz2lIk.6jIOSgo/Bkl.DYCmkN5Gw6Kdcr7NehQzBLyLcds6F1O', 3, 1, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `viagem`
--

CREATE TABLE `viagem` (
  `id` int(11) NOT NULL,
  `rota_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alerta`
--
ALTER TABLE `alerta`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `bilhete`
--
ALTER TABLE `bilhete`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_validacao` (`codigo_validacao`),
  ADD KEY `viagem_id` (`viagem_id`),
  ADD KEY `Bilhete_ibfk_1` (`cliente_id`);

--
-- Índices de tabela `carteira`
--
ALTER TABLE `carteira`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Carteira_ibfk_1` (`utilizador_id`);

--
-- Índices de tabela `movimentocarteira`
--
ALTER TABLE `movimentocarteira`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MovimentoCarteira_ibfk_1` (`carteira_origem`),
  ADD KEY `MovimentoCarteira_ibfk_2` (`carteira_destino`);

--
-- Índices de tabela `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `rota`
--
ALTER TABLE `rota`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `utilizador`
--
ALTER TABLE `utilizador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `perfil_id` (`perfil_id`);

--
-- Índices de tabela `viagem`
--
ALTER TABLE `viagem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rota_id` (`rota_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alerta`
--
ALTER TABLE `alerta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `bilhete`
--
ALTER TABLE `bilhete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carteira`
--
ALTER TABLE `carteira`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `movimentocarteira`
--
ALTER TABLE `movimentocarteira`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `perfil`
--
ALTER TABLE `perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `rota`
--
ALTER TABLE `rota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `utilizador`
--
ALTER TABLE `utilizador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `viagem`
--
ALTER TABLE `viagem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `bilhete`
--
ALTER TABLE `bilhete`
  ADD CONSTRAINT `Bilhete_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `utilizador` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bilhete_ibfk_2` FOREIGN KEY (`viagem_id`) REFERENCES `viagem` (`id`);

--
-- Restrições para tabelas `carteira`
--
ALTER TABLE `carteira`
  ADD CONSTRAINT `Carteira_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizador` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `movimentocarteira`
--
ALTER TABLE `movimentocarteira`
  ADD CONSTRAINT `MovimentoCarteira_ibfk_1` FOREIGN KEY (`carteira_origem`) REFERENCES `carteira` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `MovimentoCarteira_ibfk_2` FOREIGN KEY (`carteira_destino`) REFERENCES `carteira` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `utilizador`
--
ALTER TABLE `utilizador`
  ADD CONSTRAINT `utilizador_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`);

--
-- Restrições para tabelas `viagem`
--
ALTER TABLE `viagem`
  ADD CONSTRAINT `viagem_ibfk_1` FOREIGN KEY (`rota_id`) REFERENCES `rota` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
