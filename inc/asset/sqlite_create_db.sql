
DROP TABLE IF EXISTS visitors;

CREATE TABLE visitors (
  id         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  ip         TEXT NOT NULL,
  url	       TEXT NOT NULL,
  timestamp  TEXT NULL,
  useragent  TEXT NULL,
  platform   TEXT NULL,
  referer    TEXT NULL,
  hits       INTEGER NOT NULL,
  hostname   TEXT NULL,
  rendertime TEXT NULL
);

INSERT INTO visitors VALUES (0,	'127.0.0.1', '/index.php', '2022-03-27 18:30:00', 'Firefox xx.x', 'macos', '/', 1, 'localhost', '0.1234');

