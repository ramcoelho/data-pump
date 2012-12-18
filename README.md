Data Pump
=========

A generic PHP data pump script.

When should I use it?
---------------------

In any of the following cases:

* You have two databases and want to migrate data from one into another;
* You can't simply copy data (or can, but won't);
* You have to map data over the wire;
* You have to translate data over the wire;

How do I use it?
----------------

    git clone https://github.com/ramcoelho/data-pump.git

* Set the PDO connection parameters in `config/ConnectionBootstrap.php` (`getOriginConnection` and `getDestinationConnection`);
* Define you migration ruleset in the beautiful markdown commented config file `ruleset/sample_ruleset.md`;
* Save your custom ruleset with any name you like and reference it in `config/ConnectionBootstrap.php` (`getRuleset`);

    cd data-pump

    php runDataPump.php

Change whatever I did wrong and help to improve the project.

That's it.
