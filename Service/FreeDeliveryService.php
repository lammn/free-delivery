<?php
/**
 * Created by PhpStorm.
 * User: mnlam
 * Date: 2016/06/09
 * Time: 10:45
 */
namespace Plugin\FreeDelivery\Service;
use Eccube\Entity\Product;
use Plugin\FreeDelivery\Entity\CategoryMember;
use Plugin\FreeDelivery\Entity\FreeDeliProduct;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Entity\Order;
use Eccube\Event\EventArgs;
use Symfony\Component\DomCrawler\Crawler;
use Eccube\Application;

class FreeDeliveryService
{
    private $app;
    const ONLY_ONE_PRODUCT = 1;
    const ALL_PRODUCT = 2;
    const DELIVERY_FREE = 1;
    const DELIVERY_NOT_FREE = 2;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getHtmlShopping(Request $request, Response $response, Order $Order)
    {
        // HTMLを取得し、DOM化
        $crawler = new Crawler($response->getContent());
        $Order = $this->checkShipFree($Order);
        $app = $this->app;
        if($Order != null){
            //update order
            $total = $Order->getSubtotal() + $Order->getCharge();
            $Order->setTotal($total);
            $Order->setDeliveryFeeTotal(0);
            $Order->setPaymentTotal($total);
            $deliveryView = $app->renderView(
                'FreeDelivery/Resource/template/delivery.twig'
            );
            $totalView = $app->renderView(
                'FreeDelivery/Resource/template/total.twig', array(
                    "Order" => $Order
                )
            );
            // show view
            $html = $response->getContent();
            $crawler = new Crawler($html);
            $html = $this->changeHtml("#summary_box__shipping_price", $crawler, $deliveryView);
            $crawler = new Crawler($html);
            $html = $this->changeHtml("#summary_box__total_amount", $crawler, $totalView);
            $app['orm.em']->persist($Order);
            $app['orm.em']->flush($Order);
            $response->setContent($html);
            return $response;
        }
        return $response;
    }

    public function changeHtml($id, $crawler, $view)
    {
        //change one html node by other
        $oldElement = $crawler->filter($id);
        $oldHtml = $oldElement->html();
        $oldHtml = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');
        $newHtml = $view;
        $html = $this->getHtml($crawler);
        $html = str_replace($oldHtml, $newHtml, $html);
        return $html;
    }

    public function addHtml($id, $crawler, $view)
    {
        //add new node
        $oldElement = $crawler->filter($id);
        $oldHtml = $oldElement->html();
        $oldHtml = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');
        $newHtml = $oldHtml.$view;
        $html = $this->getHtml($crawler);
        $html = str_replace($oldHtml, $newHtml, $html);
        return $html;
    }

    public function getHtmlProductDetail(Request $request, Response $response, Product $Product)
    {
        $from = null;
        $to = null;
        $today =  date("Y-m-d H:i:s");
        if (!is_null($Product->getId())) {
            $FreeDeliProduct = $this->app['eccube.plugin.repository.free_delivery_product']->findOneBy(array(
                "Product" => $Product
            ));
            if($FreeDeliProduct != null && $FreeDeliProduct->getProduct()->getId() == $Product->getId()){
                $from = $FreeDeliProduct->getSellFrom();
                $to = $FreeDeliProduct->getSellTo();
            }
            if($from != null && $to != null){
                $from = date_format($FreeDeliProduct->getSellFrom(), "Y-m-d H:i:s");
                $to = date_format($FreeDeliProduct->getSellTo(), "Y-m-d H:i:s");
                $productDetailView = $this->app->renderView(
                    'FreeDelivery/Resource/template/product_detail.twig', array(
                        "sell_from" => $from,
                        "sell_to" => $to,
                    )
                );
                $html = $response->getContent();
                $crawler = new Crawler($html);
                if($today < $from || $today > $to){
                    $html = $this->changeHtml("#detail_cart_box__insert_button", $crawler, null);
                    $crawler = new Crawler($html);
                }
                $html = $this->addHtml("#item_detail_area .extra-form", $crawler, $productDetailView);
                $response->setContent($html);
            }
        }
        return $response;
    }

