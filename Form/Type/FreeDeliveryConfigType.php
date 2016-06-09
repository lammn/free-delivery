<?php

/*
 * This file is part of the FreeDelivery
 *
 * Copyright (C) 2016 lammn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\FreeDelivery\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FreeDeliveryConfigType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option', 'choice', array(
                'choices' => array(
                    '1' => '１つでも対象商品があれば無料',
                    '2' => '全て対象商品の場合のみ無料'
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'empty_value' => false,
            ))
            // 有効期間(FROM)
            ->add('free_from', 'date', array(
                'label' => '有効期間',
                'required' => true,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            // 有効期間(TO)
            ->add('free_to', 'date', array(
                'label' => '有効期間日(TO)',
                'required' => true,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\FreeDelivery\Entity\FreeDelivery',
        ));
    }

    public function getName()
    {
        return 'freedelivery_config';
    }
}
