[![Build Status](https://travis-ci.org/tagadvance/traPDOor.svg?branch=master)](https://travis-ci.org/tagadvance/traPDOor)
[![Coverage Status](https://coveralls.io/repos/github/tagadvance/traPDOor/badge.svg?branch=master)](https://coveralls.io/github/tagadvance/traPDOor?branch=master)

# traPDOor
This library acts as an extension for PHP Data Objects ([PDO](http://php.net/manual/en/book.pdo.php)). Basically, it makes prepared SQL queries accessible. This is useful for debug-level logging.

## Download / Install
The easiest way to install traPDOor is via Composer:
```bash
composer require "tagadvance/trapdoor:dev-master"
```
```json
{
    "require": {
        "tagadvance/trapdoor": "dev-master"
    }
}
```

## Example
```php
$pdo = new TraPDO($dsn);
$sql = 'SELECT * FROM foo WHERE a = ? AND b = ? AND c = ?';
$statement = $pdo->prepare($sql);
$statement->bindValue(1, 'one');
$statement->bindValue(2, 2);
$statement->bindValue(3, 'three');
$preparedQueryString = $statement->getPreparedQueryString();
$log->debug($preparedQueryString);
```

## What's with the name?
```bash
wget -q -O - https://raw.githubusercontent.com/dwyl/english-words/master/words.txt | grep ".*p.*d.*o.*" | awk 'length($0) <= 8' | less
```