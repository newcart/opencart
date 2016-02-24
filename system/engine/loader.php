<?php

use \Newcart\System\Libraries\Extension;

final class Loader
{
    private $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function controller($route, $data = array())
    {
        // $this->event->trigger('pre.controller.' . $route, $data);

        $parts = explode('/', str_replace('../', '', (string)$route));

        // Break apart the route
        while ($parts) {

            //load extension controller
            $extensions_file = glob(DIR_ROOT . '/' . $this->config()->get('extension_path') . '/*/*/' . $this->config()->get('environment') . '/controller/' . implode('/', $parts) . '.php');
            if ($extensions_file && is_array($extensions_file) && count($extensions_file)) {
                $file = $extensions_file[0];
            } else {
                $file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';
            }

            $class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', implode('/', $parts));

            if (is_file($file)) {
                include_once($file);

                break;
            } else {
                $method = array_pop($parts);
            }
        }

        $controller = new $class($this->registry);

        if (!isset($method)) {
            $method = 'index';
        }

        // Stop any magical methods being called
        if (substr($method, 0, 2) == '__') {
            return false;
        }

        $output = '';

        if (is_callable(array($controller, $method))) {
            $output = call_user_func(array($controller, $method), $data);
        }

        // $this->event->trigger('post.controller.' . $route, $output);

        return $output;
    }

    public function model($model, $data = array())
    {
        // $this->event->trigger('pre.model.' . str_replace('/', '.', (string)$model), $data);

        //load extension model
        $extensions_file = glob(DIR_ROOT . '/' . $this->config()->get('extension_path') . '/*/*/' . $this->config()->get('environment') . '/model/' . $model . '.php');
        if ($extensions_file && is_array($extensions_file) && count($extensions_file)) {
            $file = $extensions_file[0];
        } else {
            $file = DIR_APPLICATION . 'model/' . $model . '.php';
        }

        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once($file);

            $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
        } else {
            trigger_error('Error: Could not load model ' . $file . '!');
            exit();
        }

        // $this->event->trigger('post.model.' . str_replace('/', '.', (string)$model), $output);
    }

    public function view($template, $data = array())
    {
        // $this->event->trigger('pre.view.' . str_replace('/', '.', $template), $data);

        //se o twig for desativado
        if (!$this->config()->get('enable_twig')) {
            return $this->viewRaw($template, $data);
        }

        Twig_Autoloader::register();

        $paths = [];

        //pega o nome do template atual
        $current_theme = $this->config()->get('config_template');

        //remove theme paths
        $view = str_replace([
            'default/template/',
            $current_theme . '/template/'
        ], '', $template);

        //check vqmod view
        $vqmod_template = Vqmod::modCheck(DIR_TEMPLATE . $current_theme . '/' . $view);
        if (strpos($vqmod_template, DIR_VQMOD_CACHE) !== false) {

            $paths[] = DIR_VQMOD_CACHE;
            $view = str_replace(DIR_VQMOD_CACHE, '', $vqmod_template);

        } else {

            //load theme view
            if (is_dir(DIR_TEMPLATE . $current_theme . '/template/')) {
                $paths[] = DIR_TEMPLATE . $current_theme . '/template/';
            }

            //load theme default
            if (is_dir(DIR_TEMPLATE . $this->config()->get('theme_default') . '/template/')) {
                $paths[] = DIR_TEMPLATE . $this->config()->get('theme_default') . '/template/';
            }

            //Get all extensions
            $extensions = Extension::getAll();

            if ($extensions) {
                //load extension view
                if ($this->config()->get('is_admin')) {

                    $extensions_path = glob(
                        DIR_ROOT . '/' . $this->config()->get('extension_path') .
                        '/*/*/' . $this->config()->get('admin_path') . '/view/template/',
                        GLOB_ONLYDIR
                    );

                    if ($extensions_path && is_array($extensions_path) && count($extensions_path)) {
                        $paths = array_merge($paths, $extensions_path);
                    }
                } else {

                    $extensions_path = glob(
                        DIR_ROOT . '/' . $this->config()->get('extension_path') . '/*/*/' . $this->config()->get('theme_path') . '/template/',
                        GLOB_ONLYDIR
                    );

                    if ($extensions_path && is_array($extensions_path) && count($extensions_path)) {
                        foreach ($extensions_path as $item) {
                            if (file_exists($item . $view)) {
                                $template = $view;
                                $paths = array_merge($paths, $extensions_path);
                            }
                        }
                    }
                }
            }
        }

        $fileSystem = new Twig_Loader_Filesystem($paths);

        $loader = new Twig_Loader_Chain([$fileSystem]);

        $cache = false;
        if ($this->config()->get('twig_cache')) {
            $cache = DIR_STORAGE . '/' . $this->config()->get('twig_cache');
        }

        $twig = new Twig_Environment($fileSystem, array(
            'autoescape' => $this->config()->get('twig_autoescape'),
            'cache' => $cache,
            'debug' => $this->config()->get('twig_debug')
        ));

        $twig->addExtension(new Twig_Extension_Debug());
        $twig->addExtension(new \Newcart\System\TwigExtensions\Ecommerce($this->registry));

        $this->registry->set('twig', $twig);
        extract($data);
        ob_start();

        if ($fileSystem->exists(str_replace('.tpl', '', $view) . '.twig', $data)) {
            $output = $this->registry->get('twig')->render(str_replace('.tpl', '', $view) . '.twig', $data);
        } else if ($fileSystem->exists($view, $data)) {
            $output = $this->registry->get('twig')->render($view, $data);
        } else {
            trigger_error('Error: Could not load template ' . $template . '!');
            exit();
        }

        eval(' ?>' . $output);
        $output = ob_get_contents();
        ob_end_clean();

        // $this->event->trigger('post.view.' . str_replace('/', '.', $template), $output);

        return $output;
    }

    /**
     * Get view raw php tpl
     * @param $template
     * @param array $data
     * @return string
     */
    public function viewRaw($template, $data = array())
    {
        // $this->event->trigger('pre.view.' . str_replace('/', '.', $template), $data);
        //load extension view raw
        $extensions_file = glob(
            DIR_ROOT . '/' . $this->config()->get('theme_path') . '/' .
            $this->config()->get('config_template') . '/template/' . $template
        );

        if ($extensions_file && is_array($extensions_file) && count($extensions_file)) {
            $file = $extensions_file[0];
        } else {
            $file = DIR_TEMPLATE . $template;
        }

        if (file_exists($file)) {
            extract($data);
            ob_start();
            require($file);
            $output = ob_get_contents();
            ob_end_clean();
        } else {
            trigger_error('Error: Could not load template ' . $file . '!');
            exit();
        }
        // $this->event->trigger('post.view.' . str_replace('/', '.', $template), $output);
        return $output;
    }

    public function helper($helper)
    {
        //load extension helper
        $extensions_file = glob(DIR_ROOT . '/' . $this->config()->get('extension_path') . '/*/*/helper/' . str_replace('../', '', (string)$helper) . '.php');
        if ($extensions_file && is_array($extensions_file) && count($extensions_file)) {
            $file = $extensions_file[0];
        } else {
            $file = DIR_SYSTEM . 'helper/' . str_replace('../', '', (string)$helper) . '.php';
        }

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load helper ' . $file . '!');
            exit();
        }
    }

    public function config()
    {
        return $this->registry->get('config');
    }

    public function language($language)
    {
        return $this->registry->get('language')->load($language);
    }
}
