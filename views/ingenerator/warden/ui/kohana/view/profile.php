<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 *
 * @var ProfileView                                  $view
 * @var Ingenerator\KohanaView\Renderer\HTMLRenderer $renderer
 */

use Ingenerator\Warden\UI\Kohana\View\ProfileView;

?>
<h1 class="page-title">My Profile</h1>
<p><strong>Email:</strong> <?= $view->user->getEmail(); ?>
