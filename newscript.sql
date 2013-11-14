--let’s think about it
--
--limiting under-18 users
--having a filename attribute for users that points to a .jpg file in a filesystem
-- Put an interest attribute for User

--drop all tables
DROP TABLE users CASCADE CONSTRAINTS;
DROP TABLE event CASCADE CONSTRAINTS;
DROP TABLE groups CASCADE CONSTRAINTS;
DROP TABLE membership CASCADE CONSTRAINTS;
DROP TABLE system_admin CASCADE CONSTRAINTS;
DROP TABLE report_user CASCADE CONSTRAINTS;
DROP TABLE report_event CASCADE CONSTRAINTS;
DROP TABLE message CASCADE CONSTRAINTS;
DROP TABLE review_submit CASCADE CONSTRAINTS;
DROP TABLE attends CASCADE CONSTRAINTS;
DROP TABLE invite CASCADE CONSTRAINTS;




--CREATE TABLEs


CREATE TABLE users(
userid number (9,0), 
username varchar2(30), 
upswd varchar2(30), 
uemail varchar2(30), 
uphone varchar2(30), 
unique (username),
PRIMARY KEY (userid)
);
-- users table ok

CREATE TABLE event(
eid number(9,0),
etitle varchar2(300),
edescription varchar2(400), 
startTime DATE, 
end DATE, 
street_address varchar2(100),
building varchar2(100),
userid number(9,0),
PRIMARY KEY (eid),
FOREIGN KEY (userid) REFERENCES users(userid)
);
-- event table ok, changed "DATE" to "DATE" which is an actual datatype in lines 39,40

CREATE TABLE groups(
gid number(9,0),
gname varchar2(30),
description varchar2(300),
since DATE,
manager number(9,0),
PRIMARY KEY (gid),
FOREIGN KEY (manager) REFERENCES users(userid)
);

CREATE TABLE membership(
gid number(9,0),
userid number(9,0),
PRIMARY KEY (gid,userid),
FOREIGN KEY (gid) REFERENCES groups(gid),
FOREIGN KEY (userid) REFERENCES users(userid)
);


CREATE TABLE system_admin(
aid number (9,0),
apswd varchar2(30), 
aemail varchar2(30), 
PRIMARY KEY (aid)
);

CREATE TABLE report_user(
userid1 number(9,0), 
userid2 number(9,0), 
time DATE, 
comments varchar2(300),
PRIMARY KEY (userid1, userid2),
FOREIGN KEY(userid1) REFERENCES users(userid), 
FOREIGN KEY(userid2) REFERENCES users(userid) 
);

CREATE TABLE report_event(
userid number(9,0),
eid number(9,0),
time DATE,
comments varchar2(300),
PRIMARY KEY (userid, eid),
FOREIGN KEY (userid) REFERENCES users(userid),
FOREIGN KEY (eid) REFERENCES event(eid)
);

CREATE TABLE message(
aid number(9,0),
userid number(9,0),
comments varchar2(300),
PRIMARY KEY (aid,userid),
FOREIGN KEY (userid) REFERENCES users(userid),
FOREIGN KEY (aid) REFERENCES system_admin(aid)
);



CREATE TABLE review_submit(
rid number(9,0),
comments varchar2(30),
rating number(9,0),
userid number(9,0),
eid number(9,0),
PRIMARY KEY (rid),
FOREIGN KEY (userid) REFERENCES users(userid),
FOREIGN KEY (eid) REFERENCES event(eid)
);

CREATE TABLE attends(
userid number(9,0),
eid number(9,0),
PRIMARY KEY (userid, eid),
FOREIGN KEY (userid) REFERENCES users(userid),
FOREIGN KEY (eid) REFERENCES event(eid)
);

CREATE TABLE invite(
userid1 number(9,0),
userid2 number(9,0),
eid number(9,0),
PRIMARY KEY (userid1, userid2, eid),
FOREIGN KEY (userid1) REFERENCES users(userid),
FOREIGN KEY (userid2) REFERENCES users(userid),
FOREIGN KEY (eid) REFERENCES event(eid)
);


--data entry

--insert tuples in user

INSERT INTO users VALUES(000000001,'rpottinger','db123db','rpottinger@live.ca','7888049292');
INSERT INTO users VALUES(000000002,'giannacone','sportmaster23','greg.iannacone@gmail.com','6048924502');
INSERT INTO users VALUES(000000003,'gsilvestri','longhairdontcare','gsilver@hotmail.com','7889032120');
INSERT INTO users VALUES(000000004,'dpeterson','oldstewq11','danepet@gmail.com','7888889029');
INSERT INTO users VALUES(000000005,'dmackenzie','tpeo2me','doug.mac@live.ca','2502342342');
-- insert tuples into system_admin

INSERT INTO system_admin VALUES(000000001, 'masterdbadmin102', 'dbmaster@gmail.com');
INSERT INTO system_admin VALUES(000000002, 'youshallnotpass20', 'sportstar202@gmail.com');
INSERT INTO system_admin VALUES(000000003, '101tlemeire2', 'sysadmindbs@gmail.com');
INSERT INTO system_admin VALUES(000000004, 'l1l1l1e0r', 'helpfulboom@gmail.com');
INSERT INTO system_admin VALUES(000000005, '12345oooo9', 'susie.crabgrass@hotmail.ca');

--insert tuples into event

