
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE DATABASE IF NOT EXISTS `aquarium_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `aquarium_system`;

CREATE TABLE `alerts` (
  `alert_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `parameter_type` varchar(20) NOT NULL,
  `recorded_value` decimal(5,2) NOT NULL,
  `alert_message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `sensorreadings` (
  `reading_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `temperature` decimal(4,2) NOT NULL,
  `ph_level` decimal(4,2) NOT NULL,
  `turbidity` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `alerts`
  ADD PRIMARY KEY (`alert_id`);

ALTER TABLE `sensorreadings`
  ADD PRIMARY KEY (`reading_id`);

ALTER TABLE `alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sensorreadings`
  MODIFY `reading_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

