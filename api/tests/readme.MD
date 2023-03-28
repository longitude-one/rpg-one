# How to run test?

## Default mode
```sh
php bin/console doctrine:fixtures:load --env=test -n && php bin/phpunit
```

## Coverage mode
```sh
php bin/console doctrine:fixtures:load --env=test -n && XDEBUG_MODE=coverage php -d pcov.enabled=1 bin/phpunit --coverage-html=.coverage
```
