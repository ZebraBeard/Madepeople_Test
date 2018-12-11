<?php

namespace Madepeople\Test\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Madepeople\test\Api\Data\OrdersInterface;

/**
 * @api
 */
interface OrdersRepositoryInterface
{

    public function save(OrdersInterface $order);


    public function getById($orderId);


    public function getList(SearchCriteriaInterface $searchCriteria);


    public function delete(OrdersInterface $order);


    public function deleteById($orderId);
}
