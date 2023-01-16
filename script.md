# Scripts For RBDS

## Obsah
ToDo

## Funkce poceč zanrů
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `funkce_pocet_zanru`(`genrename` VARCHAR(45)) RETURNS int(11)
    NO SQL
BEGIN

DECLARE pocet INT;

SELECT COUNT(genre.name) INTO pocet FROM genre
INNER JOIN book_has_genre ON book_has_genre.genre_id = genre.id
INNER JOIN book ON book.id = book_has_genre.book_id
WHERE genre.name = genrename;

RETURN pocet;

END$$
DELIMITER ;
```
## Transakce přidej x kč
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Transakce_přidej_x_kc`(IN `parID` INT, IN `addPrice` INT)
    NO SQL
BEGIN
START TRANSACTION;
select id into @a from book where book.id = parID;

IF (@a = parID) then
   UPDATE book SET 
       price = price + addPrice
       WHERE book.id = parID;
   COMMIT;
   Select CONCAT("Přidali jsem ",addPrice ,"kč!") as "info";
Else 
    ROLLBACK;
    Select "Tato knihas není v naší databázi!" as "info";
END IF;
END$$
DELIMITER ;
```
## Index book name
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `index_book_name`()
    NO SQL
BEGIN

CREATE FULLTEXT INDEX book_name_index
ON book (name);

END$$
DELIMITER ;
```
## Pohled na fantasy knozky
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `pohled_na_fantasy_knizky`()
    NO SQL
CREATE OR REPLACE VIEW pohled AS
SELECT bk.name as "knižka", bk.pages, bk.language, CONCAT_WS(" ", au.f_name, au.l_name) as "jmeno", au.country, a.name as "žánr"
FROM book bk
INNER JOIN book_has_author bkr
ON bk.id = bkr.book_id
INNER JOIN author au
ON au.id = bkr.author_id
INNER JOIN book_has_genre bkg
on bk.id = bkg.book_id
RIGHT JOIN (SELECT * FROM genre g WHERE (g.name = "Fantasy")) a ON a.id = bkg.genre_id$$
DELIMITER ;
```
## Proceuda book name calculator
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `procedura_book_name_curaor`(OUT `nameList` VARCHAR(21000))
    NO SQL
BEGIN
    DECLARE konec INTEGER DEFAULT 0;
    DECLARE bookName varchar(45) DEFAULT "";
    DECLARE curName
        CURSOR FOR
            SELECT name FROM book;

    DECLARE CONTINUE HANDLER
        FOR NOT FOUND SET konec = 1;

    OPEN curName;

    getName: LOOP
        FETCH curName INTO bookName;
        IF konec = 1 THEN
            LEAVE getName;
        END IF;
         SET nameList = CONCAT_WS(", ",bookName,nameList);
    END LOOP getName;
    CLOSE curName;

END$$
DELIMITER ;
```
## Select analiticka funkce
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_analiticka_funkce`()
    NO SQL
SELECT
AVG(book.pages) as "průmer stránek v knížkách"
FROM book$$
DELIMITER ;
select avg tabulek
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_avg_tabulek`()
    NO SQL
SELECT (
COUNT(*) +
(SELECT COUNT(*) FROM book) +
(SELECT COUNT(*) FROM book_has_author) +
(SELECT COUNT(*) FROM book_has_genre) +
(SELECT COUNT(*) FROM book_has_reservation) +
(SELECT COUNT(*) FROM genre) +
(SELECT COUNT(*) FROM rating) +
(SELECT COUNT(*) FROM reservation) +
(SELECT COUNT(*) FROM room) +
(SELECT COUNT(*) FROM triggers_room) +
(SELECT COUNT(*) FROM user)
)/11 as "Počet řádků na tabulku"
FROM author$$
DELIMITER ;
```
## Select join
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_join`()
    NO SQL
SELECT book.name as "název knižky", reservation.taken as "je vyzvednuta" FROM `reservation`
INNER JOIN book_has_reservation ON reservation.id = book_has_reservation.reservation_id
INNER JOIN book ON book.id = book_has_reservation.book_id
WHERE `e-reservation` >= CURRENT_DATE() AND `s-reservation` <= CURRENT_DATE()$$
DELIMITER ;
```
## Select select
```
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_select`()
    NO SQL
SELECT bk.name as "knižka", bk.pages, bk.language, CONCAT_WS(" ", au.f_name, au.l_name) as "jmeno", au.country, a.name as "žánr"
FROM book bk
INNER JOIN book_has_author bkr
ON bk.id = bkr.book_id
INNER JOIN author au
ON au.id = bkr.author_id
INNER JOIN book_has_genre bkg
on bk.id = bkg.book_id
RIGHT JOIN (SELECT * FROM genre g WHERE (g.name = "Fantasy")) a ON a.id = bkg.genre_id$$
DELIMITER ;
```
## Lock/Unlock
```
LOCK TABLE book READ;
```
```
UNLOCK TABLES;
```

## User
### Vytvoří uživatele
```
CREATE USER 'sammy'@'localhost' IDENTIFIED BY 'password';
```
### Smaže uživatele
```
DROP USER user1 [, userNames]......;
```
### Aktualizuje záznam v paměti (zprovozní upravené věci s uživateli)
```
FLUSH PRIVILEGES;
```

### Dá všechny práva uživateli...
```
GRANT SELECT ON *.* TO <username>;
```
### Dá select, update... pro dbname.vše .. pro uživatele@přístup
```
GRANT SELECT, UPDATE, INSERT ON dbname.* TO rfc@localhost;
```
### Odebere práva
```
REVOKE INSERT, UPDATE ON classicmodels.* FROM rfc@localhost;
```
### Ukáže práva pro daného uživatele
```
SHOW GRANTS FOR <username>@<host>;
```
### Pro všechny
```
SHOW GRANTS;
```
