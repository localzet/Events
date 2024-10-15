<p align="center"><a href="https://www.localzet.com" target="_blank">
  <img src="https://cdn.localzet.com/assets/media/logos/ZorinProjectsSP.svg" width="400">
</a></p>

<p align="center">
  <a href="https://packagist.org/packages/localzet/events">
  <img src="https://img.shields.io/packagist/dt/localzet/events?label=%D0%A1%D0%BA%D0%B0%D1%87%D0%B8%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F" alt="Скачивания">
</a>
  <a href="https://github.com/localzet/Events">
  <img src="https://img.shields.io/github/commit-activity/t/localzet/Events?label=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B8%D1%82%D1%8B" alt="Коммиты">
</a>
  <a href="https://packagist.org/packages/localzet/events">
  <img src="https://img.shields.io/packagist/v/localzet/events?label=%D0%92%D0%B5%D1%80%D1%81%D0%B8%D1%8F" alt="Версия">
</a>
  <a href="https://packagist.org/packages/localzet/events">
  <img src="https://img.shields.io/packagist/dependency-v/localzet/events/php?label=PHP" alt="Версия PHP">
</a>
  <a href="https://github.com/localzet/Events">
  <img src="https://img.shields.io/github/license/localzet/Events?label=%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F" alt="Лицензия">
</a>
</p>

# Библиотека событий Localzet

Библиотека событий Localzet — это простая и эффективная система управления событиями для PHP приложений. Она позволяет легко регистрировать, вызывать и управлять событиями.

## Установка

Вы можете установить библиотеку с помощью Composer. Просто выполните команду:

```sh
composer require localzet/events
```

## Использование

### Регистрация обработчиков событий

Вы можете регистрировать обработчики событий с помощью метода `on`. Метод `on` принимает имя события и callable или массив в качестве обработчика.

```php
use localzet\Events;

// Регистрация простого callable обработчика
Events::on('user.created', function($data, $eventName) {
    echo "Пользователь создан с данными: " . json_encode($data);
});

// Регистрация метода класса в качестве обработчика
Events::on('user.deleted', [UserHandler::class, 'handleUserDeleted']);
```

### Вызов событий

Чтобы вызвать событие, используйте метод `emit`. Метод `emit` принимает имя события, данные и необязательный флаг остановки. Флаг остановки прекращает выполнение после первого обработчика, вернувшего ненулевой ответ.

```php
// Вызов события
$data = ['id' => 1, 'name' => 'John Doe'];
Events::emit('user.created', $data);
```

### Удаление обработчиков событий

Вы можете удалить обработчики событий с помощью метода `off`. Метод `off` требует имя события и ID обработчика.

```php
$listenerId = Events::on('user.updated', function($data, $eventName) {
    echo "Пользователь обновлен с данными: " . json_encode($data);
});

// Удаление обработчика
Events::off('user.updated', $listenerId);
```

### Список всех обработчиков событий

Вы можете получить список всех зарегистрированных обработчиков событий с помощью метода `list`.

```php
$listeners = Events::list();
print_r($listeners);
```

### Проверка наличия обработчиков событий

Чтобы проверить, есть ли зарегистрированные обработчики для конкретного события, используйте метод `has`.

```php
if (Events::has('user.created')) {
    echo "Есть обработчики для события user.created.";
}
```

## Продвинутые функции

### Обработчики событий с префиксами

Вы можете регистрировать обработчики для событий с общим префиксом, используя символ `*`.

```php
// Регистрация обработчика для всех событий user.*
Events::on('user.*', function($data, $eventName) {
    echo "Событие $eventName вызвано с данными: " . json_encode($data);
});
```