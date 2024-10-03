СЗНИИ ЛИС
Информационная система для исследований проб в лабораториях для сельскохозяйственного института
================
Схема БД
---------

![SZNII_LIS-BD drawio](https://github.com/user-attachments/assets/e76a8a76-de80-4f62-88b9-39a3b4c25bb2)

Установка
---------
миграция структуры бд
```
yii migrate
```

миграция с тестовыми данными
```
yii migrate --migrationPath=@app/migrations/test_data
```

Загрузка календаря определенного года (обязательно требуется загрузить текущий)
```
yii calendar 2024
```

На 1 декабря каждого года требуется запускать консольную команду на добавление календаря следующего года
cron:
```
0 0 1 12 * php /srv/web/sznii-lis/yii calendar
```

Тестирование
================

* Перед запуском тестирования сделайте предварительно дамб БД, если есть в этом необходимость. Т.к. используемые фикстуры перезаписывают соответствующие таблицы.

Запуск тестирования с помощью команды:
```
codecept run -- -c frontend
```
