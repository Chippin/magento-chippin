<?php
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$data = array(
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_NEW, 'label' => 'Chippin New'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_INVITED, 'label' => 'Chippin Invited'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_CONTRIBUTED, 'label' => 'Chippin Contributed'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_REJECTED, 'label' => 'Chippin Rejected'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_COMPLETED, 'label' => 'Chippin Completed'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_PAID, 'label' => 'Chippin Paid'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_FAILED, 'label' => 'Chippin Failed'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_CANCELED, 'label' => 'Chippin Canceled'),
    array('status' => Chippin_ChippinPayment_Model_Order::STATUS_TIMEDOUT, 'label' => 'Chippin Timed Out')
);

$mappingData = array(
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_NEW,
        'state' => Mage_Sales_Model_Order::STATE_NEW,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_INVITED,
        'state' => Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_CONTRIBUTED,
        'state' => Mage_Sales_Model_Order::STATE_PROCESSING,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_REJECTED,
        'state' => Mage_Sales_Model_Order::STATE_HOLDED,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_COMPLETED,
        'state' => Mage_Sales_Model_Order::STATE_PROCESSING,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_PAID,
        'state' => Mage_Sales_Model_Order::STATE_PROCESSING,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_FAILED,
        'state' => Mage_Sales_Model_Order::STATE_HOLDED,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_CANCELED,
        'state' => Mage_Sales_Model_Order::STATE_CANCELED,
        'is_default' => 0
    ),
    array(
        'status' => Chippin_ChippinPayment_Model_Order::STATUS_TIMEDOUT,
        'state' => Mage_Sales_Model_Order::STATE_CANCELED,
        'is_default' => 0
    )
);

$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');

// Insert statuses
$installer->getConnection()->insertArray(
    $statusTable,
    array('status', 'label'),
    $data
);

// Insert states and mapping of statuses to states
$installer->getConnection()->insertArray(
    $statusStateTable,
    array('status', 'state', 'is_default'),
    $mappingData
);

$installer->endSetup();
