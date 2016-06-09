<?php

/*
 * This file is part of the FreeDelivery
 *
 * Copyright (C) 2016 lammn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\FreeDelivery;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Eccube\Event\EventArgs;
use Eccube\Application;
use Plugin\FreeDelivery\Service\FreeDeliveryService;

class Event
{
    /** @var  \Eccube\Application $app */
    private $app;
    private $service;

    public function __construct($app)
    {
        $this->app = $app;
        $this->service = new FreeDeliveryService($app);
    }

    public function onRenderShoppingBefore(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        // 受注データを取得
        $Order = $this->getOrder();
        if (is_null($Order)) {
            return;
        }
        $response = $this->service->getHtmlShopping($request, $response, $Order);
        $event->setResponse($response);
    }

    public function onRenderProductsEdit(EventArgs $event)
    {
        $this->service->getHtmlAdminProduct($event);
    }

    public function onAdminProductEditComplete(EventArgs $event)
    {
        $this->service->saveFreeDeliveryProduct($event);
    }

    public function onRenderProductsDetailBefore(FilterResponseEvent $event)
    {
        if ($event->getRequest()->getMethod() === 'GET') {
            $request = $event->getRequest();
            $response = $event->getResponse();
            $id = $this->app['request']->attributes->get('id');
            $Product = $this->app['eccube.repository.product']->find($id);
            $response = $this->service->getHtmlProductDetail($request, $response, $Product);
            $event->setResponse($response);
        }
    }

    /**
     * 受注データを取得
     *
     * @return null|object
     */
    private function getOrder()
    {
        // 受注データを取得
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array(
            'pre_order_id' => $preOrderId,
            'OrderStatus' => $this->app['config']['order_processing']
        ));
        return $Order;
    }

}
