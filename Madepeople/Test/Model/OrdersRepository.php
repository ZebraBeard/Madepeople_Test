<?php

namespace Madepeople\Test\Model;

use Madepeople\Test\Api\Data\{
    OrdersInterface,
    OrdersInterfaceFactory,
    OrdersSearchResultsInterfaceFactory
};
use Madepeople\Test\Api\OrdersRepositoryInterface;
use Madepeople\Test\Model\ResourceModel\{
    Orders as ResourceOrders,
    Orders\Collection,
    Orders\CollectionFactory as OrdersCollectionFactory};
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OrdersRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrdersRepository implements OrdersRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourceOrders
     */
    public $resource;
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;
    /**
     * @var OrdersCollectionFactory
     */
    public $ordersCollectionFactory;
    /**
     * @var OrdersSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    /**
     * @var OrdersInterfaceFactory
     */
    public $ordersInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * OrdersRepository constructor.
     *
     * @param ResourceOrders                      $resource
     * @param StoreManagerInterface               $storeManager
     * @param OrdersCollectionFactory             $ordersCollectionFactory
     * @param OrdersSearchResultsInterfaceFactory $ordersSearchResultsInterfaceFactory
     * @param OrdersInterfaceFactory              $ordersInterfaceFactory
     * @param DataObjectHelper                    $dataObjectHelper
     */
    public function __construct(
        ResourceOrders $resource,
        StoreManagerInterface $storeManager,
        OrdersCollectionFactory $ordersCollectionFactory,
        OrdersSearchResultsInterfaceFactory $ordersSearchResultsInterfaceFactory,
        OrdersInterfaceFactory $ordersInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->ordersCollectionFactory = $ordersCollectionFactory;
        $this->searchResultsFactory = $ordersSearchResultsInterfaceFactory;
        $this->ordersInterfaceFactory = $ordersInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param OrdersInterface $order
     *
     * @return OrdersInterface|\Magento\Framework\Model\AbstractModel
     * @throws CouldNotSaveException
     */
    public function save(OrdersInterface $order)
    {
        /**
         * @var OrdersInterface|\Magento\Framework\Model\AbstractModel $order
         */
        try {
            $this->resource->save($order);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the order: %1',
                    $exception->getMessage()
                )
            );
        }
        return $order;
    }

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     *
     * @return \Madepeople\Test\Api\Data\OrdersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
         * @var \Madepeople\Test\Api\Data\OrdersSearchResultsInterface $searchResults
         */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /**
         * @var \Madepeople\Test\Model\ResourceModel\Orders\Collection $collection
         */
        $collection = $this->ordersCollectionFactory->create();

        //Add filters from root filter group to the collection
        /**
         * @var FilterGroup $group
         */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();

        /**
         * @var SortOrder $sortOrder
         */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $field = 'id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /**
         * @var \Madepeople\Test\Api\Data\OrdersInterface[] $orders
         */
        $orders = [];
        /**
         * @var \Madepeople\Test\Model\Orders $order
         */
        foreach ($collection as $order) {
            /**
             * @var \Madepeople\Test\Api\Data\OrdersInterface $orderDataObject
             */
            $orderDataObject = $this->ordersInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($orderDataObject, $order->getData(), OrdersInterface::class);
            $orders[] = $orderDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($orders);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param  FilterGroup $filterGroup
     * @param  Collection  $collection
     *
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

    /**
     * Delete Order by ID.
     *
     * @param  int $orderId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($orderId)
    {
        $order = $this->getById($orderId);
        return $this->delete($order);
    }

    /**
     * Retrieve Order.
     *
     * @param  int $orderId
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($orderId)
    {
        if (!isset($this->instances[$orderId])) {
            /**
             * @var \Madepeople\Test\Api\Data\OrdersInterface|\Magento\Framework\Model\AbstractModel $order
             */
            $order = $this->ordersInterfaceFactory->create();
            $this->resource->load($order, $orderId);

            if (!$order->getId()) {
                throw new NoSuchEntityException(__('Requested order doesn`t exist'));
            }
            $this->instances[$orderId] = $order;
        }

        return $this->instances[$orderId];
    }

    /**
     * Delete order.
     *
     * @param OrdersInterface $order
     *
     * @return bool true on success
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(OrdersInterface $order)
    {

        $id = $order->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($order);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Order %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }
}
