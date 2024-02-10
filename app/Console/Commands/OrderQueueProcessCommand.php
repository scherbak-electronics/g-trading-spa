<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Trading\OrderQueueService;

class OrderQueueProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-queue-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process exchange order queue items';

    public function __construct(protected OrderQueueService $orderQueueService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->orderQueueService->process();
    }
}
