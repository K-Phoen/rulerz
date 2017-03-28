DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
  id INTEGER PRIMARY KEY NOT NULL,
  name TEXT NOT NULL
);

DROP TABLE IF EXISTS groups;
CREATE TABLE groups (
  id INTEGER PRIMARY KEY NOT NULL,
  role_id INTEGER DEFAULT NULL,
  name TEXT NOT NULL,
  CONSTRAINT FK_F06D3970D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
);

DROP TABLE IF EXISTS players;
CREATE TABLE players(
  pseudo   VARCHAR(50) PRIMARY KEY NOT NULL,
  group_id INTEGER DEFAULT NULL,
  fullname VARCHAR(50) NOT NULL,
  gender   CHAR(1) NOT NULL,
  age      INTEGER NOT NULL,
  points   INTEGER NOT NULL,
  address_street VARCHAR(255) NOT NULL,
  address_postalCode VARCHAR(255) NOT NULL,
  address_city VARCHAR(255) NOT NULL,
  address_country VARCHAR(255) NOT NULL,
  CONSTRAINT FK_264E43A6FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)
);

CREATE INDEX IDX_264E43A6FE54D947 ON players (group_id);
CREATE INDEX IDX_F06D3970D60322AC ON groups (role_id);

INSERT INTO roles (id, name) VALUES (1, 'ROLE_ADMIN');
INSERT INTO roles (id, name) VALUES (2, 'ROLE_PLAYER');

INSERT INTO groups (id, role_id, name) VALUES (1, 1, 'Admin');
INSERT INTO groups (id, role_id, name) VALUES (2, 2, 'Players');

INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Joe',       1, 'Joe la frite',      'M', 34,  2500, 'Baker street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Bob',       2, 'Bob Morane',        'M', 62,  9001, 'Baker street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Ada',       2, 'Ada Lovelace',      'F', 175, 10000, 'Baker street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Kévin',     2, 'Yup, that is me.',  'M', 24,  100, 'Baker street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Margaret',  2, 'Margaret Hamilton', 'F', 78,  5000, 'Some street', '47454', 'Paoli', 'United-States');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Alice',     1, 'Alice Foo',         'F', 30,  175, 'Baker street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Louise',    1, 'Louise Foo',        'F', 32,  800, 'Other street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Francis',   1, 'Francis Foo',       'M', 30,  345, 'Random street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('John',      1, 'John Foo',          'M', 40,  23,  'Doe street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Arthur',    1, 'Arthur Foo',        'M', 25,  200, 'Round table street', 'NW1 6XE', 'London', 'England');
INSERT INTO players (pseudo, group_id, fullname, gender, age, points, address_street, address_postalCode, address_city, address_country) VALUES ('Moon Moon', 1, 'Moon moon Foo',     'D', 7,   300, 'Moon moon street', 'NW1 6XE', 'London', 'England');


select *
from players p
left join groups g on g.id = p.group_id
left join roles r on r.id = g.role_id;
