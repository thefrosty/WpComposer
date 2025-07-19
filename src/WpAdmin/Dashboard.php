<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\WpAdmin;

use TheFrosty\WpComposer\Composer\Process;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;
use function esc_html;
use function esc_html__;
use function get_submit_button; // phpcs:ignore
use function is_super_admin;
use function sanitize_key;
use function sprintf;
use function wp_add_dashboard_widget;
use function wp_get_current_user; // phpcs:ignore
use function wp_nonce_field; // phpcs:ignore
use function wp_slash; // phpcs:ignore

/**
 * Class Dashboard
 * @package TheFrosty\WpComposer\WpAdmin
 */
class Dashboard implements WpHooksInterface
{

    use HooksTrait;

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction('wp_dashboard_setup', [$this, 'addDashboardWidget']);
    }

    protected function addDashboardWidget(): void
    {
        wp_add_dashboard_widget(
            'wp-composer-ui',
            esc_html__('Composer', 'wp-composer-ui'),
            function (): void {
                if (!is_super_admin()) {
                    return;
                }
                $this->render();
            }
        );
    }

    private function render(): void
    {
        $cb = static fn(mixed $fn): mixed => $fn;
        echo <<<HTML
<div id="wp-composer-ui__wrapper">
<form id="wp-composer-ui__form" aria-hidden="true" autocomplete="off" method="post">
<p>
<span>Command<br></span>
<select id="wp-composer-ui__command" name="command" required>
    <option value="" disabled selected>Select Command:</option>
    <option value="install">install</option>
    <option value="update">update</option>
    <option value="require">require</option>
    <option value="remove">remove</option>
    <option value="search">search</option>
    <option value="diagnose">diagnose</option>
    <option value="version">version</option>
</select>
<input id="wp-composer-ui__args" name="args" type="text" value="" 
    class="hidden" placeholder="vendor/package:2.*" disabled required>
</p>

<fieldset id="wp-composer-ui__flags">
<span>Options<br><br></span>
{$this->renderCheckboxes()}
</fieldset>
<br>

{$cb(get_submit_button('Submit', name: 'wp-composer-ui__submit'))}

<p><span id="wp-composer-ui__response" style="white-space: pre-wrap"></span></p>
{$cb(wp_nonce_field(Process::ACTION, Process::NONCE, display: false))}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('wp-composer-ui__command').addEventListener('change', function() {
        const arg = document.getElementById('wp-composer-ui__args')
        const flags = document.getElementById('wp-composer-ui__flags')
        const showForFlags = document.querySelectorAll('[data-show-for]')
        if (['require', 'remove', 'search'].includes(this.value)) {
            arg.classList.remove('hidden')
            arg.removeAttribute('disabled')
            arg.setAttribute('required', 'required')
        } else {
            arg.classList.add('hidden')
            arg.removeAttribute('required')
            arg.setAttribute('disabled', 'disabled')
        }
        if (['diagnose', 'version'].includes(this.value)) {
            flags.classList.add('hidden')
        } else {
            flags.classList.remove('hidden')
        }
        if (['search'].includes(this.value)) {
            showForFlags.forEach(element => {
                if (element.dataset.showFor === this.value) {
                    element.classList.remove('hidden')
                }
            })
        } else {
            showForFlags.forEach(element => {
                if (element.dataset.showFor !== this.value) {
                    element.classList.add('hidden')
                }
            })
        }
    })
    
    document.getElementById('wp-composer-ui__form').addEventListener('submit', async function (e) {
        e.preventDefault()
        
        const args = document.getElementById('wp-composer-ui__args')
        const command = document.getElementById('wp-composer-ui__command')
        const responseEl = document.getElementById('wp-composer-ui__response')
        const submit = document.getElementById('wp-composer-ui__submit')
        if (
          command.value === '' || 
          (args.hasAttribute('required') && args.getAttribute('required') === 'required') && !args
          ) {
            return
        }

        // Clear the "console".
        responseEl.innerHTML = ''
        submit.disabled = true

        const data = new FormData()
        data.append('action', '{$cb(Process::ACTION)}')
        data.append('args', args.value)
        data.append('command', command.value)
        data.append('flags', Array.from(document.querySelectorAll('input[name="flags"]:checked')).map(cb => cb.value))
        data.append('nonce', document.getElementById('{$cb(wp_slash(Process::NONCE))}').value)
        data.append('user_id', '{$cb(wp_get_current_user()->ID)}')
        
        try {
            const response = await fetch(ajaxurl, { method: 'POST', body: data })
            const result = await response.json()        
            if (result.success) {
                responseEl.innerHTML = result.data
            } else {
                responseEl.innerHTML = '❌ Failed: ' + result.data
            }
        } catch (error) {
            console.error('Error', error)
            responseEl.innerHTML = '❌ Error: ' + error
        } finally {
            submit.disabled = false
        }
    })
})
</script>
</form>
</div>
HTML;
    }

    private function renderCheckboxes(): string
    {
        $flags = [
            ['flag' => '--no-interaction', 'attributes' => ['checked' => 'checked']],
            ['flag' => '--optimize-autoloader', 'attributes' => ['checked' => 'checked']],
            ['flag' => '--no-dev', 'attributes' => ['checked' => 'checked']],
            ['flag' => '--only-name', 'attributes' => ['extra' => ' class="hidden" data-show-for="search"']],
            ['flag' => '--only-vendor', 'attributes' => ['extra' => ' class="hidden" data-show-for="search"']],
        ];
        $html = '';
        foreach ($flags as $data) {
            $div = <<<'HTML'
<div %4$s>
    <label for="%3$s"><input id="%3$s" type="checkbox" name="flags" value="%1$s" %5$s>%2$s</label>
</div>
HTML;
            $html .= sprintf(
                $div,
                esc_attr($data['flag']),
                esc_html($data['flag']),
                sprintf('flag%s', sanitize_key($data['flag'])),
                $data['attributes']['extra'] ?? '',
                $data['attributes']['checked'] ?? '',
            );
        }

        return $html;
    }
}
