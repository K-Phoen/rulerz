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
  birthday DATE NOT NULL,
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

INSERT INTO players (pseudo, group_id, fullname, gender, birthday, points, address_street, address_postalCode, address_city, address_country) VALUES
('Joe',       1, 'Joe la frite',      'M', '1924-03-02',  2500, 'Baker street', 'NW1 6XE', 'London', 'England'),
('Bob',       2, 'Bob Morane',        'M', '1995-10-02',  9001, 'Baker street', 'NW1 6XE', 'London', 'England'),
('Ada',       2, 'Ada Lovelace',      'F', '1997-10-02', 10000, 'Baker street', 'NW1 6XE', 'London', 'England'),
('Kévin',     2, 'Yup, that is me.',  'M', '1999-10-02',  100, 'Baker street', 'NW1 6XE', 'London', 'England'),
('Margaret',  2, 'Margaret Hamilton', 'F', '1936-08-17',  5000, 'Some street', '47454', 'Paoli', 'United-States'),
('Alice',     1, 'Alice Foo',         'F', '2001-10-02',  175, 'Baker street', 'NW1 6XE', 'London', 'England'),
('Louise',    1, 'Louise Foo',        'F', '2002-10-02',  800, 'Other street', 'NW1 6XE', 'London', 'England'),
('Francis',   1, 'Francis Foo',       'M', '1998-10-02',  345, 'Random street', 'NW1 6XE', 'London', 'England'),
('John',      1, 'John Foo',          'M', '1987-10-02',  23,  'Doe street', 'NW1 6XE', 'London', 'England'),
('Arthur',    1, 'Arthur Foo',        'M', '1989-10-02',  200, 'Round table street', 'NW1 6XE', 'London', 'England'),
('Moon Moon', 1, 'Moon moon Foo',     'D', '1985-10-02',   300, 'Moon moon street', 'NW1 6XE', 'London', 'England');


select *
from players p
left join groups g on g.id = p.group_id
left join roles r on r.id = g.role_id;
