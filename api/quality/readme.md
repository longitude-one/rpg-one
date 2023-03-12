
How to install then run PhpStan:
```shell
docker compose exec php sh -c 'composer update --working-dir=quality/php-stan'
docker compose exec php sh -c 'php quality/php-stan/vendor/bin/phpstan analyse -c quality/php-stan/phpstan.neon'
```

How to install then run PHP CS Fixer:
```shell
docker compose exec php sh -c 'composer update --working-dir=quality/php-cs-fixer'
docker compose exec php sh -c 'PHP_CS_FIXER_IGNORE_ENV=1 php quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --allow-risky=yes'
```
