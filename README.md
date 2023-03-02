# CRYPTO API

Для использования требуется авторизационный токен:  
**oFhN_7wC7RhgXW3jkpi-re2nWyI_EdhoM3oqtk-5vnO20maaQu-8Hhr-e1JaQHV**  

*комиссия на операции = 2%*  

2 метода:  
    `/v1?method=rates&<currency>=<rate>`  
> без указания <currency> выводит все обменные курсы, иначе только указанный;   

`/v1?method=convert&<currency_from>=<rate>&<currency_to>=<rate>&<value>=<float number > 0.01>` 
> запрос на обмен валюты c учетом комиссии
