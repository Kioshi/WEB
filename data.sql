/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.7.11 : Database - deskovky
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `clenove` */

INSERT  INTO `clenove`(`id`,`jmeno`,`prezdivka`,`img`,`aktivni`,`userName`,`passHash`,`session`,`role`) VALUES (1,'tester','','http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png',1,'tester','$2y$10$t/rqJ6B/uTjwSvkHUgzqBe5.GaEkJhWaepEAAgNfMrjjrRRkf7ZTu',NULL,2),(2,'user','user si','http://www.zatrolene-hry.cz/new.graphics/avatar-anonymous.png',1,'test','$2y$10$OWUO7xXnvLQ9pORMWF5Yz.e8KmcqRdUjQZ7lv0mj1EtLqYDd8DI7S','6e4a554e-bf27-11e6-839b-408d5cb0a346',1),(3,'asdasd','','http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png',1,'test1','$2y$10$5sJ36XowGyT.Kev2usxyG.H7WJvPnTTTPPXm4xoxW4na0V.iLhHg.',NULL,1),(4,'1','','http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png',1,'test2','$2y$10$U0/jp6/sg3StTbum0UJvM.0nk62ywf9QxL8ZzLKW1Kx9HWcpBximW',NULL,1),(5,'3','','http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png',1,'test3','$2y$10$FieU.ZqobQizcVNJYhnSRubXCqgbZUyRZB6FRsV6/M9dPAZ1YJjEK',NULL,1),(6,'4','','http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png',1,'test4','$2y$10$KD0HQZ4rN2gl/fCsD0HIheZiQG3/ZMCcwM2s9Jid7nzhymus23cpy',NULL,1),(7,'5','','http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png',1,'test5','$2y$10$jGfxv7RWGnBbjIIA29LaWuVQyhQfOIoQOMPFmCdEk66ZmaEBXTJj2',NULL,1);

/*Data for the table `hral` */

/*Data for the table `hry` */

INSERT  INTO `hry`(`id`,`nazev`,`alternativniNazev`,`cena`,`datumPorizeni`,`zpusob`,`pozn`,`link`,`img`,`hernidoba`,`skrine`,`minPocet`,`maxPocet`) VALUES (1,'asda','',0,'2016-12-19 19:21:44','',NULL,'','http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png',14425,1,1,1),(2,'test','',0,'2016-12-10 19:47:06','',NULL,'','http://www.zatrolene-hry.cz/galerie/108/main.large.jpg',1,1,1,1),(3,'sadaf','',0,'2016-12-10 19:47:34','',NULL,'','http://www.zatrolene-hry.cz/galerie/6151/main.large.jpg',1,1,1,1),(4,'asd','',0,'2030-11-20 16:00:00','',NULL,'','http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png',1,1,1,1),(5,'asdfaf','',0,'2014-12-20 16:00:00','',NULL,'','http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png',1,1,1,1),(6,'asdfaf','',0,'2016-12-14 00:00:00','',NULL,'','http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png',1,1,1,1),(7,'sadsad','',0,'2016-12-31 00:00:00','',NULL,'','http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png',1,1,1,1);

/*Data for the table `role` */

INSERT  INTO `role`(`id`,`role`) VALUES (1,'CLEN'),(2,'ADMIN');

/*Data for the table `skrine` */

INSERT  INTO `skrine`(`id`,`skrin`) VALUES (1,'as');

/*Data for the table `vypujcky` */

INSERT  INTO `vypujcky`(`id`,`clenove_id`,`hry_id`,`datumPujceni`,`datumVraceni`,`pozn`) VALUES (1,1,1,'2017-11-11','2017-12-11','jbhjk'),(2,1,2,'2014-12-20','2029-12-20','asdasd'),(3,1,5,'2016-12-13','2016-12-24','');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
