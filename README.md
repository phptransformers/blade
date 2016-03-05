# Blade for PHPTransformers

[Laravel Blade](https://laravel.com/docs/master/blade) support for [PHPTransformers](http://github.com/phptransformers/phptransformer).

## Install

Via Composer

``` bash
$ composer require phptransformers/blade
```

## Usage

``` php
$engine = new BladeTransformer();
echo $engine->render('Hello, {{ $name }}!', array('name' => 'phptransformers'));
```


### Options

``` php
$engine = new BladeTransformer(array(
    'cache-dir' => 'path/to/the/cache', // Default to the system temporary directory
));

// ...

$blade = new Illuminate\View\Compilers\BladeCompiler(
    new Illuminate\Filesystem\Filesystem(),
    sys_get_temp_dir()
);
$engine = new BladeTransformer(array(
    'blade' => $blade
));
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.