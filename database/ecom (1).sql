-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 07 fév. 2025 à 10:41
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecom`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `password`, `nom`, `prenom`) VALUES
(1, 'mohamed94', 'moha', 'ben');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `id_modele` int(11) NOT NULL,
  `product_title` varchar(10) NOT NULL,
  `nom` varchar(10) NOT NULL,
  `commentaire` varchar(2048) NOT NULL,
  `note` int(11) NOT NULL,
  `date_ajout` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `prix_total` float NOT NULL,
  `date_commande` date NOT NULL DEFAULT current_timestamp(),
  `status` int(2) NOT NULL,
  `nom_modele` varchar(20) NOT NULL,
  `image_modele` varchar(80) NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `prix_total`, `date_commande`, `status`, `nom_modele`, `image_modele`, `id_user`) VALUES
(1, 0, '0000-00-00', 0, '', '', 1),
(2, 0, '0000-00-00', 0, '', '', 1),
(3, 0, '0000-00-00', 0, '', '', 1),
(4, 0, '0000-00-00', 0, '', '', 1),
(5, 0, '0000-00-00', 0, '', '', 1),
(6, 0, '0000-00-00', 0, '', '', 1),
(7, 0, '2025-01-31', 0, '', '', 1),
(8, 0, '2025-01-31', 0, '', '', 1),
(9, 0, '2025-01-31', 0, '', '', 1),
(10, 0, '0000-00-00', 0, '', '', 1),
(11, 0, '0000-00-00', 0, '', '', 1),
(12, 0, '0000-00-00', 0, '', '', 1),
(13, 0, '0000-00-00', 0, '', '', 1),
(14, 0, '0000-00-00', 0, '', '', 1),
(15, 0, '0000-00-00', 0, '', '', 1),
(16, 0, '0000-00-00', 0, '', '', 1),
(17, 0, '0000-00-00', 0, '', '', 1),
(18, 0, '0000-00-00', 0, '', '', 1),
(19, 0, '0000-00-00', 1, '', '', 1),
(20, 0, '0000-00-00', 0, '', '', 1),
(21, 0, '0000-00-00', 0, '', '', 1),
(22, 0, '0000-00-00', 0, '', '', 1),
(23, 0, '0000-00-00', 0, '', '', 1),
(24, 0, '0000-00-00', 0, '', '', 1),
(25, 0, '0000-00-00', 0, '', '', 1),
(26, 0, '0000-00-00', 0, '', '', 1),
(27, 0, '0000-00-00', 0, '', '', 1),
(28, 0, '2025-01-31', 0, '', '', 1),
(29, 0, '2025-01-31', 0, '', '', 1),
(30, 0, '2025-01-31', 0, '', '', 1),
(31, 0, '2025-01-31', 0, '', '', 2),
(32, 0, '2025-01-31', 0, '', '', 1),
(33, 0, '2025-01-31', 0, '', '', 1),
(34, 0, '2025-01-31', 0, '', '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `detail_commande`
--

CREATE TABLE `detail_commande` (
  `id_detail` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_modele` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` float NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historique_commandes`
--

