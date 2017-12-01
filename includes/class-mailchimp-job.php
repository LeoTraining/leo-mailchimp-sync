<?php

use DrewM\MailChimp\MailChimp;

class MailChimpJob
{
    public function __construct($batchId, $key)
    {
        $this->mc = new MailChimp($key);
        $this->batch = $this->mc->new_batch($batchId);
    }

    public function isComplete()
    {
        $status = $this->batch->check_status();

        return 'finished' === $status['status'];
    }

    public function completedOperations()
    {
        $status = $this->batch->check_status();

        return [
            'complete' => (int) $status['finished_operations'],
            'total' => (int) $status['total_operations'],
        ];
    }
}
