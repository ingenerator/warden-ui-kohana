<?php

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 *
 * @var \Ingenerator\Warden\UI\Kohana\View\ChangeEmailView $view
 * @var \Ingenerator\KohanaView\Renderer\HTMLRenderer       $renderer
 */
?>
<h1 class="page-title">Change Email</h1>
<form method="post">
    <div class="form-group">
        <label for="input-current-email" class="control-label">Current email</label>
        <input
                class="form-control"
                type="email"
                name="email"
                readonly
                id="input-current-email"
                value="<?= $view->user->getEmail(); ?>"
        >
    </div>
    <div class="form-group">
        <label for="input-email" class="control-label">New email</label>
        <input
                class="form-control"
                type="email"
                name="email"
                id="input-email"
                value="<?= $view->fields['email']['value']; ?>"
                required>
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-lg">
            Verify new email
        </button>
    </div>
</form>
