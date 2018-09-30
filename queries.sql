INSERT INTO categories
SET name = 'Доски и лыжи';
INSERT INTO categories
SET name = 'Крепления';
INSERT INTO categories
SET name = 'Ботинки';
INSERT INTO categories
SET name = 'Одежда';
INSERT INTO categories
SET name = 'Инструменты';
INSERT INTO categories
SET name = 'Разное';

INSERT INTO users
SET registration_date = timestamp('20180901'), email = 'vasya@gmail.com', name = 'Василий', password = 'qwerty',
  avatar_path         = 'img/avatar.png', contacts = '8-999-123-45-67';
INSERT INTO users
SET registration_date = timestamp('20180723'), email = 'nikita@gmail.com', name = 'Никита', password = '123456',
  avatar_path         = 'img/avatar.png', contacts = '8-999-987-65-43';
INSERT INTO users
SET registration_date = timestamp('20180725'), email = 'petya@gmail.com', name = 'Петр', password = 'password',
  avatar_path         = 'img/avatar.png', contacts = '8-999-987-56-43';

INSERT INTO lots
SET name          = '2014 Rossignol District Snowboard', description = 'Сноуборд', img_path = 'img/lot-1.jpg',
  start_price     = 10999, bet_step = 100, creation_date = timestamp('20180910'),
  expiration_date = timestamp('20181031'), author = 1, winner = NULL, category = 1;
INSERT INTO lots
SET name          = 'DC Ply Mens 2016/2017 Snowboard', description = 'Сноуборд', img_path = 'img/lot-2.jpg',
  start_price     = 159999, bet_step = 1000, creation_date = timestamp('20180902'),
  expiration_date = timestamp('20181020'), author = 2, winner = NULL, category = 1;
INSERT INTO lots
SET name          = 'Крепления Union Contact Pro 2015 года размер L/XL', description = 'Крепления',
  img_path        = 'img/lot-3.jpg', start_price = 8000, bet_step = 50, creation_date = timestamp('20180810'),
  expiration_date = timestamp('20180901'), author = 1, winner = NULL, category = 2;
INSERT INTO lots
SET name          = 'Ботинки для сноуборда DC Mutiny Charocal', description = 'Ботинки', img_path = 'img/lot-4.jpg',
  start_price     = 10999, bet_step = 105, creation_date = timestamp('20180920'),
  expiration_date = timestamp('20181020'), author = 2, winner = NULL, category = 3;
INSERT INTO lots
SET name          = 'Куртка для сноуборда DC Mutiny Charocal', description = 'Куртка', img_path = 'img/lot-5.jpg',
  start_price     = 7500, bet_step = 140, creation_date = timestamp('20180710'),
  expiration_date = timestamp('20181030'), author = 1, winner = NULL, category = 4;
INSERT INTO lots
SET name   = 'Маска Oakley Canopy', description = 'Маска', img_path = 'img/lot-6.jpg', start_price = 5400,
  bet_step = 25, creation_date = timestamp('20180903'), expiration_date = timestamp('20180930'),
  author   = 2, winner = NULL, category = 6;

INSERT INTO bets
SET bet = 11999, date = timestamp('20180910'), author = 2, lot = 1;
INSERT INTO bets
SET bet = 12999, date = timestamp('20180911'), author = 3, lot = 1;
INSERT INTO bets
SET bet = 200000, date = timestamp('20180910'), author = 1, lot = 2;

# 1. получить все категории
SELECT name
FROM categories;

# 2. получить самые новые, открытые лоты. Каждый лот должен включать название,
#    стартовую цену, ссылку на изображение, цену, количество ставок, название категории
SELECT
  l.id,
  l.name,
  l.start_price,
  l.img_path,
  MAX(b.bet)   AS current_price,
  COUNT(b.bet) AS bet_counter,
  c.name       AS category
FROM lots l
  JOIN categories c ON l.category = c.id
  LEFT JOIN bets b ON l.id = b.lot
WHERE l.expiration_date > now()
GROUP BY l.id
ORDER BY l.creation_date DESC
LIMIT 9;

# 3. показать лот по его id. Получите также название категории, к которой принадлежит лот
SELECT
  l.name,
  c.name
FROM lots l
  JOIN categories c ON l.category = c.id
WHERE l.id = 3;

# 4. обновить название лота по его идентификатору
UPDATE lots
SET name = 'New name'
WHERE id = 7;

# 5. получить список самых свежих ставок для лота по его идентификатору
SELECT b.bet
FROM bets b
WHERE b.lot = 1
ORDER BY date DESC;
