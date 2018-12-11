<?php

namespace Madepeople\Test\Observer\Sales;

use Madepeople\Test\Model\Orders;
use Madepeople\Test\Model\OrdersFactory;
use Madepeople\Test\Model\OrdersRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;

class OrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var OrdersRepository
     */
    private $ordersRepository;
    /**
     * @var OrdersFactory
     */
    private $orders;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * OrderSaveAfter constructor.
     *
     * @param OrdersRepository     $ordersRepository
     * @param OrdersFactory        $orders
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        OrdersRepository $ordersRepository,
        OrdersFactory $orders,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->orders = $orders;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if ($this->scopeConfig->getValue(Orders::CONFIG_ENABLED)) {
            /**
             * @var $order \Magento\Sales\Model\Order
             */
            $order = $observer->getEvent()->getOrder();
            if ($order instanceof \Magento\Framework\Model\AbstractModel) {
                if ($order->getTotalDue() == 0) {
                    $orderObject = $this->orders->create();
                    $orderObject->setOrderId($order->getId());
                    $multipliedSum =
                        (float)$order->getTotalPaid() * (float)$this->scopeConfig->getValue(Orders::CONFIG_MULTIPLIER);
                    $orderObject->setMultipliedSum($multipliedSum);
                    $this->ordersRepository->save($orderObject);
                }
            }
        }
    }
}
