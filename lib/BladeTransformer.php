<?php

namespace PhpTransformers\Blade;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use PhpTransformers\PhpTransformer\TransformerInterface;

/**
 * Class BladeTransformer.
 *
 * The PhpTransformer for Laravel Blade template engine.
 * {@link https://laravel.com/docs/master/blade}
 *
 * @author  MacFJA
 * @package PhpTransformers\Blade
 * @license MIT
 */
class BladeTransformer implements TransformerInterface
{
    protected $blade;

    /**
     * The transformer constructor.
     *
     * Options are:
     *   - "blade" a Illuminate\View\Compilers\BladeCompiler instance
     *   - "cache-dir" the directory where Blade will search and store templates output
     * if the option "blade" is provided, the option "cache-dir" is ignored.
     *
     * @param array $options The BladeTransformer options
     */
    public function __construct(array $options = array())
    {
        if (array_key_exists('blade', $options)) {
            $this->blade = $options['blade'];
            return;
        }

        $cacheDir = sys_get_temp_dir();
        if (array_key_exists('cache-dir', $options)) {
            $cacheDir = $options['cache-dir'];
        }

        $this->blade = new BladeCompiler(new Filesystem(), $cacheDir);
    }

    public function getName()
    {
        return 'blade';
    }

    public function renderFile($file, array $locals = array())
    {
        if ($this->blade->isExpired($file)) {
            // Compile Blade template into PHP code
            $this->blade->compile($file);
        }

        // Get the path of the compiled template
        $path = $this->blade->getCompiledPath($file);

        // Render the template
        ob_start();
        extract($locals, EXTR_SKIP);
        include $path;
        return ltrim(ob_get_clean());
    }

    public function render($template, array $locals = array())
    {
        // Compile Blade template into PHP code
        $data = $this->blade->compileString($template);

        // Create the temporary PHP file that will contains the compiled PHP code
        $path = tempnam(sys_get_temp_dir(), 'blade');
        file_put_contents($path, $data);

        ob_start();
        extract($locals, EXTR_SKIP);
        include $path;
        $result = ob_get_clean();
        unlink($path);
        return $result;
    }
}
