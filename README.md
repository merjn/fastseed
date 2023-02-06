# FastSeed
FastSeed is a Laravel package that enables parallelization of your database seeds. It's particularly useful if you
have a large number of database seeds that take a long time to run.

FastSeed is **not** a drop-in replacement for Laravel's built-in database seeder. It is a wrapper around the built-in 
seeder. More concretely, FastSeed extends the built-in seeder, and adds additional functionality to it by creating 
new methods, but never overrides the existing methods. You are still able to use the built-in seeder as you normally
would.


## Installation
1. Install OpenSwoole 22.0.0 or higher. You can find the installation instructions [here](https://https://openswoole.com/).
2. Install FastSeed via composer. The package is not published yet, so you need to add the repository to your composer.json file.
3. Run `php artisan vendor:publish` to publish the package to your project.

## Configuration
FastSeed works out of the box. However, you may want to configure the number of workers that will be used to 
run your seeds. 

To do this, open `config/fastseed.php`.  Additionally, you can use environment variables to configure FastSeed:
```
FASTSEED_DRIVER=openswoole
FASTSEED_SWOOLE_WORKERS=6
```
FastSeed uses a coroutine pool. The number of workers you set will be the number of coroutines that will be used to run 
your seeds. You can use the amount of CPU cores as an initial value for the number of workers, and then adjust it
according to your needs.

Also keep in mind that some database drivers (e.g. MySQL) may consume a lot of memory. If you have a large number of seeds,
you may want to set the number of workers to a lower number than the number of CPU cores you have.

## Usage
You'll have to open the `DatabaseSeeder` class and extend it with the `Merjn\FastSeed\Seeder\FastSeed` class instead of 
`Illuminate\Database\Seeder`. In order to actually run the seeders in parallel, you have to specify which seeders you want
to run in parallel. You can do this by calling the `callParallel` method. 

For example:
```php
<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Merjn\FastSeed\Seeder\FastSeed; // (1)

class DatabaseSeeder extends FastSeed
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    { 
        // Run the following seeders sequentially.
        $this->call(UsersTableSeeder::class); // (2)
        $this->call(PostsTableSeeder::class);
        $this->call([
            CommentsTableSeeder::class,
            TagsTableSeeder::class,
        ]);

        // Run the following seeders in parallel.        
        $this->callParallel([ // (3)
            CategoriesTableSeeder::class,
            TagsPivotTableSeeder::class,
        ]);
    }
}
```
1. Import the `Merjn\FastSeed\Seeder\FastSeed` class.
2. Run the seeders sequentially.
3. Run the seeders in parallel.

That's it. Now, just run `php artisan db:seed` as you normally would. The specified seeder classes will be run in parallel.

### Conditional parallelization
You can also conditionally run the seeders in parallel. For example, you may want to run the seeders in parallel only 
if the application is running in development mode. You can do this by using the `callParallelIf` method:
```php
<?php

// ...

public function run(): void 
{
    $this->callParallelIf(fn (): bool => App::environment('local'), [
        CategoriesTableSeeder::class,
        TagsPivotTableSeeder::class,
    ]);
}
```
The seeders will run sequentially if the closure returns `false`, meaning that the application is not running in 
development mode.

### Grouping seeders
You can also group the seeders. This is useful if you want parallelization, but seeders rely on each other through
foreign keys. For example, you may want to run the `UsersTableSeeder` and `PostsTableSeeder` in parallel, but you
want to run the `CommentsTableSeeder` after the `PostsTableSeeder` has finished. You can do this by using the
`callParallel` and `callParallelIf` methods:
```php
<?php

// ...

public function run(): void 
{
    $group = [        
        [
            // (1)
            PostsTableSeeder::class,
            CommentsTableSeeder::class,
        ],
        
        // (2)
        UsersTableSeeder::class,
        TagsSeeder::class,
    ];
    
    // Call the seeders in parallel.
    $this->callParallel($group);

    // Call the seeders in parallel if the application is running in development mode.
    $this->callParallelIf(fn (): bool => App::environment('local'), $group);
}
```
The group can contain an array of seeders, or a single seeder. In the example above, the seeders will be run in the
following order:
1. Allocate the `PostsTableSeeder` and `CommentsTableSeeder` to a worker. The worker stays busy until both seeders have finished.
2. `UsersTableSeeder` and `TagsSeeder` are run in parallel. They are not grouped, so they do not depend on each other.

