<?php
use Ingenerator\Warden\UI\Kohana\View\RegistrationView;

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 *
 * @var RegistrationView                             $view
 * @var Ingenerator\KohanaView\Renderer\HTMLRenderer $renderer
 */
?>
<h1 class="page-title">Register</h1>
<form method="post">
    <input type="hidden" name="email_confirmation_token" value="<?= $view->fields['email_confirmation_token']['value']; ?>">
    <div class="form-group">
        <label for="input-email" class="control-label">Email</label>
        <input
            class="form-control"
            type="email"
            name="email"
            id="input-email"
            value="<?= $view->fields['email']['value']; ?>"
            <?=$view->is_email_readonly;?>
            required>
    </div>
    <div class="form-group">
        <label for="input-password" class="control-label">Password</label>
        <input
            class="form-control"
            type="password"
            name="password"
            id="input-password"
            minlength="8"
            placeholder="At least 8 characters"
            autocomplete="off"
            required>
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-lg">
            Register
        </button>
    </div>
</form>


