<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \Andriy\SimpleQueryBuilder;

$queryBuilder = new SimpleQueryBuilder();
echo $queryBuilder->select(['id', 'email', 'password'])
    ->from('user')
    ->where(['email like "%andriy%"', 'password IS NOT NULL'])
//    ->orderBy(['id', 'email'])
    ->orderBy('email')
    ->limit(15)
    ->offset(5)
    ->build();

echo '<br>';

$queryBuilder2 = new SimpleQueryBuilder();
try {
    echo $queryBuilder2->select('*')
        ->from($queryBuilder)
        ->where('id > 5')
        ->buildCount();
} catch (LogicException $exception)
{
    echo $exception->getMessage();
}
