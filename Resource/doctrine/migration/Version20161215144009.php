<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Plugin\FreeDelivery\Entity;

/**
 * Class Version20161215144009
 * @package DoctrineMigrations
 */
class Version20161215144009 extends AbstractMigration
{
    protected $tables = array();

    protected $entities = array();

    protected $sequences = array();

    public function __construct()
    {
        $this->tables = array(
            'plg_free_delivery',
            'plg_free_delivery_product'
        );

        $this->entities = array(
            'Plugin\FreeDelivery\Entity\FreeDelivery',
            'Plugin\FreeDelivery\Entity\FreeDeliProduct'
        );

        $this->sequences = array(
            'plg_free_delivery_product_free_delivery_id_seq',
        );
    }

    /**
     * インストール時処理
     * @param Schema $schema
     * @return bool
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function up(Schema $schema)
    {
        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array();
        foreach ($this->entities as $entity) {
            $classes[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        $tool = new SchemaTool($em);
        $tool->createSchema($classes);
    }

    /**
     * アンインストール時処理
     * @param Schema $schema
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function down(Schema $schema)
    {
        foreach ($this->tables as $table) {
            if ($schema->hasTable($table)) {
                $schema->dropTable($table);
            }
        }
        foreach ($this->sequences as $sequence) {
            if ($schema->hasSequence($sequence)) {
                $schema->dropSequence($sequence);
            }
        }
    }
}
