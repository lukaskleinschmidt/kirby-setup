<?php

namespace App\Core;

use Kirby\Cms\App as Base;

class App extends Base
{
    /**
     * Custom plugin priority
     */
    protected static array $pluginPriority = [
        //
    ];

    /**
     * Loads all plugins from site/plugins
     */
    protected function pluginsLoader(): array
    {
        $loaded = parent::pluginsLoader();

        static::$plugins = (new Plugins(
            static::$pluginPriority,
            static::$plugins,
        ))->data();

        return $loaded;
    }

	/**
	 * Initializes and returns the Site object
	 */
	public function site(): Site
	{
		return $this->site ??= new Site([
			'errorPageId' => $this->options['error'] ?? 'error',
			'homePageId'  => $this->options['home']  ?? 'home',
			'url'         => $this->url('index'),
		]);
	}
}
