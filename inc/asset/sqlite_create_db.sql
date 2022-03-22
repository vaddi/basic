
DROP TABLE IF EXISTS visitors;

CREATE TABLE visitors (
  id        INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  ip        TEXT NOT NULL,
  url	      TEXT NOT NULL,
  timestamp TEXT NULL,
  useragent TEXT NULL,
  platform  TEXT NULL,
  referer   TEXT NULL,
  hits      INTEGER NOT NULL
);

INSERT INTO visitors VALUES (0,	'::1', 'index.php', '2022-03-22 18:30:00', '', '', '', 1);

