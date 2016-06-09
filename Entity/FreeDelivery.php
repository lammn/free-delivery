<?php
namespace Plugin\FreeDelivery\Entity;

/**
 * FreeDelivery entity
 */
class FreeDelivery extends \Eccube\Entity\AbstractEntity
{
    
    public function __construct()
    {
    }

    private $id;

    private $option;
    
    private $free_from;

    private $free_to;

    private $create_date;

    private $update_date;

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
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param mixed $option
     */
    public function setOption($option)
    {
        $this->option = $option;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFreeFrom()
    {
        return $this->free_from;
    }

    /**
     * @param mixed $free_from
     */
    public function setFreeFrom($free_from)
    {
        $this->free_from = $free_from;
    }

    /**
     * @return mixed
     */
    public function getFreeTo()
    {
        return $this->free_to;
    }

    /**
     * @param mixed $free_to
     */
    public function setFreeTo($free_to)
    {
        $this->free_to = $free_to;
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