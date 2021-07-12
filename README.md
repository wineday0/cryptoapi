# CRYPTO API

> Доброго времени суток!


Запрос:

SELECT users.id AS ID, CONCAT_WS(' ', users.first_name, users.last_name) AS Name,  
books.author AS Author, GROUP_CONCAT(books.name ORDER BY books.name SEPARATOR ', ') AS Books  
FROM  bi_reports.user_books AS owner  
 INNER JOIN bi_reports.users AS users  
 ON owner.user_id = users.id   
 INNER JOIN bi_reports.books AS books  
 ON owner.book_id = books.id  
WHERE users.age BETWEEN 7 AND 17  
GROUP BY users.id  
HAVING count(books.name) < 3 AND count(DISTINCT books.author) = 1  
ORDER BY users.id  

API:  

Для использования требуется авторизационный токен:  
**oFhN_7wC7RhgXW3jkpi-re2nWyI_EdhoM3oqtk-5vnO20maaQu-8Hhr-e1JaQHV**  

*комиссия на операции = 2%*  

2 метода:  
    `/v1?method=rates&<currency>=<rate>`  
> без указания <currency> выводит все обменные курсы, иначе только указанный;   

`/v1?method=convert&<currency_from>=<rate>&<currency_to>=<rate>&<value>=<float number > 0.01>` 
> запрос на обмен валюты c учетом комиссии
