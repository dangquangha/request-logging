# Request Logging
This package allow you to track the following information:
- How many times do bots visit?
- How many time do users search or go to page search?
- How many times do users visit your website through Google or CocCoc?


### Installation
In your project folder, run
```
composer require quangha2611/request-logging
```

Aftef finish, publish vendor by this command:
```
php artisan vendor:publish --provider="Workable\RequestLogging\RequestLoggingServiceProvider"
```

and <code>php artisan migrate</code> to run migration file

### Usage Instructions
This package works by using a middleware, logging every request performed by bots in a log file, you can rename the middleware in <code>config/robots_counter.php</code> file.

If you want the middleware works for every request, just put its class <code>\Workable\RequestLogging\Middleware\RobotsCounterMiddleware::class</code> in array <code>$middleware </code> in <code>app/Http/Kernel.php</code>
But the best practise is using this middleware for routes need reporting for better performance.
Also, you can config your accepted request methods you want to be in your log.

If you want to export the obtained information to the database, you can run the following commands:

<b>To report robots visited times: </b>
```
php artisan robot:report --date=today
```

<b>To report users searched times: </b>
```
php artisan user-search:report --date=today
```

<b>To report users visit your website through Google or CocCoc: </b>
```
php artisan refer:report --date=today 
```

I support some another options
```
--date=today

--date=yesterday

--date=week

--date=month

--date=range --start=YYYY-MM-DD --end=YYYY-MM-DD
```
