# traPDOor

## What's with the name?
```bash
wget -q -O - https://raw.githubusercontent.com/dwyl/english-words/master/words.txt | grep ".*p.*d.*o.*" words.txt | awk 'length($0) <= 8' | less
```