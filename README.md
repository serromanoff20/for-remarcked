# testing task for ReMarked

Для развёртывания проекта на локальной машине, необходимо заполнить .env файл, например:

DB_HOST='localhost' \
DB_PORT='5432' \
DB_DATABASE='testdb' \
DB_USERNAME='test_user' \
DB_PASSWORD='test_password_1234' 

Далее необходимо стянуть зависимости:
 - Находясь в корне проекта введите - <b>composer install</b>

После того как стянули зависимости, разверните БД с помощью файла
 - /handbook/dump.sql

И в заключении проверьте соединение, перейдя по ссылке
 - http://localhost/goods

Если, перейдя по ссылке, у Вас отобразились данные:
`{
    "code": 200,
    "response": [
        {
            "id": 1,
            "name": "монитор",
            "base_cost": "15000",
            "amount": 5
        },
        {
            "id": 2,
            "name": "клавиатура",
            "base_cost": "400",
            "amount": 25
        },
        {
            "id": 3,
            "name": "электронная беспроводная мышь",
            "base_cost": "200",
            "amount": 50
        }
    ]
}`\
то процедура развёртывания проекта прошла успешно.

## Можете перейти по http://localhost/ что бы ознакомиться с полным роутингом проекта


