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

        return (new NewcartLoader())->controller($route, $data, $this->registry);

    }

    public function model($model, $data = array())
    {
        // $this->event->trigger('pre.model.' . str_replace('/', '.', (string)$model), $data);

        return (new NewcartLoader())->model($model, $this->registry);

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
        (new NewcartLoader())->helper($helper);
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
