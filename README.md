# Crypto API

Для использования требуется авторизационный токен:  
`oFhN_7wC7RhgXW3jkpi-re2nWyI_EdhoM3oqtk-5vnO20maaQu-8Hhr-e1JaQHV`

*Комиссия на операции = 2%*

2 метода:  
`/v1?method=rates&currency=<rate>`
> Без указания `currency` выводит все обменные курсы, иначе только указанный

`/v1?method=convert&currency_from=<rate>&currency_to=<rate>&value=<float number > 0.01>`
> Запрос на обмен валюты c учетом комиссии

Пример запроса

```
curl --location 'http://localhost/api/v1?method=convert&currency_from=btc&currency_to=usd&value=.01' \
--header 'Authorization: Bearer oFhN_7wC7RhgXW3jkpi-re2nWyI_EdhoM3oqtk-5vnO20maaQu-8Hhr-e1JaQHV'
```

Результат

```json
{
  "status": "success",
  "data": {
    "currency_from": "BTC",
    "currency_to": "USD",
    "value": 0.01,
    "converted_value": 255.56,
    "rate": 25556.304
  }
}
```