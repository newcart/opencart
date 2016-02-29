<?php

use \Newcart\System\Modification\System\Engine\Loader as NewcartLoader;

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

            $file = (new NewcartLoader())->controller($route, $parts);

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

        $file = (new NewcartLoader())->view($model);

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

        $output = (new NewcartLoader())->view($template, $data);

        // $this->event->trigger('post.view.' . str_replace('/', '.', $template), $output);

        return $output;
    }

    public function helper($helper)
    {
        $file = (new NewcartLoader())->helper($helper);

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
