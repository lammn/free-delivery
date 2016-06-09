<?php

/*
 * This file is part of the FreeDelivery
 *
 * Copyright (C) 2016 lammn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\FreeDelivery\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Plugin\FreeDelivery\Entity\FreeDelivery;

class ConfigController
{

    /**
     * FreeDelivery用設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $FreeDelivery = $app['eccube.plugin.repository.free_delivery_plugin']->find(1);
        $form = $app['form.factory']->createBuilder('freedelivery_config')->getForm();
        $form->setData($FreeDelivery);
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if (!$FreeDelivery) {
                    $FreeDelivery = $data;
                    $FreeDelivery->setId(1);
                }else{
                    $FreeDelivery->setOption($data['option']);
                    $FreeDelivery->setFreeFrom($data['free_from']);
                    $FreeDelivery->setFreeTo($data['free_to']);
                }

                $app['orm.em']->persist($FreeDelivery);
                $app['orm.em']->flush($FreeDelivery);
                $app->addSuccess('admin.free_delivery.save.complete', 'admin');
            }
        }

        return $app->render('FreeDelivery/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }

}
