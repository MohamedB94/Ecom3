-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 27 mai 2024 à 16:34
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
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` date NOT NULL,
  `adresse` varchar(150) NOT NULL,
  `code_postal` varchar(20) NOT NULL,
  `ville` varchar(80) NOT NULL,
  `complement_adresse` varchar(150) NOT NULL,
  `valide` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `detail-commande`
--

CREATE TABLE `detail-commande` (
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
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom`, `prenom`, `email`, `mdp`) VALUES
(1, 'user1', 'user1', 'user1@gmail.com', '$2y$10$/ek3/fUOAkhTqlNdo18PmOjbMcGVQAsxkcHClcpG81z4ixkfpaMfy'),
(2, 'user2', 'user2', 'user2@gmail.com', '$2y$10$01XmAgJ/rmKwM6CTpq6ik.a6hRgss2VoVC2tOvRLSr0qQLJMFZ5Yq'),
(3, 'ouf', 'azert', 'oufaz@gmail.com', '$2y$10$7lsbIM90uyIfwhSLDsLVeup38NHiwz6UTHd0SI0UCeuZ27mAYm08C'),
(4, 'azerty', 'uiop', 'qsd@gmail.com', '$2y$10$U7zulYpKN5OzCq2A/SxN0.chYcOtFqSYqwcVbuEypJx54C1rqlKsq'),
(5, 'az', 'er', 'ty@gmail.com', '$2y$10$HhhgDINOyfyURkF78X08curtAvzUNoxEB1Oichbt5JgTNnsts79EK'),
(6, 'user2', 'user2', 'user2@gmail.com', '$2y$10$PGGw3bPNOkdLmklVF1iyruN//iIu.k9x2DWe.7EpduKzLKj9w7Qme'),
(7, 'user2', 'user2', 'user2@gmail.com', '$2y$10$XBcTdqHvlCphfP7sLvj0q.RSB6bqHC3s/tAPCsaLeRn0mrMQqvpRu'),
(8, 'user3', 'user3', 'user3@gmail.com', '$2y$10$rFJQM2le.6wnWsyND6bPp.S2HZ6UzyGtr8tc2xP7qM0xnZ.S4obIu'),
(9, 'user2', 'user2', 'user2@gmail.com', '$2y$10$fBpdgIPYfgSjs2cvn4tqdeiDQ8fNhGPLT0vRLSnNmV8LDFkzECZEm');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`);

--
-- Index pour la table `detail-commande`
--
ALTER TABLE `detail-commande`
  ADD PRIMARY KEY (`id_detail`);

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
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `detail-commande`
--
ALTER TABLE `detail-commande`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `modele`
--
ALTER TABLE `modele`
  MODIFY `id_modele` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id_panier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
