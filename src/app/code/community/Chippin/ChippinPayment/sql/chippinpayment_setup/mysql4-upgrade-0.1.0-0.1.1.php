<?php
$installer = $this;

$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');

// Insert statuses
$installer->getConnection()->insertArray(
    $statusTable,
    array(
        'status',
        'label'
    ),
    array(
        array('status' => 'chippin_pending_payment', 'label' => 'Chippin Pending'),
        array('status' => 'chippin_pending_completion', 'label' => 'Chippin Incomplete'),
        array('status' => 'chippin_complete', 'label' => 'Chippin Complete'),
        array('status' => 'chippin_rejected', 'label' => 'Chippin Rejected'),
        array('status' => 'chippin_timedout', 'label' => 'Chippin Timed Out'),
        array('status' => 'chippin_canceled', 'label' => 'Chippin Canceled')
    )
);

// Insert states and mapping of statuses to states
$installer->getConnection()->insertArray(
    $statusStateTable,
    array(
        'status',
        'state',
        'is_default'
    ),
    array(
        array(
            'status' => 'chippin_pending_payment',
            'state' => 'pending_payment',
            'is_default' => 1
        ),
        array(
            'status' => 'chippin_pending_completion',
            'state' => 'pending_payment',
            'is_default' => 0
        ),
        array(
            'status' => 'chippin_complete',
            'state' => 'processing',
            'is_default' => 0
        ),
        array(
            'status' => 'chippin_rejected',
            'state' => 'holded',
            'is_default' => 0
        ),
        array(
            'status' => 'chippin_timedout',
            'state' => 'holded',
            'is_default' => 0
        ),
        array(
            'status' => 'chippin_canceled',
            'state' => 'canceled',
            'is_default' => 0
        )
    )
);
