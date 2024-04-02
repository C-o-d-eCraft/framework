<?php

namespace Craft\Components\ErrorHandler;

class MessageEnum
{
    public const CONTINUE = 'Продолжить';
    public const SWITCHING_PROTOCOLS = 'Переключение протоколов';
    public const PROCESSING = 'Обработка';
    public const OK = 'OK';
    public const CREATED = 'Создано';
    public const ACCEPTED = 'Принято';
    public const NON_AUTHORITATIVE_INFORMATION = 'Неподтвержденная информация';
    public const NO_CONTENT = 'Нет контента';
    public const RESET_CONTENT = 'Сбросить контент';
    public const PARTIAL_CONTENT = 'Частичное содержание';
    public const MULTI_STATUS = 'Множественный статус';
    public const MULTIPLE_CHOICES = 'Множество вариантов';
    public const MOVED_PERMANENTLY = 'Перемещено навсегда';
    public const FOUND = 'Найдено';
    public const SEE_OTHER = 'Смотреть другое';
    public const NOT_MODIFIED = 'Не изменено';
    public const USE_PROXY = 'Используйте прокси';
    public const TEMPORARY_REDIRECT = 'Временное перенаправление';
    public const BAD_REQUEST = 'Неверный запрос';
    public const UNAUTHORIZED = 'Неавторизованный';
    public const PAYMENT_REQUIRED = 'Требуется оплата';
    public const FORBIDDEN = 'Запрещено';
    public const NOT_FOUND = 'Не найдено';
    public const METHOD_NOT_ALLOWED = 'Метод не разрешен';
    public const NOT_ACCEPTABLE = 'Недопустимо';
    public const PROXY_AUTHENTICATION_REQUIRED = 'Требуется аутентификация прокси';
    public const REQUEST_TIMEOUT = 'Время ожидания запроса';
    public const CONFLICT = 'Конфликт';
    public const GONE = 'Ушел';
    public const LENGTH_REQUIRED = 'Требуется длина';
    public const PRECONDITION_FAILED = 'Предварительное условие не выполнено';
    public const REQUEST_ENTITY_TOO_LARGE = 'Слишком большой размер сущности запроса';
    public const REQUEST_URI_TOO_LONG = 'Слишком длинный URI запроса';
    public const UNSUPPORTED_MEDIA_TYPE = 'Неподдерживаемый тип медиа';
    public const REQUESTED_RANGE_NOT_SATISFIABLE = 'Запрашиваемый диапазон недопустим';
    public const EXPECTATION_FAILED = 'Ожидание не выполнено';
    public const UNPROCESSABLE_ENTITY = 'Неподдерживаемая сущность';
    public const LOCKED = 'Заблокировано';
    public const FAILED_DEPENDENCY = 'Зависимость не выполнена';
    public const UPGRADE_REQUIRED = 'Требуется обновление';
    public const INTERNAL_SERVER_ERROR = 'Внутренняя ошибка сервера';
    public const NOT_IMPLEMENTED = 'Не реализовано';
    public const BAD_GATEWAY = 'Неверный шлюз';
    public const SERVICE_UNAVAILABLE = 'Служба недоступна';
    public const GATEWAY_TIMEOUT = 'Шлюз временно недоступен';
    public const HTTP_VERSION_NOT_SUPPORTED = 'Версия HTTP не поддерживается';
    public const VARIANT_ALSO_NEGOTIATES = 'Вариант также ведет переговоры';
    public const INSUFFICIENT_STORAGE = 'Недостаточно места';
    public const BANDWIDTH_LIMIT_EXCEEDED = 'Превышен лимит пропускной способности';
    public const NOT_EXTENDED = 'Не расширено';
    public const LOGIC_ERROR = 'Логическая ошибка';
}