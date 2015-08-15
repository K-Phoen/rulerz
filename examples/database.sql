CREATE TABLE players(
  pseudo   VARCHAR(50) PRIMARY KEY NOT NULL,
  fullname VARCHAR(50) NOT NULL,
  gender   CHAR(1) NOT NULL,
  age      UNSIGNED TINYINT NOT NULL,
  points   UNSIGNED TINYINT NOT NULL
);

INSERT INTO players (pseudo, fullname, gender, age, points) VALUES
('Joe',      'Joe la frite',      'M', 34,  2500),
('Bob',      'Bob Morane',        'M', 62,  9001),
('Ada',      'Ada Lovelace',      'F', 175, 10000),
('Kévin',    'Yup, that is me.',  'M', 24,  100),
('Margaret', 'Margaret Hamilton', 'F', 78,  5000),
('Alice',    'Alice Foo',         'F', 30,  175)
;


select * from players;