    public function getHtmlAdminProduct(EventArgs $event)
    {
        $builder = $event->getArgument('builder');
        $Product = $event->getArgument('Product');
        $data = self::DELIVERY_NOT_FREE;
        $from = null;
        $to = null;
        if (!is_null($Product->getId())) {
            $FreeDeliProduct = $this->app['eccube.plugin.repository.free_delivery_product']->findOneBy(array(
                "Product" => $Product
            ));
            if($FreeDeliProduct != null && $FreeDeliProduct->getProduct()->getId() == $Product->getId()){
                $data = $FreeDeliProduct->getFreeDeliCheckbox();
                $from = $FreeDeliProduct->getSellFrom();
                $to = $FreeDeliProduct->getSellTo();
            }
        }
        $builder
            ->add('plg_delivery_free_product', 'choice', array(
                'label' => '送料無料',
                'choices' => array(
                    '1' => '送料無料',
                    '2' => '送料無料(なし)'
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'empty_value' => false,
                'mapped' => false,
                'data' => $data,
            ))
            // 有効期間(FROM)
            ->add('plg_delivery_free_sell_from', 'date', array(
                'label' => '有効期間',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'mapped' => false,
                'data' => $from,
            ))
            // 有効期間(TO)
            ->add('plg_delivery_free_sell_to', 'date', array(
                'label' => '有効期間日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'mapped' => false,
                'data' => $to,
            ));
    }

    public function getHtmlAdminCategory(EventArgs $event)
    {
        $builder = $event->getArgument('builder');
        $Category = $event->getArgument('TargetCategory');
        $data = 1;
        if(!is_null($Category->getId())){
            $CategoryMember = $this->app['eccube.plugin.repository.category_member']->findOneBy(array(
                "Category" => $Category
            ));
            if($CategoryMember != null && $CategoryMember->getCategory()->getId() == $Category->getId()){
                $data = $CategoryMember->getCateMemberCheckbox();
            }
        }
        $builder
            ->add('plg_category_member', 'choice', array(
                'label' =>false,
                'choices' => array(
                    '1' => '会員限定',
                    '2' => '会員限定(なし)'
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'empty_value' => false,
                'mapped' => false,
                'data' => $data,
            ));
    }

    public function saveCategoryMember(EventArgs $event)
    {
        // フォーム情報取得処理
        $form = $event->getArgument('form');
        $selectBox = $form->get('plg_category_member')->getData();
        $Category = $event->getArgument('TargetCategory');
        $CategoryMember = $this->app['eccube.plugin.repository.category_member']->findOneBy(array(
            "Category" => $Category
        ));
        if ($CategoryMember == null) {
            $CategoryMember = new CategoryMember();
        }
        $CategoryMember->setCateMemberCheckbox($selectBox);
        $CategoryMember->setCategory($Category);
        $this->app['orm.em']->persist($CategoryMember);
        $this->app['orm.em']->flush($CategoryMember);
    }

    public function getTopHtml(Request $request, Response $response)
    {
        if(!$this->isAuthRouteFront()){
            $crawler = new Crawler($response->getContent());
            $CategoryMembers = $this->app['eccube.plugin.repository.category_member']->findAll();
            $Categories = $this->app['eccube.repository.category']->findBy(array(
                "Parent" => NULL
            ));
            $category = $this->app->renderView(
                'FreeDelivery/Resource/template/category.twig', array(
                    'Categories' => $Categories,
                    'CategoryMembers' => $CategoryMembers
                )
            );
            $html = $response->getContent();
            $crawler = new Crawler($html);
            $html = $this->changeHtml("#category", $crawler, $category);
            $response->setContent($html);
            return $response;
        }
        return $response;
    }


    public function saveFreeDeliveryProduct(EventArgs $event)
    {
        // フォーム情報取得処理
        $form = $event->getArgument('form');
        $selectBox = $form->get('plg_delivery_free_product')->getData();
        $from = $form->get('plg_delivery_free_sell_from')->getData();
        $to = $form->get('plg_delivery_free_sell_to')->getData();
        $Product = $event->getArgument('Product');
        $FreeDeliProduct = $this->app['eccube.plugin.repository.free_delivery_product']->findOneBy(array(
            "Product" => $Product
        ));
        if ($FreeDeliProduct == null) {
            $FreeDeliProduct = new FreeDeliProduct();
        }
        $FreeDeliProduct->setFreeDeliCheckbox($selectBox);
        $FreeDeliProduct->setSellFrom($from);
        $FreeDeliProduct->setSellTo($to);
        $FreeDeliProduct->setProduct($Product);
        $this->app['orm.em']->persist($FreeDeliProduct);
        $this->app['orm.em']->flush($FreeDeliProduct);
    }

    public function checkShipFree(Order $Order)
    {
        $FreeDelivery =  $this->app['eccube.plugin.repository.free_delivery_plugin']->find(1);
        $option = $FreeDelivery->getOption();
        $orderDetails = $Order->getOrderDetails();
        $today =  date("Y-m-d H:i:s");
        $freeFrom = date_format($FreeDelivery->getFreeFrom(), "Y-m-d H:i:s");
        $freeTo = date_format($FreeDelivery->getFreeTo(), "Y-m-d H:i:s");
        $count = 0;
        if($today > $freeFrom && $today < $freeTo){
            foreach ($orderDetails as $orderDetail) {
                $Product = $orderDetail->getProduct();
                $FreeDeliProduct = $this->app['eccube.plugin.repository.free_delivery_product']->findOneBy(array(
                    "Product" => $Product
                ));
                if ($FreeDeliProduct != null) {
                    if ($option == self::ONLY_ONE_PRODUCT) {
                        if ($Product->getId() == $FreeDeliProduct->getProduct()->getId()) {
                            $Order->setDeliveryFeeTotal(0);
                            // お届け先情報の配送料も0にセット
                            $shippings = $Order->getShippings();
                            foreach ($shippings as $Shipping) {
                                $Shipping->setShippingDeliveryFee(0);
                            }
                            return $Order;
                        }
                    } elseif ($option == self::ALL_PRODUCT) {
                        if ($Product->getId() == $FreeDeliProduct->getProduct()->getId()) {
                            $count++;
                        }
                    }
                }
            }
        }
        if($option == self::ALL_PRODUCT && $count == sizeof($orderDetails)){
            $Order->setDeliveryFeeTotal(0);
            // お届け先情報の配送料も0にセット
            $shippings = $Order->getShippings();
            foreach ($shippings as $Shipping) {
                $Shipping->setShippingDeliveryFee(0);
            }
            return $Order;
        }
        return null;
    }

    /**
     * html decode
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getHtml(Crawler $crawler)
    {
        $html = '';
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }
        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }

    protected function isAuthRouteFront()
    {
        return $this->app->isGranted('ROLE_USER');
    }

}