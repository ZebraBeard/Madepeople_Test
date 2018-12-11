<?php

namespace Madepeople\Test\Model;

use Madepeople\Test\Api\Data\OrdersInterface;
use Madepeople\Test\Model\ResourceModel\Orders as OrdersResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Orders extends AbstractModel implements OrdersInterface
{

    /**
     * @var string
     */
    const CONFIG_ENABLED = "madepeople_test/orders_general/enabled";

    /**
     * @var string
     */
    const CONFIG_MULTIPLIER = "madepeople_test/orders_general/order_multiplier";

    /**
     * Orders constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param array                 $data
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init(OrdersResource::class);
    }
    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(OrdersInterface::ORDER_ID);
    }

    /**
     * Get multiplied sum
     *
     * @return string
     */
    public function getMultipliedSum()
    {
        return $this->getData(OrdersInterface::MULTIPLIED_SUM);
    }

    /**
     * Set Order ID
     *
     * @param  $orderId
     *
     * @return OrdersInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(OrdersInterface::ORDER_ID, $orderId);
    }

    /**
     * Set multiplied sum
     *
     * @param  $multipliedSum
     *
     * @return OrdersInterface
     */
    public function setMultipliedSum($multipliedSum)
    {
        return $this->setData(OrdersInterface::MULTIPLIED_SUM, $multipliedSum);
    }
}
