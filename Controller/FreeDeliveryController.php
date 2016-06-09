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

class FreeDeliveryController
{

    /**
     * FreeDelivery画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // add code...

        return $app->render('FreeDelivery/Resource/template/index.twig', array(
            // add parameter...
        ));
    }

}
