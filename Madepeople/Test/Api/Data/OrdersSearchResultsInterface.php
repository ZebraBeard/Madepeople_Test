<?php

namespace Madepeople\Test\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrdersSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Madepeople\Test\Api\Data\OrdersInterface[]
     */
    public function getItems();

    /**
     * @param \Madepeople\Test\Api\Data\OrdersInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}