<?php

namespace App\Core;

use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Plugin\Plugin;
use Kirby\Toolkit\Collection;

class Plugins extends Collection
{
    /**
     * Constructor
     */
    public function __construct(array $priority, array $plugins)
    {
        $this->data = $this->sortPlugins($priority, $plugins);
    }

    /**
     * Sort the plugins by the given priority
     */
    protected function sortPlugins(array $priority, array $plugins): array
    {
        $prevIndex = 0;
        $index = 0;

        foreach ($plugins as $name => $plugin) {
            $priorityIndex = $this->priorityIndex($priority, $name);

            if (! is_null($priorityIndex)) {
                if (isset($prevPriorityIndex) && $priorityIndex < $prevPriorityIndex) {
                    return $this->sortPlugins(
                        $priority, $this->movePlugin($plugins, $plugin, $prevIndex)
                    );
                }

                $prevPriorityIndex = $priorityIndex;
                $prevIndex = $index;
            }

            $index++;
        }

        return $plugins;
    }

    /**
     * Calculate the priority index of the plugin
     */
    protected function priorityIndex(array $priority, string $name): ?int
    {
        $index = array_search($name, $priority);

        if ($index !== false) {
            return $index;
        }

        return null;
    }

    /**
     * Move the plugin to the index while preserving the keys
     */
    protected function movePlugin(array $plugins, Plugin $plugin, int $index): array
    {
        return array_slice($plugins, 0, $index, true)
            + [$plugin->name() => $plugin]
            + array_slice($plugins, $index, null, true);
    }

    /**
     * Load plugins from the given directory
     *
     * @see \Kirby\Cms\App::pluginsLoader()
     */
    public static function load(?string $dir = null): void
    {
        $dir ??= dirname(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file']);

        foreach (Dir::read($dir) as $dirname) {
            if (in_array(substr($dirname, 0, 1), ['.', '_']) === true) {
                continue;
            }

            $dir = __DIR__ . '/' . $dirname;

            if (is_dir($dir) !== true) {
                continue;
            }

            $entry  = $dir . '/index.php';
            $script = $dir . '/index.js';
            $styles = $dir . '/index.css';

            if (is_file($entry) === true) {
                F::loadOnce($entry, allowOutput: false);
            } elseif (is_file($script) === true || is_file($styles) === true) {
                App::plugin(name: 'plugins/' . $dirname, extends: [], root: $dir);
            } else {
                continue;
            }
        }
    }
}
