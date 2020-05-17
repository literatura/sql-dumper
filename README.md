# sql-dumper

## To dump database
``` php sql-dumper.php /some/path/to/folder```

If not specified path to folder will used default path ```~/www```.

You can make the file executable and run it like this ```./sql-dumper.php /some/path/to/folder```


### Handler configuration:
To create new handlers, see the file /src/Handler/ExampleHandler.php

``` 
const ENGINE_CONFIG_PATH = 'path/to/config.php'; // path to engine config file (relative to the www folder)
const ENGINE_NAME = 'Example'; // Name that displayed for user

protected $excludedTables = ['some_table_name', '...']; // These tables will not be included in the dump. Default is empty array

protected $tableWithoutData = ['some_table_name2']; // Only create commands without data will be included in the dump. Default is empty array
```

The handler must implement the interface /src/Handler/HandlerInterface.php
