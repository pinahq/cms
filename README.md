# PinaCMS 2

## Установка 
1. Создайте базу данных и укажите параметры доступа к БД в config/db.php
2. Установите зависимости через composer:
```
composer update
```
3. Запустите настройку БД. Скрипт создаст таблицы и триггеры:
```
php pinacli.php system.update
```
4. Установите модули по умолчанию и их данные:
```
php pinacli.php system.install
```
5. Установите клиентские библиотеки через bower:
```
bower i
```
6. Перенесте необходимые клиентские библиотеки с помощью скрипта:
```
sh update-static.sh
```
7. Настройте веб-сервер таким образом, чтобы корень сайта был бы в public/, а любой отсутствующий на диске ресурс веб-сервер запрашивал бы в public/index.php

8. Убедитесь, что существуют и доступны на запись следующие директории:
- public/uploads
- public/resize
- var/cache
- var/compiled
- var/log
- var/temp

9. Если в качестве сервера очередей собираетесь использовать решение по умолчанию (упрощенный менеджер очередей на базе CRON), то добавьтена  cron команду (заместо {your_pina_root} укажите директорию, в которой расположен корень проекта):
```
cd {your_pina_root};php pinacli.php system.cron 
```
Так же вы можете отказаться от асинхронного выполнения тяжелых задач и закомментировать строчку в файле config/app.php:
```
\Pina\EventQueueInterface::class => \Pina\CronEventQueue::class,
```
Кроме того система поддерживает сервер очередей Gearman, описание его настройки будет позже.

10. Откройте сайт в браузере, авторизуйтесь под пользователем по умолчанию с логином / паролем: admin / admin. Поменяйте пароль.

11. Перейдите в панель администратора, используя зеленую иконку-домик слева экрана, перейдите в раздел Настройки -> Модули. Установите дополнительные модули. 

## Обзорные видео

[Авторизация и включение модулей](https://youtu.be/SOxIErvyiO8)

[Добавляем страничку](https://youtu.be/koICvoSfsBw)

[Управление каталогом с помощью тегов](https://youtu.be/L5Il93XhJRA)

[Импорт каталога из произвольного XLS-файла и файла на десятки тысяч позиций](https://youtu.be/ZTx9ScgX7u8)

[Пример каталога на 233 000 товаров без кэширования](https://youtu.be/gnrLE-iPOjw)
