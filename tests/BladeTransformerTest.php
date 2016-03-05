<?php

namespace PhpTransformers\Blade\Test;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use PhpTransformers\Blade\BladeTransformer;

class BladeTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderFile()
    {
        $engine = new BladeTransformer();
        $template = 'tests/Fixtures/BladeTransformer.tpl';
        $locals = array(
            'name' => 'Linus',
        );
        $actual = $engine->renderFile($template, $locals);
        self::assertEquals('Hello, Linus!', $actual);
    }

    public function testRender()
    {
        $engine = new BladeTransformer();
        $template = file_get_contents('tests/Fixtures/BladeTransformer.tpl');
        $locals = array(
            'name' => 'Linus',
        );
        $actual = $engine->render($template, $locals);
        self::assertEquals('Hello, Linus!', $actual);
    }

    public function testGetName()
    {
        $engine = new BladeTransformer();
        self::assertEquals('blade', $engine->getName());
    }

    public function testConstructor()
    {
        $blade = new BladeCompiler(new Filesystem(), sys_get_temp_dir());
        $engine = new BladeTransformer(array('blade' => $blade));

        $template = 'tests/Fixtures/BladeTransformer.tpl';
        $locals = array(
            'name' => 'Linus',
        );
        $actual = $engine->renderFile($template, $locals);
        self::assertEquals('Hello, Linus!', $actual);
    }

    public function testExpired()
    {
        // Clean-up cache dir
        $path = __DIR__.DIRECTORY_SEPARATOR.'cache';
        if (!is_dir($path)) {
            mkdir($path);
        } else {
            foreach (glob($path.DIRECTORY_SEPARATOR.'*') as $file) {
                unlink($file);
            }
        }

        $engine = new BladeTransformer(array('cache-dir' => $path));

        $template = 'tests/Fixtures/BladeTransformer.tpl';
        $locals = array(
            'name' => 'Linus',
        );

        // By checking the code coverage, we can see that the first times, `compile` is executed ...
        $actual = $engine->renderFile($template, $locals);
        self::assertEquals('Hello, Linus!', $actual);

        // ... but not the second times
        $actual = $engine->renderFile($template, $locals);
        self::assertEquals('Hello, Linus!', $actual);

        // Clean-up cache dir
        foreach (glob($path.DIRECTORY_SEPARATOR.'*') as $file) {
            unlink($file);
        }
        rmdir($path);
    }
}
