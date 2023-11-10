-- Active: 1670833551932@@127.0.0.1@3306@bdd-palais3


SELECT *
FROM users
JOIN calendar ON users.id = calendar.name_id;

SELECT *
FROM users
Join calendar ON users.id = calendar.name_id
WHERE users.id = 9;

SELECT *
FROM dishes
JOIN menu ON menu.id = dishes.id;

SELECT *
FROM calendar
ORDER BY start ASC;