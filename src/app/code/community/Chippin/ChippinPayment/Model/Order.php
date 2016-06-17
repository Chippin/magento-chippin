<?php

class Chippin_ChippinPayment_Model_Order {
    /**
     * Order statuses
     */
    const STATUS_NEW                     = 'chippin_new';
    const STATUS_INVITED                 = 'chippin_invited';
    const STATUS_CONTRIBUTED             = 'chippin_contributed';
    const STATUS_REJECTED                = 'chippin_rejected';
    const STATUS_COMPLETED               = 'chippin_completed';
    const STATUS_PAID                    = 'chippin_paid';
    const STATUS_FAILED                  = 'chippin_failed';
    const STATUS_CANCELED                = 'chippin_canceled';
    const STATUS_TIMEDOUT                = 'chippin_timedout';
}
