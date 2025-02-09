# Changelog

В этом файле ведется учет изменений проекта

Формат основан на [стандарте формата CHANGELOG](https://keepachangelog.com/en/1.0.0/),
и придерживается [правил версионирования](https://semver.org/spec/v2.0.0.html).

## [ [1.0.7](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.7) ] - 09.02.2025
- Реализовано:
  - Реализован компонент KafkaFacade

## [ [1.0.6](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.6) ] - 06.02.2025
- Реализовано:
  - Реализован компонент RedisFacade

## [ [1.0.5](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.5) ] - 30.01.2025
- Изменено:
  - Добавил в зависимости redis

## [ [1.0.4](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.4) ] - 30.01.2025
- Исправлено:
  - Поправил контракт для работы с redis.

## [ [1.0.3](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.3) ] - 30.01.2025
- Реализовано:
  - Добавил компонент для работы с redis.

## [ [1.0.2](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.2) ] - 23.01.2025
- Исправлено:
  - Поправил контракт для клиента keycloak

## [ [1.0.1](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.1) ] - 12.01.2025
- Реализовано:
  - Поправил контракт для клиента keycloak

## [ [1.0.0](https://github.com/C-o-d-eCraft/framework/releases/tag/1.0.0) ] - 10.01.2025
- Реализовано:
  - Добавлен контракт для кейклок клиента и компонент для работы с кейклок

## [ [0.4.4](https://github.com/C-o-d-eCraft/framework/releases/tag/0.4.4) ] - 14.11.2024
- Исправлено:
  - Исправлен объект Resource 

## [ [0.4.3](https://github.com/C-o-d-eCraft/framework/releases/tag/0.4.3) ] - 07.11.2024
- Изменено:
  - Валидация в роутере с помощью Validator 
  - Переименован класс QueryBuilder на MySql\Connection
  - Переименован класс NotAuthorizedHttpException в UnauthorizedHttpException
  - Переименована директория service в Service
  - Доработан DIContainer
- Исправлено:
  - Уменьшил уровень вложенности в AbstractCommand
  - Вынес методы из ConsoleKernel в Input
  - Привел параметры конструктора к единообразию
  - Удален излишний абстрактный класс Message

## [ [0.4.2](https://github.com/C-o-d-eCraft/framework/releases/tag/0.4.2) ] - 20.10.2024
- Реализовано:
  - Реализована возможность добавлять мидлвеары на отдельные маршруты ресурсов

## [ [0.4.1](https://github.com/C-o-d-eCraft/framework/releases/tag/0.4.1) ] - 30.09.2024
- Исправлено:
  - Исправлен ResourceController 

## [ [0.4.0](https://github.com/C-o-d-eCraft/framework/releases/tag/0.4.0) ] - 10.09.2024
- Изменено:
  - Удалил неиспользуемые контракты
- Исправлено:
  - Исправил обработчики ошибок 
  - Исправил определение режима в DebugTag
  - Исправил методы по манифесту
  - Исправил ResourceController
  - Исправил Router
- Реализовано:
  - Класс для работы с файловой системой
  - Плагины Detach, Help

## [ [0.3.4](https://github.com/C-o-d-eCraft/framework/releases/tag/0.3.4) ] - 08.08.2024
- Реализовано:
  - Абстрактный базовый контроллер
  - Обработчик опций 
  - Покрыл юнит тестами ConsoleKernel
  - Покрыл юнит тестами Input
  - Покрыл юнит тестами InputArguments
- Изменено:
  - Изменил метод получения типа в catch блоках 

## [ [0.3.3](https://github.com/C-o-d-eCraft/framework/releases/tag/0.3.3) ] - 01.07.2024
- Исправлено:
  - Request
  - Router
  - Validator

## [ [0.3.2](https://github.com/C-o-d-eCraft/framework/releases/tag/0.3.2) ] - 28.06.2024
- Реализовано:
  - Хранилище xDebugTag
- Изменено:
  - xDebugTag генератор
  - логирование

## [ [0.3.1](https://github.com/C-o-d-eCraft/framework/releases/tag/0.3.1) ] - 20.06.2024
- Исправлено:
  - Исправлена запись трейса при ошибке
- Реализовано:
  - Добавил объект QueryBuilder и все, что связано с ним

## [ [0.3.0](https://github.com/C-o-d-eCraft/framework/releases/tag/0.3.0) ] - 23.05.2024
- Изменено:
  - Изменена логика формирования XDebugTag
  - Изменена логика логирования
- Исправлено:
  - Исправил ErrorHandler

## [ [0.2.5](https://github.com/C-o-d-eCraft/framework/releases/tag/0.2.5) ] - 25.04.2024
- Реализовано:
  - Добавил проверку заголовков запроса в ErrorHandler 
  - Добавил методы для группировки маршрутов в RoutesCollection 
- Изменено:
  - Изменил методы для реализации sql запросов

## [ [0.2.4](https://github.com/C-o-d-eCraft/framework/releases/tag/0.2.4) ] - 16.04.2024
- Изменено:
  - Изменил исключения инфраструктуры
  - Изменил метод вызова контроллера в роутере
  - Изменил метод delete для БД
  - Изменил метод exec для БД
  - Изменил Router
- Исправлено:
  - Исправил критические ошибки

## [ [0.2.3](https://github.com/C-o-d-eCraft/framework/releases/tag/0.2.3) ] - 28.03.2024
- Изменено:
  - Изменил метод exec для работы с БД

## [ [0.2.2](https://github.com/C-o-d-eCraft/framework/releases/tag/0.2.2) ] - 27.03.2024
- Изменено:
  - Изменил ядро для работы со статус кодами

## [ [0.2.1](https://github.com/C-o-d-eCraft/framework/releases/tag/0.2.1) ] - 27.03.2024
- Исправлено:
  - Исправил критические ошибки

## [ [0.2.0](https://github.com/C-o-d-eCraft/framework/releases/tag/0.2.0) ] - 25.03.2024
- Исправлено:
  - Исправил критические ошибки
  
## [ [0.1.9](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.9) ] - 25.03.2024
- Исправлено:
  - Исправил критические ошибки

## [ [0.1.8](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.8) ] - 25.03.2024
- Исправлено:
  - Исправил критические ошибки

## [ [0.1.7](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.7) ] - 24.03.2024
- Исправлено:
  - Исправил Router

## [ [0.1.6](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.6) ] - 24.03.2024
- Изменено:
  - Изменил Router
  - Изменил RoutesCollection
  - Изменил ConsoleKernel

## [ [0.1.5](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.5) ] - 24.03.2024
- Реализовано:
  - Реализовал вспомогательный метод в JsonResponse 
- Исправлено
  - Исправил RequestFactory
  - Исправил ConsoleKernel

## [ [0.1.4](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.4) ] - 22.03.2024
- Исправленно:
  - Исправил Router
  - Исправил RoutesCollection

## [ [0.1.3](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.3) ] - 22.03.2024
- Реализовано:
  - Добавил HandleInterface
- Изменено:
  - Изменил парсинг маршрутов в RoutesCollection

## [ [0.1.2](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.2) ] - 22.03.2024
- Реализовано:
  - Добавил unit-тесты
  - Добавил ObserverInterface
  - Добавил MiddlewareInterface
  - Добавил EventMessageInterface
- Изменено:
  - Изменил namespaces

## [ [0.1.1](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.1) ] - 21.03.2024
- Изменено:
  - Изменил composer.json 

## [ [0.1.0](https://github.com/C-o-d-eCraft/framework/releases/tag/0.1.0) ] - 20.03.2024
- Реализовано:
  - Добавлено описание пакета
  - Добавлен CHANGELOG
  - Добавлена лицензия