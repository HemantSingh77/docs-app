---
layout: article
language: 'el-gr'
version: '4.0'
title: 'Phalcon\Logger\Factory'
---
# Class **Phalcon\Logger\Factory**

*extends* abstract class [Phalcon\Factory](api/Phalcon_Factory)

*implements* [Phalcon\FactoryInterface](api/Phalcon_FactoryInterface)

<a href="https://github.com/phalcon/cphalcon/tree/v4.0.0/phalcon/logger/factory.zep" class="btn btn-default btn-sm">Source on GitHub</a>

Loads Logger Adapter class using 'adapter' option

```php
<?php

use Phalcon\Logger\Factory;

$options = [
    "name"    => "log.txt",
    "adapter" => "file",
];
$logger = Factory::load($options);

```

## Methods

public static **load** ([Phalcon\Config](api/Phalcon_Config) | *array* $config)

protected static **loadClass** (*mixed* $namespace, *mixed* $config)

...