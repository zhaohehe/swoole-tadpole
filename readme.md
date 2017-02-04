
# swoole-tadpole

HTLM5 + WebSocket + PHP(swoole) , [http://tadpole.laravel.band/home](http://tadpole.laravel.band/home)

## Installation

first, make sure you have installed mongodb and swoole extension on your php.

clone from github and install dependence

```
$ git clone https://github.com/zhaohehe/swoole-tadpole.git
$ composer install
```

then copy config file

```
$ cp config.example.php config.php
```
set socket server host and port

run your redis server, then start socket server

```
$ php socket.php
```



## License
The pad is licensed under the MIT license.

