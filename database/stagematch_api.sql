CREATE DATABASE IF NOT EXISTS `stagematch_api_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `stagematch_api_db`;

DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `vacancy_skills`;
DROP TABLE IF EXISTS `student_skills`;
DROP TABLE IF EXISTS `vacancies`;
DROP TABLE IF EXISTS `skills`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('student', 'company') NOT NULL,
    `api_token` VARCHAR(64) NULL DEFAULT NULL UNIQUE,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `skills` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `student_skills` (
    `user_id` INT UNSIGNED NOT NULL,
    `skill_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `skill_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vacancies` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `location` VARCHAR(100) NOT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL,
    FOREIGN KEY (`company_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vacancy_skills` (
    `vacancy_id` INT UNSIGNED NOT NULL,
    `skill_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`vacancy_id`, `skill_id`),
    FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `applications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vacancy_id` INT UNSIGNED NOT NULL,
    `student_id` INT UNSIGNED NOT NULL,
    `motivation` TEXT NULL,
    `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL,
    UNIQUE (`vacancy_id`, `student_id`),
    FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`) VALUES
('Jan de Vries', 'jan@stagematch.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'student', '2026-10-01 09:00:00'),
('Sara Bakker', 'sara@stagematch.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'student', '2026-10-01 09:00:00'),
('Tim Jansen', 'tim@stagematch.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'student', '2026-10-01 09:00:00'),
('Pixelwerk', 'info@pixelwerk.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00'),
('Datalab Noord', 'info@datalab.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00'),
('Webstudio Hunze', 'info@webstudio.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00'),
('Noorderlicht Software', 'info@noorderlicht.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00');

INSERT INTO `skills` (`name`) VALUES
('PHP'), ('JavaScript'), ('HTML'), ('CSS'), ('SQL'),
('Git'), ('Laravel'), ('React'), ('Vue'), ('Node.js'),
('Python'), ('Java'), ('C#'), ('Docker'), ('Linux'),
('REST API'), ('Figma'), ('Scrum'), ('Engels'), ('Communicatie');

INSERT INTO `student_skills` (`user_id`, `skill_id`) VALUES
(1, 1), (1, 5), (1, 6), (1, 3), (1, 4), (1, 16),
(2, 2), (2, 8), (2, 3), (2, 4), (2, 17),
(3, 11), (3, 5), (3, 15), (3, 14);

INSERT INTO `vacancies` (`company_id`, `title`, `description`, `location`, `created_at`) VALUES
(4, 'Stage back-end developer', 'Je bouwt mee aan de API''s achter de webshops van onze klanten. Je werkt in PHP met Laravel, schrijft je eigen SQL-queries en je code gaat via Git en een code review naar productie.', 'Groningen', '2026-10-05 10:00:00'),
(4, 'Stage front-end developer', 'Samen met onze designers zet je ontwerpen om in snelle en toegankelijke websites. Je werkt met React en leert hoe je componenten herbruikbaar maakt.', 'Groningen', '2026-10-05 10:30:00'),
(5, 'Stage data engineer', 'Je helpt zorginstellingen in Drenthe om hun data op orde te krijgen. Je schrijft Python-scripts die data ophalen en opschonen, en draait alles in Docker op onze Linux-servers. Een deel van de documentatie is in het Engels.', 'Assen', '2026-10-06 14:00:00'),
(6, 'Stage full-stack developer', 'Wij maken websites en klantportalen voor bedrijven in Groningen en Drenthe. Je werkt aan de PHP-backend en de Vue-frontend en draait mee in ons Scrum-team met sprints van twee weken.', 'Hoogezand', '2026-10-07 11:00:00'),
(6, 'Stage UX designer', 'Je onderzoekt hoe klanten onze portalen gebruiken en werkt verbeteringen uit in Figma. Daarna bouw je je ontwerp samen met de developers na in HTML en CSS en presenteer je het zelf aan de klant.', 'Hoogezand', '2026-10-07 11:15:00'),
(4, 'Stage C# developer', 'Voor een logistiek bedrijf in de Eemshaven bouwen we een planningssysteem in C#. Je werkt mee in een Scrum-team, schrijft SQL voor de rapportages en overlegt soms in het Engels met de klant.', 'Groningen', '2026-10-08 09:00:00'),
(5, 'Stage DevOps', 'Je zorgt ervoor dat onze servers en deploys soepel draaien. Je beheert Linux-servers, zet applicaties in Docker-containers en automatiseert taken met Python. Alles staat in Git.', 'Assen', '2026-10-08 13:30:00'),
(7, 'Stage Node.js developer', 'Wij bouwen een app waarmee melkveehouders in Friesland en Groningen hun bedrijf beheren. Je werkt aan de Node.js-backend en de REST API die de app gebruikt, met een SQL-database eronder.', 'Drachten', '2026-10-09 10:00:00');

INSERT INTO `vacancy_skills` (`vacancy_id`, `skill_id`) VALUES
(1, 1), (1, 5), (1, 6), (1, 7), (1, 16),
(2, 2), (2, 3), (2, 4), (2, 8),
(3, 11), (3, 5), (3, 14), (3, 15), (3, 19),
(4, 1), (4, 2), (4, 5), (4, 9), (4, 6), (4, 18),
(5, 17), (5, 3), (5, 4), (5, 20),
(6, 13), (6, 5), (6, 6), (6, 18), (6, 19),
(7, 15), (7, 14), (7, 6), (7, 11),
(8, 10), (8, 2), (8, 16), (8, 5), (8, 6);

INSERT INTO `applications` (`vacancy_id`, `student_id`, `motivation`, `status`, `created_at`) VALUES
(1, 1, 'Op school heb ik een REST API in PHP gebouwd en ik wil graag leren hoe dat in een echt team gaat. Laravel wil ik tijdens de stage onder de knie krijgen.', 'pending', '2026-10-10 12:00:00'),
(2, 2, 'Ik maak al een jaar websites met React en vind het leuk om een ontwerp precies goed na te bouwen.', 'accepted', '2026-10-11 09:30:00'),
(3, 3, 'Ik werk graag met data en heb al een Python-script geschreven dat gegevens uit een API in een database zet.', 'rejected', '2026-10-12 16:45:00'),
(4, 1, NULL, 'pending', '2026-10-13 08:15:00'),
(7, 3, 'Thuis draai ik een eigen Linux-server met Docker. Ik wil leren hoe dat bij een bedrijf op grotere schaal werkt.', 'pending', '2026-10-14 10:20:00');