INSERT INTO event VALUES(000000001, 'Computation And Sustainability: Beyond Green IT - FLS Talk By Alan Mackworth, UBC/CS', 
'Alan Mackworth, Professor and Canada Research Chair in AI, UBC Computer Science', 
TO_DATE('30-AUG-13 3:30PM', 'DD-MON-YY HH:MIPM'), 
TO_DATE('30-AUG-13 6:30PM','DD-MON-YY HH:MIPM'), 
'110-6245 Agronomy Rd.', 
'Hugh Dempster Pavilion', 000000001);

INSERT INTO event VALUES(000000002, 'UBC FilmSoc Wicker Man Beer Garden', 
'Wonder why weve been counting down to Cagemas? Well, for our beer garden of course.', 
TO_DATE('13-FEB-13 12:00AM','DD-MON-YY HH:MIAM'), 
TO_DATE('13-FEB-13 1:30PM','DD-MON-YY HH:MIPM'), 
'130-6138 Student Union Blvd', 
'Student Union Building', 000000003);

INSERT INTO event VALUES(000000003, 'Career Fair 2014', 'Join us for the chance to interface with your future masters!', 
TO_DATE('24-SEP-13 1:30PM','DD-MON-YY HH:MIPM'),
TO_DATE('24-SEP-13 5:30PM','DD-MON-YY HH:MIPM'), 
'1137 Alumni Ave',
'Life Sciences Building', 000000001);

INSERT INTO event VALUES(000000004, 'COGS Meet the Profs!', 'Join us for a chance to try to network with your fellow students and professors!', 
TO_DATE('04-OCT-13 10:30AM','DD-MON-YY HH:MIAM'), 
TO_DATE('04-OCT-13 11:30AM','DD-MON-YY HH:MIAM'), 
'6371 Crescent Rd', 
'Leo and Thea Koerner Graduate Student Centre', 000000002);

INSERT INTO event VALUES(000000005, 'CPSC 304 group meeting', 'Doug, Dane, Greg, and Gaia are meeting to work on project', 
TO_DATE('02-NOV-13 6:30PM','DD-MON-YY HH:MIPM'), 
TO_DATE('02-NOV-13 8:30PM','DD-MON-YY HH:MIPM'), 
'2366 Main Mall', 
'ICICS', 000000004);


-- insert tuples into attends
INSERT INTO attends VALUES(000000001, 000000001);
INSERT INTO attends VALUES(000000002, 000000002);
INSERT INTO attends VALUES(000000002, 000000001);
INSERT INTO attends VALUES(000000004, 000000003);
INSERT INTO attends VALUES(000000004, 000000002);
INSERT INTO attends VALUES(000000005, 000000003);

--insert tuples into report_event

INSERT INTO report_event VALUES(00000001, 00000001, TO_DATE('05-OCT-13 10:30AM','DD-MON-YY HH:MIAM'), 'This event offended me with its use of profanity');
INSERT INTO report_event VALUES(00000002, 00000001, TO_DATE('04-OCT-13 11:30AM','DD-MON-YY HH:MIAM'), 'The burgers were undercooked');
INSERT INTO report_event VALUES(00000002, 00000002, TO_DATE('25-SEP-13 11:30AM','DD-MON-YY HH:MIAM'), 'This event was not related to databases');
INSERT INTO report_event VALUES(00000004, 00000002, TO_DATE('13-NOV-13 1:30PM','DD-MON-YY HH:MIPM'), 'I was cold this whole time');
INSERT INTO report_event VALUES(00000004, 00000003, TO_DATE('04-AUG-13 9:30AM','DD-MON-YY HH:MIAM'), 'I felt like the floor was going to cave in');

--  first, add in the groups

INSERT INTO groups VALUES(100000000,'Origami Lovers','A groups for anyone who loves folding paper',
TO_DATE('23-OCT-12 10:30AM','DD-MON-YY HH:MIAM'),
000000003);
INSERT INTO groups VALUES(200000000,'Basketball Rec','Rec team for amateur basketball games',
TO_DATE('05-JAN-11 10:30AM','DD-MON-YY HH:MIAM'),
000000003);
INSERT INTO groups VALUES(300000000,'Cooking club','We meet once a week and make awesome food',
TO_DATE('23-MAR-09 8:30AM','DD-MON-YY HH:MIAM'),
000000001);
INSERT INTO groups VALUES(400000000,'Graduating class of 2014','A group for all students graduating in 2014',
TO_DATE('25-APR-11 10:30AM','DD-MON-YY HH:MIAM'),
000000005);
INSERT INTO groups VALUES(500000000,'Chess club','We play chess every friday night of the week',
TO_DATE('17-DEC-13 10:30AM','DD-MON-YY HH:MIAM'),
000000005);

-- then add in the memberships

INSERT INTO membership VALUES(400000000,000000003);
INSERT INTO membership VALUES(300000000,000000003);
INSERT INTO membership VALUES(200000000,000000002);
INSERT INTO membership VALUES(500000000,000000005);
INSERT INTO membership VALUES(100000000,000000001);


-- then add in the messages

INSERT INTO message VALUES(000000001,000000003,'We changed the font of our application, have fun looking at some Arial');
INSERT INTO message VALUES(000000001,000000001,'We changed the font of our application, have fun looking at some Arial');	
INSERT INTO message VALUES(000000001,000000004,'We changed the font of our application, have fun looking at some Arial');
INSERT INTO message VALUES(000000001,000000002,'We changed the font of our application, have fun looking at some Arial');
INSERT INTO message VALUES(000000001,000000005,'We changed the font of our application, have fun looking at some Arial');

-- when displaying tables type: 1. set lines 256 2. set trimout on 3. set space 1 4. set tab off