<?php
$_SERVER['KOHANA_ENV'] = 'DEVELOPMENT';
require_once __DIR__.'/../koharness_bootstrap.php';

// Require in warden core test bootstrap to autoload mocks and base tests
require_once __DIR__.'/../vendor/ingenerator/warden-core/test/bootstrap.php';

// Require dummy page layout view
require_once __DIR__.'/../vendor/ingenerator/kohana-view/tests/mock/ViewModel/PageLayout/DummyPageLayoutView.php';

// Require fake session implementation from Koharness
require_once __DIR__.'/../vendor/kohana/koharness/helper_classes/Session/Fake.php';

// Autoload mocks and test-support helpers that should not autoload in the main app
$mock_loader = new \Composer\Autoload\ClassLoader;
$mock_loader->addPsr4('test\\mock\\Ingenerator\\Warden\\UI\\Kohana\\', [__DIR__.'/mock/']);
$mock_loader->addPsr4('test\\unit\\Ingenerator\\Warden\\UI\\Kohana\\', [__DIR__.'/unit/']);
$mock_loader->register();
