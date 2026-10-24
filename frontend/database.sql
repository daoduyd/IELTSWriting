-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: April 25, 2025
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.31

CREATE DATABASE IF NOT EXISTS IELTS_Writing;
USE IELTS_Writing;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `IELTS_Writing`
--

-- --------------------------------------------------------
-- Table structure for table `ai_interaction_logs`
CREATE TABLE `ai_interaction_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `essay_id` INT,
    `ai_model` VARCHAR(100),
    `interaction_type` ENUM('support', 'writing_assistance', 'plagiarism_check') NOT NULL,
    `prompt` TEXT,
    `ai_response` TEXT,
    `similarity_score` FLOAT,
    `is_potential_plagiarism` ENUM('Yes', 'No', 'Uncertain') DEFAULT 'Uncertain',
    `ai_assistance_level` ENUM('None', 'Low', 'Medium', 'High') DEFAULT 'None',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`essay_id`) REFERENCES `essay_responses`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table structure for table `ds_cau_hoi`
CREATE TABLE `ds_cau_hoi` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `question` TEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `question_image` VARCHAR(255),
    `time_limit` INT DEFAULT 40 COMMENT 'Time limit in minutes',
    `word_count` INT DEFAULT 250 COMMENT 'Recommended word count',
    `topic` VARCHAR(50) COMMENT 'Topic of the question (e.g., Education, Environment)',
    `difficulty_level` ENUM('Beginner', 'Intermediate', 'Advanced') COMMENT 'Difficulty level of the question',
    `question_type` ENUM('AdvantagesDisadvantages', 'ProblemSolution', 'Opinion', 'Discussion', 'AgreeDisagree', '2-partQuestion', 'LineGraph', 'BarChart', 'PieChart', 'Mixes', 'Table', 'Map', 'Process') COMMENT 'Type of question'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `ds_de_thi`
CREATE TABLE `ds_de_thi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ten_de_thi` VARCHAR(255),
    `category` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `difficulty_level` ENUM('Beginner', 'Intermediate', 'Advanced') COMMENT 'Difficulty level of the test',
    `topic` VARCHAR(50) COMMENT 'Topic for practice tests',
    `question_type` ENUM('AdvantagesDisadvantages', 'ProblemSolution', 'Opinion', 'Discussion', 'AgreeDisagree', '2-partQuestion', 'LineGraph', 'BarChart', 'PieChart', 'Mixes', 'Table', 'Map', 'Process') COMMENT 'Question type for practice tests'
);

-- --------------------------------------------------------
-- Table structure for table `ds_de_thi_cau_hoi`
CREATE TABLE `ds_de_thi_cau_hoi` (
    `de_thi_id` INT,
    `cau_hoi_id` INT,
    PRIMARY KEY (`de_thi_id`, `cau_hoi_id`),
    FOREIGN KEY (`de_thi_id`) REFERENCES `ds_de_thi`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`cau_hoi_id`) REFERENCES `ds_cau_hoi`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table structure for table `essay_responses`
CREATE TABLE `essay_responses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `question_id` INT NOT NULL,
    `response_text` LONGTEXT,
    `file_path` VARCHAR(255),
    `file_type` VARCHAR(10),
    `word_count` INT,
    `submission_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `time_taken` INT COMMENT 'Time taken in minutes',
    `feedback` TEXT,
    `score` FLOAT,
    `ai_assistance_level` ENUM('None', 'Low', 'Medium', 'High') DEFAULT 'None',
    `ai_detection_score` FLOAT,
    `plagiarism_risk` ENUM('Low', 'Medium', 'High') DEFAULT 'Low',
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `ds_cau_hoi`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table structure for table `essay_reviews`
CREATE TABLE `essay_reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `essay_id` INT NOT NULL,
    `reviewer_id` INT,
    `ai_generated_score` FLOAT,
    `ai_task_achievement` FLOAT,
    `ai_coherence_cohesion` FLOAT,
    `ai_lexical_resource` FLOAT,
    `ai_grammatical_range` FLOAT,
    `human_score` FLOAT,
    `human_task_achievement` FLOAT,
    `human_coherence_cohesion` FLOAT,
    `human_lexical_resource` FLOAT,
    `human_grammatical_range` FLOAT,
    `final_score` FLOAT,
    `review_comments` TEXT,
    `ai_dependency_comments` TEXT,
    `advice` TEXT,
    `task_achievement_explanation` TEXT,
    `coherence_cohesion_explanation` TEXT,
    `lexical_resource_explanation` TEXT,
    `grammatical_range_explanation` TEXT,
    `reviewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`essay_id`) REFERENCES `essay_responses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewer_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- --------------------------------------------------------
