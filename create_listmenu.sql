DROP DATABASE IF EXISTS ListMenuDb;
CREATE DATABASE ListMenuDb;
USE ListMenuDb;

DROP TABLE IF EXISTS ListItems;
CREATE TABLE ListItems(
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    alias VARCHAR(20) NOT NULL,
    parent_id INT,
    FOREIGN KEY (parent_id) REFERENCES ListItems(id)
);

INSERT INTO ListItems VALUES (1, 'Пользователи', 'users', NULL);
INSERT INTO ListItems VALUES (2, 'Создание', 'create', 1);
INSERT INTO ListItems VALUES (3, 'Список', 'list', 1);
INSERT INTO ListItems VALUES (4, 'Активные', 'active', 3);
INSERT INTO ListItems VALUES (5, 'Удаленные', 'deleted', 3);
INSERT INTO ListItems VALUES (8, 'Поиск', 'search', 1);
INSERT INTO ListItems VALUES (6, 'Заявки', 'requests', NULL);
INSERT INTO ListItems VALUES (9, 'Заявки на поключение', 'connecting', 6);
INSERT INTO ListItems VALUES (10, 'Заявки на ремонт', 'repairs', 6);
INSERT INTO ListItems VALUES (11, 'Заявки на обход', 'round', 6);
INSERT INTO ListItems VALUES (7, 'Отчёты', 'reports', NULL);
INSERT INTO ListItems VALUES (12, 'Отдел маркетинга', 'marketing', 7);
INSERT INTO ListItems VALUES (15, 'Отчёт по списаниям', 'write-offs', 12);
INSERT INTO ListItems VALUES (16, 'Отчёт по расходам', 'costs', 12);
INSERT INTO ListItems VALUES (17, 'Годовой отчёт', 'year', 12);
INSERT INTO ListItems VALUES (14, 'Управление', 'control', 7);
