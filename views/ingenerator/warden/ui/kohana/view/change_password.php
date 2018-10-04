<?php

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 *
 * @var \Ingenerator\Warden\UI\Kohana\View\ChangePasswordView $view
 * @var Ingenerator\KohanaView\Renderer\HTMLRenderer          $renderer
 */

?>
<h1 class="page-title">Change Password</h1>
<form method="post">
    <div class="form-group">
        <label for="input-email" class="control-label">Email</label>
        <input
                class="form-control"
                type="email"
                name="email"
                id="input-email"
                value="<?= $view->user->getEmail(); ?>"
                readonly>
    </div>
    <div class="form-group <?= $view->fields['current_password']['validation_class']; ?>">
        <label for="input-current-password" class="control-label">Current password</label>
        <input
                class="form-control"
                type="password"
                name="current_password"
                id="input-current-password"
                required
        >
        <?php if ($view->fields['current_password']['errors']): ?>
            <span class="help-block"><?= $view->fields['current_password']['errors']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group <?= $view->fields['new_password']['validation_class']; ?>">
        <label for="input-new-password" class="control-label">New password</label>
        <input
                class="form-control"
                type="password"
                minlength="8"
                placeholder="At least 8 characters"
                autocomplete="off"
                name="new_password"
                id="input-new-password"
                value="<?= $view->fields['new_password']['value']; ?>"
                required
        >
        <?php if ($view->fields['new_password']['errors']): ?>
            <span class="help-block"><?= $view->fields['new_password']['errors']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-lg">
            Change Password
        </button>
    </div>
</form>


