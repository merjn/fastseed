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
You'll have to open the `DatabaseSeeder` class and extend it with the `Merjn\FastSeed\Seeder\FastSeed` class. In order
to actually run the seeds in parallel, you have to specify a list of seeders.

Here's an example of a DatabaseSeeder class:
```php
<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Merjn\FastSeed\Seeder\FastSeed;

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
        $this->call(UsersTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        $this->call([
            CommentsTableSeeder::class,
            TagsTableSeeder::class,
        ]);

        // Run the following seeders in parallel.        
        $this->callParallel([
            CategoriesTableSeeder::class,
            TagsPivotTableSeeder::class,
        ]);
    }
}
```

That's it. Now, when you run `php artisan db:seed`, the seeders will be run in parallel.