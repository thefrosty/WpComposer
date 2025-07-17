<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use Composer\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WP_CLI;
use WP_Theme;
use const DIRECTORY_SEPARATOR;

/**
 * Class WpComposer
 * @package TheFrosty\WpComposer
 */
class WpComposer
{

    public const string VERSION = '0.1.0';

    public function __construct(protected Application $app)
    {
        //
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): ?int
    {
        try {
            $application = $this->getApp();
            $application->setAutoExit(false);
            return $application->run($input, $output);
        } catch (\Exception $e) {
            WP_CLI::warning($e->getMessage());
            return null;
        }
    }

    /**
     * Execute a composer command for all the plugins and themes.
     * @param callable $callback
     */
    public function recursiveExecution(callable $callback): void
    {
        $directories = $this->getDirectories();
        $current_dir = getcwd();

        foreach ($directories as $dir => $data) {
            chdir($dir);

            $is_theme = $data instanceof WP_Theme;
            $is_plugin = !$is_theme;

            $callback($dir, $data, $is_plugin, $is_theme);
        }

        chdir($current_dir);
    }

    /**
     * Retrieve the directories to act upon.
     * @return array
     */
    private function getDirectories(): array
    {
        $index = [];

        $plugins = apply_filters('all_plugins', get_plugins());
        if (count($plugins) > 0) :
            foreach ($plugins as $path => $data) :
                $plugin = $this->filterPlugin($path);

                if ($plugin !== null && $this->shouldUsePath($plugin)) {
                    $index[$plugin] = $data;
                }
            endforeach;
        endif;

        // Themes
        $themes = wp_get_themes();
        $themes_root = trailingslashit(get_theme_root());

        if (count($themes) > 0) :
            foreach ($themes as $path => $data) :
                if ($this->shouldUsePath($themes_root . $path)) {
                    $index[$themes_root . DIRECTORY_SEPARATOR . $path] = $data;
                }
            endforeach;
        endif;

        return apply_filters('wp_composer_paths', $index);
    }

    /**
     * Internally filter the plugin's path.
     * @param string $plugin
     * @return string|null
     */
    private function filterPlugin(string $plugin): ?string
    {
        // They're not in a single file, cannot support them.
        if (dirname(trailingslashit(WP_PLUGIN_DIR) . $plugin) === WP_PLUGIN_DIR) {
            return null;
        }

        return trailingslashit(WP_PLUGIN_DIR) . dirname($plugin);
    }

    /**
     * See if we should include a path if they don't have a composer.json.
     * @param string $path
     * @return bool
     */
    private function shouldUsePath(string $path): bool
    {
        return file_exists(trailingslashit($path) . 'composer.json');
    }
}
