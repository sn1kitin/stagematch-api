CREATE DATABASE IF NOT EXISTS `stagematch_api_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `stagematch_api_db`;

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

-- wachtwoord van alle accounts: wachtwoord
INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`) VALUES
('Jan de Vries', 'jan@stagematch.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'student', '2026-10-01 09:00:00'),
('Sara Bakker', 'sara@stagematch.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'student', '2026-10-01 09:00:00'),
('Tim Jansen', 'tim@stagematch.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'student', '2026-10-01 09:00:00'),
('Pixelwerk', 'info@pixelwerk.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00'),
('Datalab Noord', 'info@datalab.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00'),
('Webstudio Ipsum', 'info@webstudio.test', '$2y$10$c/1Lbiejhz9eZ1FNLa1/rupUFdwHAENJuQ1iNH.LGND3cz.xK7rXu', 'company', '2026-10-01 09:00:00');

INSERT INTO `skills` (`name`) VALUES
('PHP'), ('JavaScript'), ('HTML'), ('CSS'), ('SQL'),
('Git'), ('Laravel'), ('React'), ('Vue'), ('Node.js'),
('Python'), ('Java'), ('C#'), ('Docker'), ('Linux'),
('REST API'), ('Figma'), ('Scrum'), ('Engels'), ('Communicatie');

INSERT INTO `vacancies` (`company_id`, `title`, `description`, `location`, `created_at`) VALUES
(4, 'Stage back-end developer', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Amsterdam', '2026-10-05 10:00:00'),
(4, 'Stage front-end developer', 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'Amsterdam', '2026-10-05 10:30:00'),
(5, 'Stage data engineer', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'Groningen', '2026-10-06 14:00:00'),
(6, 'Stage full-stack developer', 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Utrecht', '2026-10-07 11:00:00'),
(6, 'Stage UX designer', 'Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra.', 'Utrecht', '2026-10-07 11:15:00');