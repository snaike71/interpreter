# interpreter

## prerequisites

1)  php : "^8.0"
2)  symfony/console: "^6.3"

## Installation Local


### recup repository and dependency

```
git clone https://github.com/snaike71/interpreter.git
cd Interpreter
composer i
```

### execute

you can execute with option code
```
php bin/sch read "( + 5 8 )"
```

or execute code form a file with .scm extention
```
php bin/sch read "teste.scm"
```

## Installation global

### install package
```
composer global require snaik/interpreter:dev-main
```
### execute

you can execute with option code
```
sch read "( + 5 8 )"
```

or execute code from a file with .scm extention
```
sch read "teste.scm"
```