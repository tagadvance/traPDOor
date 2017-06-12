# traPDOor
This library acts as an extension for PHP Data Objects ([PDO](http://php.net/manual/en/book.pdo.php)). Basically, it makes prepared SQL queries accessible. This is useful for debug-level logging.

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
wget -q -O - https://raw.githubusercontent.com/dwyl/english-words/master/words.txt | grep ".*p.*d.*o.*" words.txt | awk 'length($0) <= 8' | less
```