-- Table structure for table `lectures`
CREATE TABLE `lectures` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL COMMENT 'task1, task2, general tips, etc.',
    `task_type` ENUM('Task1', 'Task2') NOT NULL,
    `question_type` ENUM('AdvantagesDisadvantages', 'ProblemSolution', 'Opinion', 'GraphsWithTrend', 'ComparativeGraphs', 'ProcessesAndMaps') DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table structure for table `sample_answers`
CREATE TABLE `sample_answers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `lecture_id` INT NOT NULL,
    `question_id` INT NOT NULL,
    `category` ENUM('Task 1', 'Task 2') NOT NULL,
    `response_text` LONGTEXT NOT NULL,
    `examiner_comments` TEXT,
    `task_achievement` FLOAT,
    `coherence_cohesion` FLOAT,
    `lexical_resource` FLOAT,
    `grammatical_range` FLOAT,
    `overall_score` FLOAT,
    `errors` TEXT,
    `suggestions` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`lecture_id`) REFERENCES `lectures`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `ds_cau_hoi`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `score_submission_logs`
CREATE TABLE `score_submission_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `essay_id` INT NOT NULL,
    `overall_score` FLOAT NOT NULL,
    `task_achievement` FLOAT NOT NULL,
    `coherence_cohesion` FLOAT NOT NULL,
    `lexical_resource` FLOAT NOT NULL,
    `grammatical_range` FLOAT NOT NULL,
    `ai_detection_score` FLOAT,
    `plagiarism_risk` ENUM('Low', 'Medium', 'High') DEFAULT 'Low',
    `ai_assistance_level` ENUM('Low', 'Medium', 'High') DEFAULT 'Low',
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`essay_id`) REFERENCES `essay_responses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `test_attempts`
CREATE TABLE `test_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `de_thi_id` INT NOT NULL,
    `score` FLOAT,
    `submission_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `content` TEXT,
    `feedback` TEXT,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`de_thi_id`) REFERENCES `ds_de_thi`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table structure for table `user_progress`
CREATE TABLE `user_progress` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `lecture_id` INT,
    `lecture_component_id` INT DEFAULT NULL,
    `practice_exercise_id` INT,
    `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lecture_id`) REFERENCES `lectures`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lecture_component_id`) REFERENCES `lecture_components`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`practice_exercise_id`) REFERENCES `practice_exercises`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table structure for table `users`
