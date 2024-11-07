<?php

namespace Craft\Components\ErrorHandler;

enum ErrorMessage: string
{
    case CONTINUE = 'Продолжить';
    case SWITCHING_PROTOCOLS = 'Переключение протоколов';
    case PROCESSING = 'Обработка';
    case OK = 'OK';
    case CREATED = 'Создано';
    case ACCEPTED = 'Принято';
    case NON_AUTHORITATIVE_INFORMATION = 'Неподтвержденная информация';
    case NO_CONTENT = 'Нет контента';
    case RESET_CONTENT = 'Сбросить контент';
    case PARTIAL_CONTENT = 'Частичное содержание';
    case MULTI_STATUS = 'Множественный статус';
    case MULTIPLE_CHOICES = 'Множество вариантов';
    case MOVED_PERMANENTLY = 'Перемещено навсегда';
    case FOUND = 'Найдено';
    case SEE_OTHER = 'Смотреть другое';
    case NOT_MODIFIED = 'Не изменено';
    case USE_PROXY = 'Используйте прокси';
    case TEMPORARY_REDIRECT = 'Временное перенаправление';
    case BAD_REQUEST = 'Неверный запрос';
    case UNAUTHORIZED = 'Неавторизованный';
    case PAYMENT_REQUIRED = 'Требуется оплата';
    case FORBIDDEN = 'Запрещено';
    case NOT_FOUND = 'Не найдено';
    case METHOD_NOT_ALLOWED = 'Метод не разрешен';
    case NOT_ACCEPTABLE = 'Недопустимо';
    case PROXY_AUTHENTICATION_REQUIRED = 'Требуется аутентификация прокси';
    case REQUEST_TIMEOUT = 'Время ожидания запроса';
    case CONFLICT = 'Конфликт';
    case GONE = 'Ушел';
    case LENGTH_REQUIRED = 'Требуется длина';
    case PRECONDITION_FAILED = 'Предварительное условие не выполнено';
    case REQUEST_ENTITY_TOO_LARGE = 'Слишком большой размер сущности запроса';
    case REQUEST_URI_TOO_LONG = 'Слишком длинный URI запроса';
    case UNSUPPORTED_MEDIA_TYPE = 'Неподдерживаемый тип медиа';
    case REQUESTED_RANGE_NOT_SATISFIABLE = 'Запрашиваемый диапазон недопустим';
    case EXPECTATION_FAILED = 'Ожидание не выполнено';
    case UNPROCESSABLE_ENTITY = 'Неподдерживаемая сущность';
    case LOCKED = 'Заблокировано';
    case FAILED_DEPENDENCY = 'Зависимость не выполнена';
    case UPGRADE_REQUIRED = 'Требуется обновление';
    case INTERNAL_SERVER_ERROR = 'Внутренняя ошибка сервера';
    case NOT_IMPLEMENTED = 'Не реализовано';
    case BAD_GATEWAY = 'Неверный шлюз';
    case SERVICE_UNAVAILABLE = 'Служба недоступна';
    case GATEWAY_TIMEOUT = 'Шлюз временно недоступен';
    case HTTP_VERSION_NOT_SUPPORTED = 'Версия HTTP не поддерживается';
    case VARIANT_ALSO_NEGOTIATES = 'Вариант также ведет переговоры';
    case INSUFFICIENT_STORAGE = 'Недостаточно места';
    case BANDWIDTH_LIMIT_EXCEEDED = 'Превышен лимит пропускной способности';
    case NOT_EXTENDED = 'Не расширено';
    case LOGIC_ERROR = 'Логическая ошибка';
}
