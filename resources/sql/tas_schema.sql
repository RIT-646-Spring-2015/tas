-- 04/21/15 16:22:34
-- Model: TAS Model    Version: 1.0
-- Team Win
-- SQLite3

-- -----------------------------------------------------
-- Table `User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `User`;

CREATE TABLE IF NOT EXISTS `User` (
  `Username` VARCHAR(20) NOT NULL,
  `Password` VARCHAR(50) NOT NULL,
  `Enabled` TINYINT(1) NOT NULL,
  `FirstName` VARCHAR(50) NOT NULL,
  `LastName` VARCHAR(50) NOT NULL,
  `Email` VARCHAR(50) NOT NULL,
  `DateJoined` DATETIME DEFAULT (DATETIME('NOW', 'LOCALTIME')) NOT NULL,
  `LastOnline` DATETIME DEFAULT (DATETIME('NOW', 'LOCALTIME')) NOT NULL,
  PRIMARY KEY (`Username`));


-- -----------------------------------------------------
-- Table `Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Role`;

CREATE TABLE IF NOT EXISTS `Role` (
  `Name` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`Name`));


-- -----------------------------------------------------
-- Table `UserRole`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserRole`;

CREATE TABLE IF NOT EXISTS `UserRole` (
  `Username` VARCHAR(50) NOT NULL,
  `RoleName` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`Username`, `RoleName`),
  FOREIGN KEY (`Username`)
    REFERENCES `User` (`Username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`RoleName`)
    REFERENCES `Role` (`Name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `Status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Status`;

CREATE TABLE IF NOT EXISTS `Status` (
  `Name` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`Name`));


-- -----------------------------------------------------
-- Table `Topic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Topic`;

CREATE TABLE IF NOT EXISTS `Topic` (
  `Name` VARCHAR(60) NOT NULL,
  `Link` VARCHAR(1000) NULL,
  `SubmissionDate` DATETIME DEFAULT (DATETIME('NOW', 'LOCALTIME')) NOT NULL,
  `Blacklisted` TINYINT(1) DEFAULT 0 NOT NULL,
  `Status` VARCHAR(10) DEFAULT 'SUBMITTED' NOT NULL,
  PRIMARY KEY (`Name`),
  FOREIGN KEY (`Status`)
    REFERENCES `Status` (`Name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `UserTopic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserTopic` ;

CREATE TABLE IF NOT EXISTS `UserTopic` (
  `Username` VARCHAR(20) NOT NULL,
  `TopicName` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`Username`, `TopicName`),
  FOREIGN KEY (`Username`)
    REFERENCES `User` (`Username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`TopicName`)
    REFERENCES `Topic` (`Name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `Course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Course` ;

CREATE TABLE IF NOT EXISTS `Course` (
  `Number` VARCHAR(15) NOT NULL,
  `Name` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`Number`));


-- -----------------------------------------------------
-- Table `UserCourse`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserCourse` ;

CREATE TABLE IF NOT EXISTS `UserCourse` (
  `Username` VARCHAR(20) NOT NULL,
  `CourseNumber` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Username`, `CourseNumber`),
  FOREIGN KEY (`Username`)
    REFERENCES `User` (`Username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`CourseNumber`)
    REFERENCES `Course` (`Number`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Data for table `Role`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `Role` (`Name`) VALUES ('ROLE_ADMIN');
INSERT INTO `Role` (`Name`) VALUES ('ROLE_PROFESSOR');
INSERT INTO `Role` (`Name`) VALUES ('ROLE_TA');
INSERT INTO `Role` (`Name`) VALUES ('ROLE_STUDENT');

COMMIT;

-- -----------------------------------------------------
-- Data for table `User`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `User` (`Username`, `Password`, `Enabled`, `FirstName`, `LastName`, `Email`) VALUES ('admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Gregor', 'Mendel', 'g.mendel@tas.com');

COMMIT;

-- -----------------------------------------------------
-- Data for table `UserRole`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `UserRole` (`Username`, `RoleName`) VALUES ('admin', 'ROLE_ADMIN');

COMMIT;

-- -----------------------------------------------------
-- Data for table `Status`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `Status` (`Name`) VALUES ('SUBMITTED');
INSERT INTO `Status` (`Name`) VALUES ('APPROVED');
INSERT INTO `Status` (`Name`) VALUES ('REJECTED');

COMMIT;
