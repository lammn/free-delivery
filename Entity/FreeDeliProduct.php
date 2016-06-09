<?php
namespace Plugin\FreeDelivery\Entity;

/**
 * FreeDeliProduct entity
 */
class FreeDeliProduct extends \Eccube\Entity\AbstractEntity
{
    
    public function __construct()
    {
    }

    private $id;

    private $create_date;

    private $update_date;

    private $free_deli_checkbox;

    private $Product;

    private $sell_from;

    private $sell_to;

    /**
     * @return mixed
     */
    public function getSellFrom()
    {
        return $this->sell_from;
    }

    /**
     * @param mixed $sell_from
     */
    public function setSellFrom($sell_from)
    {
        $this->sell_from = $sell_from;
    }

    /**
     * @return mixed
     */
    public function getSellTo()
    {
        return $this->sell_to;
    }

    /**
     * @param mixed $sell_to
     */
    public function setSellTo($sell_to)
    {
        $this->sell_to = $sell_to;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @param mixed $create_date
     */
    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;
    }

    /**
     * @return mixed
     */
    public function getFreeDeliCheckbox()
    {
        return $this->free_deli_checkbox;
    }

    /**
     * @param mixed $free_deli_checkbox
     */
    public function setFreeDeliCheckbox($free_deli_checkbox)
    {
        $this->free_deli_checkbox = $free_deli_checkbox;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * @param mixed $Product
     */
    public function setProduct($Product)
    {
        $this->Product = $Product;
    }

    /**
     * @return mixed
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @param mixed $update_date
     */
    public function setUpdateDate($update_date)
    {
        $this->update_date = $update_date;
    }

}