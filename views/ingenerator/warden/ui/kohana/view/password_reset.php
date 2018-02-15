<?php

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 *
 * @var PasswordResetView                            $view
 * @var Ingenerator\KohanaView\Renderer\HTMLRenderer $renderer
 */
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;

?>
<h1 class="page-title">Password Reset</h1>
<form method="post">
  <div class="form-group">
    <label for="input-email" class="control-label">Email</label>
    <input
        class="form-control"
        type="email"
        name="email"
        id="input-email"
        value="<?= $view->fields['email']['value']; ?>"
        readonly
        required>
  </div>
  <div class="form-group">
    <label for="input-new-password" class="control-label">New Password</label>
    <input
        class="form-control"
        type="password"
        name="new_password"
        id="input-new-password"
        required
    >
  </div>
  <div class="form-group">
    <button class="btn btn-primary btn-lg">
      Change Password
    </button>
  </div>
</form>


