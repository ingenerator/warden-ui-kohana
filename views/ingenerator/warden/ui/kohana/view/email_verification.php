<?php
use Ingenerator\Warden\UI\Kohana\View\EmailVerificationView;

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 *
 * @var EmailVerificationView                        $view
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
        value="<?=$view->fields['email']['value'];?>"
        required>
  </div>
  <div class="form-group">
    <button class="btn btn-primary btn-lg">
       Register
    </button>
  </div>
</form>
