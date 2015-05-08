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
-- Table `Course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Course`;

CREATE TABLE IF NOT EXISTS `Course` (
  `Number` VARCHAR(15) NOT NULL,
  `Name` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`Number`));


-- -----------------------------------------------------
-- Table `UserCourse`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserCourse`;

CREATE TABLE IF NOT EXISTS `UserCourse` (
  `Username` VARCHAR(20) NOT NULL,
  `CourseNumber` VARCHAR(15) NOT NULL,
  `Role` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`Username`, `CourseNumber`),
  FOREIGN KEY (`Username`)
    REFERENCES `User` (`Username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`CourseNumber`)
    REFERENCES `Course` (`Number`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`Role`)
    REFERENCES `Role` (`Name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `UserCourseUserTopic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserCourseUserTopic`;

CREATE TABLE IF NOT EXISTS `UserCourseUserTopic` (
  `Username` VARCHAR(20) NOT NULL,
  `UserTopicName` VARCHAR(60) NOT NULL,
  `UserCourseNumber` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Username`, `UserTopicName`, `UserCourseNumber`),
  FOREIGN KEY (`Username`)
    REFERENCES `User` (`Username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`UserTopicName`)
    REFERENCES `Topic` (`Name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`Username`, `UserCourseNumber`)
    REFERENCES `UserCourse` (`Username`, `CourseNumber`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `Authority`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Authority` ;

CREATE TABLE IF NOT EXISTS `Authority` (
  `Name` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`Name`));

-- -----------------------------------------------------
-- Table `UserAuthority`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserAuthority` ;

CREATE TABLE IF NOT EXISTS `UserAuthority` (
  `Username` VARCHAR(20) NOT NULL,
  `AuthorityName` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`Username`, `AuthorityName`),
  FOREIGN KEY (`Username`)
    REFERENCES `User` (`Username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (`AuthorityName`)
    REFERENCES `Authority` (`Name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Data for table `Authority`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `Authority` (`Name`) VALUES ('ADMIN');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Role`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `Role` (`Name`) VALUES ('PROFESSOR');
INSERT INTO `Role` (`Name`) VALUES ('TA');
INSERT INTO `Role` (`Name`) VALUES ('STUDENT');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Status`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `Status` (`Name`) VALUES ('SUBMITTED');
INSERT INTO `Status` (`Name`) VALUES ('APPROVED');
INSERT INTO `Status` (`Name`) VALUES ('REJECTED');

COMMIT;

-- -----------------------------------------------------
-- Data for table `User`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `User` (`Username`, `Password`, `Enabled`, `FirstName`, `LastName`, `Email`) VALUES ('admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Gregor', 'Mendel', 'g.mendel@tas.com');
INSERT INTO `User` (`Username`, `Password`, `Enabled`, `FirstName`, `LastName`, `Email`) VALUES ('stan', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Stan', 'Smith', 'axa9070@rit.edu');
INSERT INTO `User` (`Username`, `Password`, `Enabled`, `FirstName`, `LastName`, `Email`) VALUES ('tom', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Tom', 'Haverford', 'axa9070@rit.edu');

COMMIT;

-- -----------------------------------------------------
-- Data for table `UserAuthority`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `UserAuthority` (`Username`, `AuthorityName`) VALUES ('admin', 'ADMIN');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Course`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `Course` (`Number`, `Name`) VALUES ('101-01', 'Introduction to Web Technologies');

COMMIT;


-- -----------------------------------------------------
-- Data for table `UserCourse`
-- -----------------------------------------------------
BEGIN TRANSACTION;
INSERT INTO `UserCourse` (`Username`, `CourseNumber`, `Role`) VALUES ('tom', '101-01', 'TA');
INSERT INTO `UserCourse` (`Username`, `CourseNumber`, `Role`) VALUES ('stan', '101-01', 'STUDENT');

COMMIT;

