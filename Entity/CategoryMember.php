<?php
namespace Plugin\FreeDelivery\Entity;

/**
 * CategoryMember entity
 */
class CategoryMember extends \Eccube\Entity\AbstractEntity
{
    
    public function __construct()
    {
    }

    private $id;

    private $cate_member_checkbox;
    
    private $Category;

    private $create_date;

    private $update_date;

    /**
     * @return mixed
     */
    public function getCateMemberCheckbox()
    {
        return $this->cate_member_checkbox;
    }

    /**
     * @param mixed $cate_member_checkbox
     */
    public function setCateMemberCheckbox($cate_member_checkbox)
    {
        $this->cate_member_checkbox = $cate_member_checkbox;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->Category;
    }

    /**
     * @param mixed $Category
     */
    public function setCategory($Category)
    {
        $this->Category = $Category;
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