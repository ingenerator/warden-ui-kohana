<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @copyright 2015 inGenerator Ltd
 * @licence   proprietary
 */
$_SERVER['KOHANA_ENV'] = 'DEVELOPMENT';
require_once(__DIR__.'/../../../application/bootstrap.php');

// And autoload mocks etc for other bits
require_once(__DIR__.'/../../../vendor/ingenerator/warden-core/test/bootstrap.php');

// Autoload mocks and test-support helpers that should not autoload in the main app
$mock_loader = new \Composer\Autoload\ClassLoader;
$mock_loader->addPsr4('test\\mock\\Ingenerator\\Warden\\UI\\Kohana\\', [__DIR__.'/mock/']);
$mock_loader->addPsr4('test\\unit\\Ingenerator\\Warden\\UI\\Kohana\\', [__DIR__.'/unit/']);
$mock_loader->register();
