<?php
final class Loader {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function controller($route, $args = array()) {
		$action = new Action($route, $args);

		return $action->execute($this->registry);
	}

	public function model($model) {
		$file = DIR_APPLICATION . 'model/' . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

		if (file_exists($file)) {
			include_once($file);

			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $file . '!');
			exit();
		}
	}

	public function view($template, $data = array()) {

		Twig_Autoloader::register();

		$paths = [];

		//pega o nome do template atual
		$template_name = $this->registry->get('config')->get('config_template');

		//fix se ta sentando tema default
		if(strpos($template, 'default/template/') !== false) {
			$template = str_replace('default/template/', $template_name . '/template/', $template);
		}

		//check vqmod view
		$vqmod_template = VQMod::modCheck(DIR_TEMPLATE . $template);
		if(strpos($vqmod_template, DIR_VQMOD_CACHE) !== false) {

			$paths[] = DIR_VQMOD_CACHE;
			$template = str_replace(DIR_VQMOD_CACHE, '', $vqmod_template);

		} else {
			//load theme view
			if (is_dir(DIR_TEMPLATE . $template_name . '/template')) {
				$paths[] = DIR_TEMPLATE . $template_name . '/template';
			}

			if (is_dir(DIR_TEMPLATE . 'default/template')) {
				$paths[] = DIR_TEMPLATE . 'default/template';
			}

			//load others view
			$paths[] = DIR_TEMPLATE;

			//load extension view
			if(IS_ADMIN) {
				$extensions_path = glob(DIR_ROOT . '/extensions/*/app/' . CATALOG_OR_ADMIN . '/view/template/', GLOB_ONLYDIR);
				if($extensions_path && is_array($extensions_path) && count($extensions_path)) {
					$paths = array_merge($paths, $extensions_path);
				}
			} else {
				$template_extension = str_replace($template_name . '/template/', '', $template);
				$extensions_path = glob(DIR_ROOT . '/extensions/*/theme/template/', GLOB_ONLYDIR);
				if($extensions_path && is_array($extensions_path) && count($extensions_path)) {
					foreach ($extensions_path as $item) {
						if(file_exists($item . $template_extension)) {
							$template = $template_extension;
							$paths = array_merge($paths, $extensions_path);
						}
					}
				}
			}
		}

		$fileSystem = new Twig_Loader_Filesystem($paths);

		$loader = new Twig_Loader_Chain([$fileSystem]);

		$cache = false;
		if (defined('TWIG_CACHE')) {
			$cache = TWIG_CACHE;
		}

		$twig = new Twig_Environment($fileSystem, array(
			'autoescape' => false,
			'cache'      => $cache,
			'debug'      => true
		));

		$twig->addExtension(new Twig_Extension_Debug());
		$twig->addExtension(new \Newcart\System\TwigExtensions\Ecommerce($this->registry));

		$this->registry->set('twig', $twig);
		extract($data);
		ob_start();

		// First Step - Render Twig Native Templates
		if($fileSystem->exists(str_replace('.tpl', '', $template) . '.twig', $data)) {
			$output = $this->registry->get('twig')->render(str_replace('.tpl', '', $template) . '.twig', $data);
		} else {
			$output = $this->registry->get('twig')->render($template, $data);
		}

		// Second Step - IF template has PHP Syntax, then execute
		eval(' ?>' . $output);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function library($library) {
		$file = DIR_SYSTEM . 'library/' . $library . '.php';

		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load library ' . $file . '!');
			exit();
		}
	}

	public function helper($helper) {
		$file = DIR_SYSTEM . 'helper/' . $helper . '.php';

		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load helper ' . $file . '!');
			exit();
		}
	}

	public function config($config) {
		$this->registry->get('config')->load($config);
	}

	public function language($language) {
		return $this->registry->get('language')->load($language);
	}
}