CREATE TABLE `historique_commandes` (
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `produits` text NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `date_achat` datetime NOT NULL,
  `date_livraison` datetime NOT NULL,
  `adresse_livraison` varchar(255) NOT NULL,
  `code_postal` varchar(20) NOT NULL,
  `ville` varchar(80) NOT NULL,
  `complement_adresse` varchar(150) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `historique_commandes`
--

INSERT INTO `historique_commandes` (`id_commande`, `id_user`, `produits`, `prix_total`, `date_achat`, `date_livraison`, `adresse_livraison`, `code_postal`, `ville`, `complement_adresse`, `image_path`) VALUES
(2, 1, '[{\"nom\":\" A\",\"prix\":49.99,\"quantite\":2,\"image\":\"produit_a.jpg\"},{\"nom\":\"Produit B\",\"prix\":20.00,\"quantite\":1,\"image\":\"produit_b.jpg\"}]', 40.00, '2023-10-01 00:00:00', '2023-10-08 00:00:00', '123 Rue Exemple', '75001', 'Paris', 'Appartement 4B', NULL),
(3, 1, '{\"G502 HERO\":{\"prix\":\"49.99\",\"quantite\":\"1\"}}', 49.99, '0000-00-00 00:00:00', '2025-02-07 10:59:29', '', '', '', NULL, NULL),
(4, 1, '{\"Core i7-14700KF\":{\"prix\":\"499.99\",\"quantite\":\"1\"}}', 499.99, '0000-00-00 00:00:00', '2025-02-07 16:00:03', '', '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `modele`
--

CREATE TABLE `modele` (
  `id_modele` int(11) NOT NULL,
  `Produits` varchar(80) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Fabricant` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `Prix` float NOT NULL,
  `Image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modele`
--

INSERT INTO `modele` (`id_modele`, `Produits`, `Nom`, `Fabricant`, `Description`, `Prix`, `Image`) VALUES
(1, 'Péripheriques', 'G502 HERO', 'Logitech', 'Souris Gamer optique - Résolution ajustable 100 à 16 000 dpi - 11 boutons programmables', 49.99, 'p1.jfif'),
(2, 'Ordinateur', 'Swift X 14', 'Acer', '14.5\" QHD+ OLED - Intel Core i7-13700H - GeForce RTX 4050 - 32 Go DDR5 - SSD 1 To - Windows 11 Pro', 1299.99, 'o1.jfif'),
(3, 'Composant', 'Core i7-14700KF', 'Intel', 'Processeur Socket 1700 - 20 coeurs - Cache 33 Mo - Raptor Lake refresh - Ventirad non inclus', 499.99, 'c1.jfif'),
(4, 'Péripheriques', 'BlackShark V2 X USB', 'Razer', 'Casque-micro gamer - Son surround 7.1 - USB-A - Micro avec annulation passive du bruit avancée', 74.99, 'p2.jfif');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id_panier` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `id_modele` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `reset_token` datetime DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom`, `prenom`, `email`, `mdp`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'user1', 'user1', 'user1@gmail.com', '$2y$10$/ek3/fUOAkhTqlNdo18PmOjbMcGVQAsxkcHClcpG81z4ixkfpaMfy', NULL, NULL),
(2, 'user2', 'user2', 'user2@gmail.com', '$2y$10$01XmAgJ/rmKwM6CTpq6ik.a6hRgss2VoVC2tOvRLSr0qQLJMFZ5Yq', NULL, NULL),
(3, 'ouf', 'azert', 'oufaz@gmail.com', '$2y$10$7lsbIM90uyIfwhSLDsLVeup38NHiwz6UTHd0SI0UCeuZ27mAYm08C', NULL, NULL),
(4, 'azerty', 'uiop', 'qsd@gmail.com', '$2y$10$U7zulYpKN5OzCq2A/SxN0.chYcOtFqSYqwcVbuEypJx54C1rqlKsq', NULL, NULL),
(5, 'az', 'er', 'ty@gmail.com', '$2y$10$HhhgDINOyfyURkF78X08curtAvzUNoxEB1Oichbt5JgTNnsts79EK', NULL, NULL),
(6, 'user2', 'user2', 'user2@gmail.com', '$2y$10$PGGw3bPNOkdLmklVF1iyruN//iIu.k9x2DWe.7EpduKzLKj9w7Qme', NULL, NULL),
(7, 'user2', 'user2', 'user2@gmail.com', '$2y$10$XBcTdqHvlCphfP7sLvj0q.RSB6bqHC3s/tAPCsaLeRn0mrMQqvpRu', NULL, NULL),
(8, 'user3', 'user3', 'user3@gmail.com', '$2y$10$rFJQM2le.6wnWsyND6bPp.S2HZ6UzyGtr8tc2xP7qM0xnZ.S4obIu', NULL, NULL),
(9, 'user2', 'user2', 'user2@gmail.com', '$2y$10$fBpdgIPYfgSjs2cvn4tqdeiDQ8fNhGPLT0vRLSnNmV8LDFkzECZEm', NULL, NULL),
(10, 'user3', 'user3', 'user3@gmail.com', '$2y$10$oFDr18IOJHpnOzDv1bC4Oud8PLXteg01gd2Hgvv3kU3ciy.LVpgvO', NULL, NULL),
(11, '', '', 'a@b.fr', '$2y$10$pg3cZeTQeDGHV2a5pMIViOJJB4pUlxl1/Aej0yd/FzMYKovItVCjW', NULL, NULL),
(12, '', '', 'a@b.fr', '$2y$10$I8SgXhnCsg6X70lgiaq6cuDXixDG.b9BwH3DVZ/JagMdpPlIOf5aW', NULL, NULL),
(13, '', '', 'a@b.fr', '$2y$10$MH/.tpyTNVG/uWMF/c9ZQuN8ODdjPsq4bwYWFYS/sByepAK5s56Nm', NULL, NULL),
(14, '', '', 'a@b.fr', '$2y$10$GleNBGKtsxMPV7VrrzxoVOBAEUssvqVua/5frp1XzuWmVIQViRiu2', NULL, NULL),
(15, '', '', 'a@b.fr', '$2y$10$13iSdnFSwLFVqYjmcoREa.OwXDwXusJL1ftr1Szoed5Zjl02oxdou', NULL, NULL),
(16, '', '', 'a@b.fr', '$2y$10$s3uexJMYlGoJ27UVanTmyOOoxJre82KaxN2MOdM8vRv6I8N3yahc6', NULL, NULL),
(17, '', '', 'a@b.fr', '$2y$10$0BRl4uVW6sTsxC5Q936OR.pkJNQ8gn8xrbB/pRqT1C3Vodmf9paT.', NULL, NULL),
(18, '', '', 'a@b.fr', '$2y$10$u0ZwpmBEbzDCie8V7OKwrenZHp8/CNe313iAGIogwWBGTEYDP2oe.', NULL, NULL),
(19, '', '', 'a@b.fr', '$2y$10$fN3tlVA5YwPj903jxWtsA.H4zhCHeA3lEhPW/pONxG1LyBhP/RA0q', NULL, NULL),
(20, '', '', 'a@b.fr', '$2y$10$mRFUxu0FoHErpiDZbCGx/.xzNXC.vAyGuoiSAvrT5sGMJu5FbIZbS', NULL, NULL),
(21, 'a', '', 'a@b.fr', '$2y$10$eKGfeKLdJ/wqKBd5gg0lV.Ol8dfY80Eki8PCe1y65EMBizO3D044W', NULL, NULL),
(22, 'a', 'a', 'a@b.fr', '$2y$10$4j110Qx5lOHgMAoXyEA.hOk9sL/fCdsD2K0nTexCYn.dAt2K5MVMW', NULL, NULL),
(23, 'a', 'a', 'a@b.fr', '$2y$10$qJGXLAeFVd75twymSu80G.KvzgLAaG4hf8L.9oXJ9k1DxKRyCcLsi', NULL, NULL),
(24, 'a', 'aa', 'a@c.fr', '$2y$10$pxO7l/psv0sT.4Yg7twVg.G148xtBbIhYqKfGcAIBXv5eVH7psSWa', NULL, NULL),
(26, 'user6', 'user7', 'user6@gmail.com', '$2y$10$TsxUQCdeE04Um5JzoAQqWOC488DG0sShR/8KO8x0EKktWisZsIDfi', NULL, NULL),
(27, 'user6', 'user7', 'user6@gmail.com', '$2y$10$SwWqeEMBCEnXQG1lpeQSqOWqMkljP3MQCwWk.vQlovABAjL0qQZc2', NULL, NULL),
(28, 'test', 'test1', 'test@test.fr', '$2y$10$gF/vFYx8PW./hy9sSPJVbeiTCOgMbAgixf4/5Km0ilJTHf1p0Ab4K', NULL, NULL),
(29, 'BENASR', 'MOHAMED', 'benasrmohamed94@gmail.com', '$2y$10$BhHPBeiblRznbXHQQCzZM.tA5lvd8wkbFfgKWFAvm2oaLXIA.JdKq', '0000-00-00 00:00:00', '2025-01-17 14:28:14');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_avis_modele` (`id_modele`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`);

--
-- Index pour la table `detail_commande`
--
ALTER TABLE `detail_commande`
  ADD PRIMARY KEY (`id_detail`);

--
-- Index pour la table `historique_commandes`
--
ALTER TABLE `historique_commandes`
  ADD PRIMARY KEY (`id_commande`);

--
-- Index pour la table `modele`
--
ALTER TABLE `modele`
  ADD PRIMARY KEY (`id_modele`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id_panier`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `detail_commande`
--
ALTER TABLE `detail_commande`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `historique_commandes`
--
ALTER TABLE `historique_commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `modele`
--
ALTER TABLE `modele`
  MODIFY `id_modele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id_panier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `fk_avis_modele` FOREIGN KEY (`id_modele`) REFERENCES `modele` (`id_modele`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
