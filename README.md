SafeApc - PHP APC Cache Safe Using Wrapper
=======

## Usage

```php

use SafeApc;

// initialize
$apc = new SafeApc();
$apc->setCacheStartTime($_SERVER['REQUEST_TIME']);
$apc->setCacheVersionKey(file_get_contents('apc_version'));


// set key
$apc->set('cache key', $value, 100);

// get key (throws SafeApcNotFoundException)
$apc->get('cache key');

// delete key
$apc->delete('cache key');

```

## clear all cache

change version key

