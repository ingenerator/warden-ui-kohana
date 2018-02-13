<?php
use Ingenerator\Warden\UI\Kohana\View\LoginView;

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 *
 * @var LoginView                             $view
 * @var Ingenerator\KohanaView\Renderer\HTMLRenderer $renderer
 */
?>
<form method="post">
    <div class="form-group">
        <label for="input-email" class="control-label">Email</label>
        <input
            class="form-control"
            type="email"
            name="email"
            id="input-email"
            value="<?=$view->email;?>"
            required>
    </div>
    <div class="form-group">
        <label for="input-password" class="control-label">Password</label>
        <input
            class="form-control"
            type="password"
            name="password"
            id="input-password"
            >
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-lg">
            Login
        </button>
    </div>
</form>


