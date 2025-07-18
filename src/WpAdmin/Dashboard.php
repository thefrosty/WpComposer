<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\WpAdmin;

use TheFrosty\WpComposer\Composer\Process;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;
use function admin_url;
use function esc_html__;
use function is_super_admin;
use function printf;
use function submit_button;
use function wp_add_dashboard_widget;
use function wp_get_current_user;
use function wp_nonce_field;
use function wp_slash;

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
<form id="wp-composer-ui__form" aria-hidden="true" method="post">
<p>
<span>Command<br></span>
<select id="wp-composer-ui__command" name="command" required>
    <option value="" disabled selected>Select Command:</option>
    <option value="install">install</option>
    <option value="update">update</option>
    <option value="require">require</option>
    <option value="remove">remove</option>
</select>
<input id="wp-composer-ui__args" name="args" type="text" value="" 
    class="hidden" placeholder="vendor/package:2.*" disabled required>
</p>
<p>
<span>Options<br></span>
<fieldset>
    <div>
        <input type="checkbox" name="flags" value="--no-interaction" checked>
        <label for="flags">--no-interaction</label>
    </div>
    <div>
        <input type="checkbox" name="flags" value="--optimize-autoloader" checked>
        <label for="flags">--optimize-autoloader</label>
    </div>
    <div>
        <input type="checkbox" name="flags" value="--no-dev" checked>
        <label for="flags">--no-dev</label>
    </div>
</fieldset>
</p>
{$cb(get_submit_button('Submit', name: 'wp-composer-ui__submit'))}

<p><pre id="wp-composer-ui__response" style="white-space: pre-wrap"></pre></p>
{$cb(wp_nonce_field(Process::ACTION, Process::NONCE, display: false))}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('wp-composer-ui__command').addEventListener('change', function() {
        const arg = document.getElementById('wp-composer-ui__args')
        if (['require', 'remove'].includes(this.value)) {
            arg.classList.remove('hidden')
            arg.removeAttribute('disabled')
            arg.setAttribute('required', 'required')
        } else {
            arg.classList.add('hidden')
            arg.removeAttribute('required')
            arg.setAttribute('disabled', 'disabled')
        }
    })
    
    document.getElementById('wp-composer-ui__form').addEventListener('submit', async function (e) {
        e.preventDefault()
        
        const args = document.getElementById('wp-composer-ui__args')
        const command = document.getElementById('wp-composer-ui__command')
        const responseEl = document.getElementById('wp-composer-ui__response')
        const submit = document.getElementById('wp-composer-ui__submit')
        if (command.value === '' || (args.hasAttribute('required') && args.getAttribute('required') === 'required') && !args) {
          return
        }
        
        // Clear the "console".
        responseEl.textContent = ''
        submit.disabled = true
        
        const data = new FormData()
        data.append('action', '{$cb(Process::ACTION)}')
        data.append('args', args.value)
        data.append('command', command.value)
        data.append('flags', Array.from(document.querySelectorAll('input[name="flags"]:checked')).map(cb => cb.value))
        data.append('nonce', document.getElementById('{$cb(wp_slash(Process::NONCE))}').value)
        data.append('user_id', '{$cb(wp_get_current_user()->ID)}')
        
        try {
          const response = await fetch(ajaxurl, {
            method: 'POST',
            body: data,
          })        
          
          const result = await response.json()
        
          if (result.success) {
            responseEl.textContent = '✅ ' + result.data
          } else {
            responseEl.textContent = '❌ Failed: ' + result.data
          }
        } catch (error) {
          console.error(error)
          responseEl.textContent = '❌ Error: ' + error
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
}
