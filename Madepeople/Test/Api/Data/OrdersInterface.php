<?php

namespace Madepeople\Test\Api\Data;

/**
 * @api
 */
interface OrdersInterface
{
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const MULTIPLIED_SUM = 'multiplied_sum';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Get multiplied sum
     *
     * @return string
     */
    public function getMultipliedSum();

    /**
     * Set ID
     *
     * @param  $id
     *
     * @return OrdersInterface
     */
    public function setId($id);

    /**
     * Set Order ID
     *
     * @param  $orderId
     *
     * @return OrdersInterface
     */
    public function setOrderId($orderId);

    /**
     * Set multiplied sum
     *
     * @param  $multipliedSum
     *
     * @return OrdersInterface
     */
    public function setMultipliedSum($multipliedSum);


}
