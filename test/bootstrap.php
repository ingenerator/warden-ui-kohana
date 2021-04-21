<?php
$_SERVER['KOHANA_ENV'] = 'DEVELOPMENT';
$_SERVER['HTTP_HOST']  = 'demo.test';

require_once __DIR__.'/../koharness_bootstrap.php';

\Kohana::$config->load('url')->set('trusted_hosts', [$_SERVER['HTTP_HOST']]);

// Require in warden core test bootstrap to autoload mocks and base tests
require_once __DIR__.'/../vendor/ingenerator/warden-core/test/bootstrap.php';

// Require dummy page layout view
require_once __DIR__.'/../vendor/ingenerator/kohana-view/tests/mock/ViewModel/PageLayout/DummyPageLayoutView.php';

// Autoload mocks and test-support helpers that should not autoload in the main app
$mock_loader = new \Composer\Autoload\ClassLoader;
$mock_loader->addPsr4('test\\mock\\Ingenerator\\Warden\\UI\\Kohana\\', [__DIR__.'/mock/']);
$mock_loader->addPsr4('test\\unit\\Ingenerator\\Warden\\UI\\Kohana\\', [__DIR__.'/unit/']);
$mock_loader->register();
