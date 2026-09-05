[![CI](https://github.com/tagadvance/traPDOor/actions/workflows/ci.yml/badge.svg)](https://github.com/tagadvance/traPDOor/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/tagadvance/trapdoor.svg)](https://packagist.org/packages/tagadvance/trapdoor)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.4-777bb4.svg)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

# traPDOor
This library acts as an extension for PHP Data Objects ([PDO](http://php.net/manual/en/book.pdo.php)). Basically, it makes prepared SQL queries accessible. This is useful for debug-level logging.

## Download / Install
The easiest way to install traPDOor is via Composer:
```bash
composer require "tagadvance/trapdoor:^1.0"
```
```json
{
    "require": {
        "tagadvance/trapdoor": "^1.0"
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

## Sponsor
If you find this library useful, please consider [sponsoring](https://github.com/sponsors/tagadvance).
