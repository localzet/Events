<?php

namespace localzet;

use Throwable;

class Events
{
    /**
     * @var array
     */
    protected static array $eventMap = [];

    /**
     * @var array
     */
    protected static array $prefixEventMap = [];

    /**
     * @var int
     */
    protected static int $id = 0;

    /**
     * Метод для регистрации обработчика события.
     *
     * @param string $eventName Имя события.
     * @param callable|array $listener Обработчик события.
     * @return int ID обработчика события.
     */
    public static function on(string $eventName, callable|array $listener): int
    {
        if (is_array($listener)) {
            $listener = array_values($listener);
            if (is_string($listener[0]) && class_exists($listener[0])) {
                $listener = [new $listener[0](), $listener[1]];
            }
        }

        $map = str_ends_with($eventName, '*') ? 'prefixEventMap' : 'eventMap';
        static::${$map}[rtrim($eventName, '*')][++static::$id] = $listener;

        return static::$id;
    }

    /**
     * Метод для удаления обработчика события.
     *
     * @param string $eventName Имя события.
     * @param int $id ID обработчика события.
     * @return int Результат удаления обработчика события.
     */
    public static function off(string $eventName, int $id): int
    {
        if (isset(static::$eventMap[$eventName][$id])) {
            unset(static::$eventMap[$eventName][$id]);
            return 1;
        }
        return 0;
    }

    /**
     * Метод для вызова события.
     *
     * @param string $eventName Имя события.
     * @param mixed $data Данные для обработчика события.
     * @param bool $halt Остановить ли выполнение после первого обработчика.
     * @return mixed Результат вызова события.
     */
    public static function emit(string $eventName, mixed $data, bool $halt = false): mixed
    {
        $listeners = static::listeners($eventName);
        $responses = [];
        foreach ($listeners as $listener) {
            try {
                $response = $listener($data, $eventName);
            } catch (Throwable $e) {
                $responses[] = $e;
                continue;
            }
            $responses[] = $response;
            if ($halt && !is_null($response)) {
                return $response;
            }
            if ($response === false) {
                break;
            }
        }
        return $halt ? null : $responses;
    }

    /**
     * Метод для получения обработчиков события.
     *
     * @param string $eventName Имя события.
     * @return callable[] Список обработчиков события.
     */
    public static function listeners(string $eventName): array
    {
        $listeners = static::$eventMap[$eventName] ?? [];
        foreach (static::$prefixEventMap as $name => $callbackItems) {
            if (str_starts_with($eventName, $name)) {
                $listeners = array_merge($listeners, $callbackItems);
            }
        }
        ksort($listeners);
        return $listeners;
    }

    /**
     * Метод для получения списка всех обработчиков событий.
     *
     * @return array Список всех обработчиков событий.
     */
    public static function list(): array
    {
        $listeners = [];

        foreach (array_merge(static::$eventMap, static::$prefixEventMap) as $eventName => $callbackItems) {
            foreach ($callbackItems as $id => $callbackItem) {
                $listeners[$id] = [$eventName . (isset(static::$prefixEventMap[$eventName]) ? '*' : ''), $callbackItem];
            }
        }

        ksort($listeners);
        return $listeners;
    }

    /**
     * Метод для проверки наличия обработчиков события.
     *
     * @param string $eventName Имя события.
     * @return bool Наличие обработчиков события.
     */
    public static function has(string $eventName): bool
    {
        return !empty(static::listeners($eventName));
    }
}