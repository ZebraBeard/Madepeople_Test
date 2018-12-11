<?php

namespace Madepeople\Test\Model\ResourceModel;

class Orders extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Orders constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('madepeople_test', 'id');
    }
}
