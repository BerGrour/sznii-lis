СЗНИИ ЛИС
================
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

БД СТРУКТУРА
-------------------
Схема бд находится в файле
/SZNII_LIS-BD.drawio.pdf

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```

Тестирование
================

* Перед запуском тестирования сделайте предварительно дамб БД, если есть в этом необходимость. Т.к. используемые фикстуры перезаписывают соответствующие таблицы.

Запуск тестирования с помощью команды:
```
codecept run -- -c frontend
```

Functional
-------------------
Полностью реализованы и переделаны только базовые функциональные тесты на Login, ResendVerificationToken, Signup и VerifyEmail.

Unit
-------------------
Полностью реализованы и переделаны только базовые функциональные тесты на PasswordReset, ResendVerificationEmail, ResetPassword, Signup и VerifyEmail.