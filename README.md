SafeApc - PHP APC Cache Safe Using Wrapper
=======

## Usage

```php

use SafeApc;

// initialize
SafeApc::setCacheStartTime($_SERVER['REQUEST_TIME']);
SafeApc::setCacheVersionKey(file_get_contents('apc_version'));


// set key
SafeApc::set('cache key', $value, 100);

// get key (throws SafeApcNotFoundException)
SafeApc::get('cache key');

// delete key
SafeApc::delete('cache key');

```

## clear all cache

change version key

