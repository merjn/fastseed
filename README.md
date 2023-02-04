# FastSeed
FastSeed is a Laravel package that enables parallelization of your database seeds. It's very useful when you have a lot 
of data to seed and you want to speed up the process. FastSeed is very flexible, because it doesn't force you to run 
all your seeds in parallel. You have to explicitly tell FastSeed which seeds you want to run in parallel. Otherwise,
FastSeed will run your seeds sequentially by using the default Laravel Seeder.


## Installation
1. Install OpenSwoole 22.0.0 or higher. You can find the installation instructions [here](https://https://openswoole.com/).
2. Install FastSeed via composer. The package is not published yet, so you need to add the repository to your composer.json file.
3. Run `php artisan vendor:publish` to publish the package to your project.

## Usage
You'll have to open the DatabaseSeeder class and extend it with the `Merjn\FastSeed\Seeder\FastSeed` class. In order
to actually run the seeds in parallel, you have to specify a list of seeders.

Here's an example of a DatabaseSeeder class:
```php
public function run()
{
    $this->callParallel([
        UsersTableSeeder::class,
        PostsTableSeeder::class,
        CommentsTableSeeder::class,
    ]);
}
```

Please note that the order of the seeders is unimportant. The seeders will be executed in parallel.

#### A note on foreign key constraints
If you have foreign key constraints in your database, you'll have to disable them before running the seeds. Chaining
is currently not supported.
