USE test;

DROP TABLE IF EXISTS client;

CREATE TABLE client (
  client_id int(11) unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL default '',
  address varchar(100) NOT NULL default '',
  category varchar(20) NOT NULL default '',
  active smallint(1) NOT NULL default '1',
  PRIMARY KEY  (client_id)
) TYPE=MyISAM COMMENT='Clients';

INSERT INTO client VALUES (1,'Foo','5th Avenue, 125','Master',1);
INSERT INTO client VALUES (6,'Bar','4th Avenue, 333','Common',1);
INSERT INTO client VALUES (7,'Baz','Wall Street, 2344','Master',1);
INSERT INTO client VALUES (8,'John','Beer Street, 1201','Common',1);
INSERT INTO client VALUES (9,'Mary','Sea Street, 25','Master',1);
INSERT INTO client VALUES (10,'Adam','France Avenue, 1445','Common',1);
INSERT INTO client VALUES (11,'Paul','Brazil Avenue, 3402','Master',1);
INSERT INTO client VALUES (12,'Richard','Mexico Avenue, 1225 room 4','Common',1);
INSERT INTO client VALUES (13,'Harry','France Street, 2049','Common',1);
INSERT INTO client VALUES (14,'Bill','Microsoft Avenue, 3144','Master',1);
INSERT INTO client VALUES (15,'Jennifer','Cuba Street, 21','Common',1);
INSERT INTO client VALUES (16,'Daisy','Flower Avenue, 114','Master',1);
INSERT INTO client VALUES (17,'Patrick','England Street, 814 room 201','Common',1);
INSERT INTO client VALUES (18,'Claudia','Spain Avenue, 1223','Master',1);
INSERT INTO client VALUES (19,'Anna','China Street, 3211','Common',1);
INSERT INTO client VALUES (20,'Christina','Japan Avenue, 15','Master',1);
INSERT INTO client VALUES (21,'David','Church Street, 866','Common',1);
INSERT INTO client VALUES (22,'Joe','Docks Street, 1429 room 3','Master',1);
INSERT INTO client VALUES (23,'Erick','School Road','Master',1);
INSERT INTO client VALUES (24,'Gary','Third Avenue','Common',1);
INSERT INTO client VALUES (25,'Sarah','Italy Avenue','Master',1);
INSERT INTO client VALUES (26,'Joseph','Fifth Street','Common',1);
INSERT INTO client VALUES (27,'James','Chester Avenue','Common',1);
INSERT INTO client VALUES (28,'Phillip','Lake Road','Master',1);
INSERT INTO client VALUES (29,'Francis','Old Country Road, 455','Common',1);
INSERT INTO client VALUES (30,'Martin','Autumn Place, 2010','Common',1);
INSERT INTO client VALUES (31,'Kim','Russia Street, 1877','Common',1);
INSERT INTO client VALUES (32,'Sandra','Central Street, 631','Master',1);
INSERT INTO client VALUES (33,'Nelson','P.O. Box 211','Master',1);
INSERT INTO client VALUES (34,'Meredith','P.O. Box 290','Common',1);
INSERT INTO client VALUES (35,'Susan','P.O. Box 390','Common',1);
INSERT INTO client VALUES (36,'Todd','P.O. Box 34','Common',1);
INSERT INTO client VALUES (37,'Randy','P.O. Box 101','Common',1);
INSERT INTO client VALUES (38,'Judith','P.O. Box 94','Common',1);
INSERT INTO client VALUES (39,'Laura','P.O. Box 345','Master',1);
INSERT INTO client VALUES (40,'Kathleen','P.O. Box 65','Master',1);
INSERT INTO client VALUES (41,'Michael','St. Paul Street','Master',1);
INSERT INTO client VALUES (42,'Deborah','St. Mary Street','Master',1);
INSERT INTO client VALUES (43,'Catherine','4th Avenue','Master',1);
INSERT INTO client VALUES (44,'Scott','4th Avenue','Master',1);
INSERT INTO client VALUES (45,'Pamela','5th Avenue','Master',1);
INSERT INTO client VALUES (46,'Nick','5th Avenue','Common',1);
INSERT INTO client VALUES (47,'Mike','P.O. Box 567','Common',1);
INSERT INTO client VALUES (48,'Cheryl','P.O. Box 44','Common',1);
INSERT INTO client VALUES (49,'Victoria','Lake Road','Master',1);
INSERT INTO client VALUES (50,'Ronald','6th Avenue, 1277','Master',1);
INSERT INTO client VALUES (51,'Bob','2nd Avenue, 443','Master',1);
INSERT INTO client VALUES (52,'Margaret','1st Avenue, 699','Master',1);
INSERT INTO client VALUES (53,'Glenda','P.O. Box 141','Master',1);
INSERT INTO client VALUES (54,'Ken','Sloan Place, 363','Common',1);
INSERT INTO client VALUES (55,'Barbara','Finland Street, 611','Common',1);
INSERT INTO client VALUES (56,'Thomas','Industrial Drive, 992','Common',1);
INSERT INTO client VALUES (57,'Kathy','School Road, 1089 room 14','Common',1);
INSERT INTO client VALUES (58,'Patricia','Exchange Street, 2244','Common',1);
INSERT INTO client VALUES (59,'Michelle','P.O. Box 55','Common',1);
INSERT INTO client VALUES (60,'Karen','Park Avenue, 11','Common',1);
INSERT INTO client VALUES (61,'Woody','Sea Avenue, 88','Master',1);
INSERT INTO client VALUES (62,'Fred','Canada Avenue, 547','Common',1);
INSERT INTO client VALUES (63,'Natalie','North Highway, 4555','Common',1);
INSERT INTO client VALUES (64,'Patricia','South Highway, 1558','Master',1);
INSERT INTO client VALUES (65,'Adrienne','3rd Avenue, 3177','Master',1);
INSERT INTO client VALUES (66,'Brian','P.O. Box 14','Master',1);
INSERT INTO client VALUES (67,'Anthony','Clouds Street','Common',1);
INSERT INTO client VALUES (68,'Paula','President Avenue','Master',1);
INSERT INTO client VALUES (69,'Thomas','Wall Street, 3444','Common',1);
INSERT INTO client VALUES (70,'Angela','3rd Avenue, 1099','Common',1);
INSERT INTO client VALUES (71,'Melissa','Brazil Avenue, 4455','Master',1);
INSERT INTO client VALUES (72,'Stephen','Mexico Avenue, 3441','Common',1);
INSERT INTO client VALUES (73,'Mike','United States Street, 765','Master',1);
INSERT INTO client VALUES (74,'Bart','Park Avenue, 1000 room 10','Master',1);