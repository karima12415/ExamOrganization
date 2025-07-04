-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 18 juin 2025 à 20:22
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
-- Base de données : `exam-org`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin1`
--

CREATE TABLE `admin1` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(10) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin1`
--

INSERT INTO `admin1` (`id`, `email`, `role`, `password`) VALUES
(1, 'attiasalemkarima@gmail.com', 'Admin', '$2y$10$aO.wI137Gus/X1QEybCh3OqUchrRf3kz7mnjrriOwsDPt85FvLY2q');

-- --------------------------------------------------------

--
-- Structure de la table `admin2`
--

CREATE TABLE `admin2` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(10) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `id_univ` int(11) DEFAULT NULL,
  `id_faculty` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin2`
--

INSERT INTO `admin2` (`id`, `email`, `role`, `password`, `id_univ`, `id_faculty`, `name`) VALUES
(1, 'nana9228476@gmail.com', 'Admin', '$2y$10$kaItABWwl68uQp3DicWzceEIYxGGRS3Jl80kHNwkPOfh7SWwn/IDa', 1, 1, 'nadjia'),
(19, 'chaima@gmail.com', 'Admin', '$2y$10$dtX4gLmRpdqzvAWZN2ng5u5fXka5Xa2HJFTzQFxkcwqutA8NOAZmu', 1, 1, 'chaima'),
(20, 'm.hammou@univ-chlef.dz', 'Admin', '$2y$10$RmzM7BEJL3dsEEgB.ruIKOzrrwL5impHhqEecfKEeMjqjEqAvirF.', 2, 3, 'hammou');

-- --------------------------------------------------------

--
-- Structure de la table `department`
--

CREATE TABLE `department` (
  `id_department` int(11) NOT NULL,
  `name_deprtment` varchar(255) DEFAULT NULL,
  `nbOfRoom` int(11) DEFAULT NULL,
  `floor` int(2) DEFAULT NULL,
  `id_faculty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `department`
--

INSERT INTO `department` (`id_department`, `name_deprtment`, `nbOfRoom`, `floor`, `id_faculty`) VALUES
(1, 'informatique', 10, 2, 1),
(2, ' Sciences Economiques', 30, 4, 3);

-- --------------------------------------------------------

--
-- Structure de la table `exam_schedules`
--

