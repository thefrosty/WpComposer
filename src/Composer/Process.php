<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\Composer;

use Composer\Console\Application;
use ReflectionMethod;
use TheFrosty\WpComposer\WpComposer;
use TheFrosty\WpComposer\WpCommands;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\HttpFoundationRequestInterface;
use TheFrosty\WpUtilities\Plugin\HttpFoundationRequestTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;
use function check_ajax_referer;
use function error_log;
use function is_super_admin;
use function print_r;
use function sprintf;
use function wp_send_json_error;
use function wp_send_json_success;

/**
 * Class Process
 * @package TheFrosty\WpComposer\Composer
 */
class Process implements HttpFoundationRequestInterface, WpHooksInterface
{

    use HttpFoundationRequestTrait, HooksTrait;

    public const string ACTION = 'wp_composer_ui_process';
    public const NONCE = self::class;

    public function addHooks(): void
    {
        $this->addAction('wp_ajax_' . self::ACTION, [$this, 'composerProcess']);
    }

    protected function composerProcess(): void
    {
        check_ajax_referer(self::ACTION, 'nonce');

        $plugin = new WpCommands(new WpComposer(new Application()));
        $request = $this->getRequest()->request;

        if (!$request->has('user_id') || !is_super_admin($request->get('user_id', false))) {
            wp_send_json_error();
        }

        $args = wp_parse_args(
            $request->all(),
            [
                'args' => '',
                'command' => '',
                'flags' => '',
            ]
        );
        $command = $args['command'];

        if (!method_exists($plugin, $command)) {
            wp_send_json_error(sprintf('Unknown command: %s', esc_attr($command)));
        }

        $reflection = new ReflectionMethod($plugin, $command);
        $params = $reflection->getNumberOfParameters();
        /** @var \Symfony\Component\Console\Output\BufferedOutput $response */
        $response = match ($params) {
            0 => $plugin->$command(),
            1 => $plugin->$command($args['flags']),
            2 => $plugin->$command($args['args'], $args['flags']),
        };

        wp_send_json_success($response->fetch());
    }
}
