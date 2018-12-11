<?php

namespace Madepeople\Test\Model\ResourceModel\Orders;

use Madepeople\Test\Model\Orders;
use Madepeople\Test\Model\ResourceModel\Orders as OrdersResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    public $_idFieldName = 'id';


    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Orders::class, OrdersResourceModel::class);
    }


}
