<?php

/*
 * This file is part of the FreeDelivery
 *
 * Copyright (C) 2016 lammn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\FreeDelivery\ServiceProvider;

use Eccube\Application;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\FreeDelivery\Form\Type\FreeDeliveryConfigType;
use Plugin\FreeDelivery\Form\Type\FreeDeliveryProductType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;


class FreeDeliveryServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // プラグイン用設定画面
        $app->match('/' . $app['config']['admin_route'] . '/plugin/FreeDelivery/config', 'Plugin\FreeDelivery\Controller\ConfigController::index')->bind('plugin_FreeDelivery_config');

        // 独自コントローラ
        $app->match('/plugin/[code_name]/hello', 'Plugin\FreeDelivery\Controller\FreeDeliveryController::index')->bind('plugin_FreeDelivery_hello');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new FreeDeliveryConfigType($app);
            return $types;
        }));

        // orm entity register
        $app['eccube.plugin.repository.free_delivery_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\FreeDelivery\Entity\FreeDeliProduct');
        });

        $app['eccube.plugin.repository.free_delivery_plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\FreeDelivery\Entity\FreeDelivery');
        });

        // language
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));

        // ログファイル設定
        $app['monolog.FreeDelivery'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('plugin.FreeDelivery');

            $file = $app['config']['root_dir'] . '/app/log/FreeDelivery.log';
            $RotateHandler = new RotatingFileHandler($file, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'FreeDelivery_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::INFO)
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }
}