CREATE TABLE `exam_schedules` (
  `id` int(11) NOT NULL,
  `filiere_id` int(11) DEFAULT NULL,
  `specialty_id` int(11) DEFAULT NULL,
  `exam_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `slot_start` time DEFAULT NULL,
  `slots` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exam_schedules`
--

INSERT INTO `exam_schedules` (`id`, `filiere_id`, `specialty_id`, `exam_type`, `start_date`, `end_date`, `slot_start`, `slots`, `created_at`) VALUES
(24, 1, 1, 'semester1_Regular', '2025-06-01', '2025-06-01', '08:00:00', 2, '2025-06-18 12:19:21');

-- --------------------------------------------------------

--
-- Structure de la table `exam_sessions`
--

CREATE TABLE `exam_sessions` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `slot` varchar(20) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `module_teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exam_sessions`
--

INSERT INTO `exam_sessions` (`id`, `schedule_id`, `exam_date`, `slot`, `level_id`, `module_id`, `module_teacher_id`) VALUES
(36, 24, '2025-06-01', '08:00 - 09:30', 4, 3, 6),
(37, 24, '2025-06-01', '09:30 - 11:00', 4, 14, 1);

-- --------------------------------------------------------

--
-- Structure de la table `faculty`
--

CREATE TABLE `faculty` (
  `id_faculty` int(11) NOT NULL,
  `name_faculty` varchar(255) DEFAULT NULL,
  `image_faculty` varchar(255) DEFAULT NULL,
  `id_univ` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `faculty`
--

INSERT INTO `faculty` (`id_faculty`, `name_faculty`, `image_faculty`, `id_univ`) VALUES
(1, 'faculty of exact sciences and informatics', 'logoFact/logofaculty.PNG', 1),
(2, 'faculty of nature and life science', 'logoFact/logosnv.PNG', 1),
(3, 'faculty of Sciences Economiques, Commerciales et de Sciences de Gestion ', 'logoFact/logoempty.PNG', 2);

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

CREATE TABLE `filiere` (
  `id_filiere` int(11) NOT NULL,
  `name_filiere` varchar(255) DEFAULT NULL,
  `id_department` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`id_filiere`, `name_filiere`, `id_department`) VALUES
(1, 'informatique', 1),
(3, ' Sciences Economiques', 2),
(5, 'reseau', 1);

-- --------------------------------------------------------

--
-- Structure de la table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialty_id` int(11) NOT NULL,
  `student_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `levels`
--

INSERT INTO `levels` (`id`, `name`, `specialty_id`, `student_count`) VALUES
(4, 'L3', 1, 25),
(9, 'L1', 1, 12),
(11, 'M1', 21, 100);

-- --------------------------------------------------------

--
-- Structure de la table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_prog` bit(1) DEFAULT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modules`
--

INSERT INTO `modules` (`id`, `name`, `is_prog`, `level_id`) VALUES
(3, 'algoritme', b'0', 4),
(4, 'App mobile', b'1', 4),
(11, 'DSS', b'0', 4),
(12, 'SI', b'0', 4),
(14, 'AI', b'0', 4),
(17, 'structeur', b'0', 9);

-- --------------------------------------------------------

--
-- Structure de la table `organizer`
--

CREATE TABLE `organizer` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `id_univ` int(11) DEFAULT NULL,
  `id_faculty` int(11) DEFAULT NULL,
  `added_by_admin2` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `organizer`
--

INSERT INTO `organizer` (`id`, `email`, `password`, `id_univ`, `id_faculty`, `added_by_admin2`, `name`, `role`) VALUES
(2, 'fatma@gmail.com', '$2y$10$Gl71VJMO86VueukrQQtRi.LgrvkZflI3f9xzRPdCWIhSnv3/O9NNC', 1, 1, 19, 'fatma', 'Organizer'),
(3, 'karima@gmail.com', '$2y$10$e7gFEclMCV6NGRpK/2EJbeydIg5Qh.euCE3HKud41cM9UwTGuC1US', 1, 1, 19, 'karima', 'Organizer'),
(5, 'khaled@gmail.com', '$2y$10$DqZ4..1PgRalQ4.KRm85Cu.wsSEn0dYJyo8Sk/gkHAqia5unDzX4C', 1, 1, 19, 'khaled', 'Organizer');

-- --------------------------------------------------------

--
-- Structure de la table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `student_count` int(11) NOT NULL,
  `supervisor_count` int(11) NOT NULL,
  `id_department` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `student_count`, `supervisor_count`, `id_department`) VALUES
(1, '001', 20, 2, 1),
(5, '002', 25, 2, 1),
(6, 'TEST1', 13, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `session_rooms`
--

CREATE TABLE `session_rooms` (
  `session_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `session_rooms`
--

INSERT INTO `session_rooms` (`session_id`, `room_id`, `teacher_id`) VALUES
(36, 5, 1),
(36, 5, 9),
(37, 1, 6),
(37, 1, 13);

-- --------------------------------------------------------

--
-- Structure de la table `specialties`
--

CREATE TABLE `specialties` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `id_filiere` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `specialties`
--

INSERT INTO `specialties` (`id`, `name`, `id_filiere`) VALUES
(1, 'informatique', 1),
(21, 'isaia', 1);

-- --------------------------------------------------------

--
-- Structure de la table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `hourly_size` int(10) NOT NULL,
  `isfree` bit(1) DEFAULT NULL,
  `id_faculty` int(11) DEFAULT NULL,
  `used_hours` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `email`, `hourly_size`, `isfree`, `id_faculty`, `used_hours`) VALUES
(1, 'Dr. Ali Bencheikh', 'ali.bencheikh@example.com', 6, b'1', 1, 1),
(6, 'Prof. Karim Belkacem', 'karim.belkacem@univ-chlef.dz', 3, b'1', 1, 1),
(9, 'Mme. Selma Zerguine', 'selma.zerguine@univ-chlef.dz', 1, b'1', 1, 1),
(13, 'chaima', 'chaima@gmail.com', 2, b'0', 1, 1),
(16, 'test', 'testfacdefr@gmail.com', 3, b'0', 2, 0);

-- --------------------------------------------------------

--
-- Structure de la table `teacher_modules`
--

CREATE TABLE `teacher_modules` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `teacher_modules`
--

INSERT INTO `teacher_modules` (`id`, `teacher_id`, `module_id`) VALUES
(4, 13, 3),
(5, 13, 4),
(9, 1, 11),
(10, 1, 14),
(11, 6, 3),
(13, 9, 17);

-- --------------------------------------------------------

--
-- Structure de la table `university`
--

CREATE TABLE `university` (
  `id_univ` int(11) NOT NULL,
  `name_univ` varchar(255) NOT NULL,
  `state_univ` varchar(255) NOT NULL,
  `adress_univ` varchar(255) NOT NULL,
  `imageU` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `university`
--

INSERT INTO `university` (`id_univ`, `name_univ`, `state_univ`, `adress_univ`, `imageU`) VALUES
(1, 'Hassiba Benbouali', 'Chlef', 'Awlad Faris', 'logoUniv/téléchargement.png'),
(2, 'hassiba benbouali', 'chlef', 'El Hassania', 'logoUniv/téléchargement.png');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin1`
--
ALTER TABLE `admin1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `admin2`
--
ALTER TABLE `admin2`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_univ` (`id_univ`),
  ADD KEY `id_faculty` (`id_faculty`);

--
-- Index pour la table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id_department`),
  ADD KEY `id_faculty` (`id_faculty`);

--
-- Index pour la table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `fk_module_teacher` (`module_teacher_id`);

--
-- Index pour la table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id_faculty`),
  ADD KEY `id_univ` (`id_univ`);

--
-- Index pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`id_filiere`),
  ADD KEY `id_department` (`id_department`);

--
-- Index pour la table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `specialty_id` (`specialty_id`);

--
-- Index pour la table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `level_id` (`level_id`);

--
-- Index pour la table `organizer`
--
ALTER TABLE `organizer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_univ` (`id_univ`),
  ADD KEY `id_faculty` (`id_faculty`),
  ADD KEY `added_by_admin2` (`added_by_admin2`);

--
-- Index pour la table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_department` (`id_department`);

--
-- Index pour la table `session_rooms`
--
ALTER TABLE `session_rooms`
  ADD PRIMARY KEY (`session_id`,`room_id`,`teacher_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Index pour la table `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_filiere` (`id_filiere`);

--
-- Index pour la table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher_faculty` (`id_faculty`);

--
-- Index pour la table `teacher_modules`
--
ALTER TABLE `teacher_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Index pour la table `university`
--
ALTER TABLE `university`
  ADD PRIMARY KEY (`id_univ`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin1`
--
ALTER TABLE `admin1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `admin2`
--
ALTER TABLE `admin2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `department`
--
ALTER TABLE `department`
  MODIFY `id_department` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id_faculty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `filiere`
--
ALTER TABLE `filiere`
  MODIFY `id_filiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `organizer`
--
ALTER TABLE `organizer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `specialties`
--
ALTER TABLE `specialties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `teacher_modules`
--
ALTER TABLE `teacher_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `university`
--
ALTER TABLE `university`
  MODIFY `id_univ` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin2`
--
ALTER TABLE `admin2`
  ADD CONSTRAINT `admin2_ibfk_1` FOREIGN KEY (`id_univ`) REFERENCES `university` (`id_univ`),
  ADD CONSTRAINT `admin2_ibfk_2` FOREIGN KEY (`id_faculty`) REFERENCES `faculty` (`id_faculty`);

--
-- Contraintes pour la table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`id_faculty`) REFERENCES `faculty` (`id_faculty`);

--
-- Contraintes pour la table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD CONSTRAINT `exam_sessions_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `exam_schedules` (`id`),
  ADD CONSTRAINT `exam_sessions_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`),
  ADD CONSTRAINT `exam_sessions_ibfk_3` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`),
  ADD CONSTRAINT `fk_module_teacher` FOREIGN KEY (`module_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`id_univ`) REFERENCES `university` (`id_univ`);

--
-- Contraintes pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD CONSTRAINT `filiere_ibfk_1` FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`);

