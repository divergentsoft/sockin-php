# Sockin-php

This is the php library used to connect to the Sockin push service.

```php

$sockin = new Sockin($appId, $appKey, $appSecret);

$sockin->send('test-channel','test-event','hello world');

```