CREATE TABLE `users` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table structure for table `vocabulary_collocations`
CREATE TABLE `vocabulary_collocations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `lecture_id` INT NOT NULL,
    `word_or_phrase` VARCHAR(255) NOT NULL,
    `definition` TEXT,
    `example_sentence` TEXT,
    `category` VARCHAR(50) NOT NULL COMMENT 'Topic: Education, Work, Cities, etc.',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`lecture_id`) REFERENCES `lectures`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Triggers
DELIMITER //
CREATE TRIGGER `update_ai_assistance_level` 
AFTER INSERT ON `ai_interaction_logs`
FOR EACH ROW
BEGIN
    IF NEW.interaction_type IN ('writing_assistance', 'support') THEN
        UPDATE `essay_responses` 
        SET `ai_assistance_level` = 
            CASE 
                WHEN `ai_assistance_level` = 'None' THEN 'Low'
                WHEN `ai_assistance_level` = 'Low' THEN 'Medium'
                ELSE 'High'
            END
        WHERE `id` = NEW.essay_id;
    END IF;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER `calculate_final_score` 
BEFORE UPDATE ON `essay_reviews`
FOR EACH ROW
BEGIN
    IF NEW.ai_generated_score IS NOT NULL AND NEW.human_score IS NOT NULL THEN
        SET NEW.final_score = (NEW.ai_generated_score + NEW.human_score) / 2;
    ELSEIF NEW.ai_generated_score IS NOT NULL THEN
        SET NEW.final_score = NEW.ai_generated_score;
    ELSEIF NEW.human_score IS NOT NULL THEN
        SET NEW.final_score = NEW.human_score;
    END IF;
END;//
DELIMITER ;

-- --------------------------------------------------------
-- Insert sample data
INSERT INTO `ds_cau_hoi` (`question`, `category`, `time_limit`, `word_count`, `topic`, `difficulty_level`, `question_type`) VALUES
('The charts below show the percentage of water used for different purposes in six areas of the world.', 'Writing Task 1', 20, 150, 'Environment', 'Intermediate', 'PieChart'),
('Some people believe that unpaid community service should be a compulsory part of high school programs. To what extent do you agree or disagree?', 'Writing Task 2', 40, 250, 'Education', 'Advanced', 'AgreeDisagree'),
('What are the advantages and disadvantages of leaving your country to live or study abroad?', 'Writing Task 2', 40, 250, 'Migration', 'Intermediate', 'AdvantagesDisadvantages'),
('These days, in many countries fewer and fewer people want to become teachers, particularly in secondary schools. What are the reasons for this, and how could the problem be solved?', 'Writing Task 2', 40, 250, 'Education', 'Advanced', 'ProblemSolution');

INSERT INTO `lectures` (`title`, `content`, `category`, `task_type`, `question_type`) VALUES
('Advantages and Disadvantages: Living Abroad', 'This lecture covers how to write an advantages and disadvantages essay about living abroad...', 'Task 2', 'Task2', 'AdvantagesDisadvantages'),
('Problem and Solution: Education and Teaching', 'This lecture explores the causes and solutions to the shortage of teachers...', 'Task 2', 'Task2', 'ProblemSolution'),
('Graphs with a Trend', 'Learn how to describe trends in graphs for Task 1...', 'Task 1', 'Task1', 'GraphsWithTrend');

INSERT INTO `lecture_components` (`lecture_id`, `component_type`, `title`, `content`, `order_index`) VALUES
((SELECT `id` FROM `lectures` WHERE `title` = 'Advantages and Disadvantages: Living Abroad'), 'TryItFirst', 'Try Writing an Essay', 'Write an essay on the advantages and disadvantages of living abroad before studying this unit...', 1),
((SELECT `id` FROM `lectures` WHERE `title` = 'Advantages and Disadvantages: Living Abroad'), 'Spotlight', 'Writing about Life Changes', 'Learn key vocabulary and phrases for discussing life changes...', 2),
((SELECT `id` FROM `lectures` WHERE `title` = 'Advantages and Disadvantages: Living Abroad'), 'ModelEssay', 'Model Essay: Living Abroad', 'Read this model essay to understand the structure and vocabulary needed...', 3);

INSERT INTO `practice_exercises` (`title`, `description`, `category`, `exercise_type`, `lecture_component_id`) VALUES
('Practice Writing an Introduction', 'Write an introduction for an advantages/disadvantages essay.', 'Task 2', 'ExamSkills', (SELECT `id` FROM `lecture_components` WHERE `title` = 'Writing about Life Changes')),
('Vocabulary Check', 'Match words to their definitions.', 'Vocabulary', 'CheckAndChallenge', (SELECT `id` FROM `lecture_components` WHERE `title` = 'Writing about Life Changes'));

INSERT INTO `vocabulary_collocations` (`lecture_id`, `word_or_phrase`, `definition`, `example_sentence`, `category`) VALUES
((SELECT `id` FROM `lectures` WHERE `title` = 'Advantages and Disadvantages: Living Abroad'), 'broaden your horizons', 'To expand your experiences and knowledge', 'Living abroad can broaden your horizons by exposing you to new cultures.', 'Life Experiences'),
((SELECT `id` FROM `lectures` WHERE `title` = 'Problem and Solution: Education and Teaching'), 'work-life balance', 'The balance between work and personal life', 'Teachers often struggle with a poor work-life balance due to excessive paperwork.', 'Education');

COMMIT;