--
-- Contraintes pour la table `levels`
--
ALTER TABLE `levels`
  ADD CONSTRAINT `levels_ibfk_1` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`);

--
-- Contraintes pour la table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`);

--
-- Contraintes pour la table `organizer`
--
ALTER TABLE `organizer`
  ADD CONSTRAINT `organizer_ibfk_1` FOREIGN KEY (`id_univ`) REFERENCES `university` (`id_univ`),
  ADD CONSTRAINT `organizer_ibfk_2` FOREIGN KEY (`id_faculty`) REFERENCES `faculty` (`id_faculty`),
  ADD CONSTRAINT `organizer_ibfk_3` FOREIGN KEY (`added_by_admin2`) REFERENCES `admin2` (`id`);

--
-- Contraintes pour la table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `session_rooms`
--
ALTER TABLE `session_rooms`
  ADD CONSTRAINT `session_rooms_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `exam_sessions` (`id`),
  ADD CONSTRAINT `session_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `session_rooms_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Contraintes pour la table `specialties`
--
ALTER TABLE `specialties`
  ADD CONSTRAINT `specialties_ibfk_1` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id_filiere`);

--
-- Contraintes pour la table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `fk_teacher_faculty` FOREIGN KEY (`id_faculty`) REFERENCES `faculty` (`id_faculty`);

--
-- Contraintes pour la table `teacher_modules`
--
ALTER TABLE `teacher_modules`
  ADD CONSTRAINT `teacher_modules_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `teacher_modules_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
