<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variable\Text;
use App\Models\Variable\BigUInt;
use App\Models\Variable\Decimal;
use App\Utilities\Data;
class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->testVariablesWriteRead();
    }

    private function testVariablesWriteRead(): void
    {
        $textVars = new Text();
        $this->info('Set text variable.');
        $start = Data::getTimestampInMilliseconds();
        $textVars->setValue('text_testing_param', '|I|I|l|l|l llII IlI|ll III|lll lIl| lIIII II|||ll IlIll| ll||l lII');
        $value = $textVars->getValue('text_testing_param');
        $end = Data::getTimestampInMilliseconds();
        $executionTime = $end - $start;
        $this->info('Done in '.$executionTime.'ms. Value of text_testing_param: ' . $value);

        $bigIntVars = new BigUInt();
        $this->info('Set big int variable.');
        $start = Data::getTimestampInMilliseconds();
        $bigIntVars->setValue('bigint_testing_param', Data::getTimestampInMilliseconds());
        $value = $bigIntVars->getValue('bigint_testing_param');
        $end = Data::getTimestampInMilliseconds();
        $executionTime = $end - $start;
        $this->info('Done in '.$executionTime.'ms. Value of bigint_testing_param: ' . $value);

        $decVars = new Decimal();
        $this->info('Set decimal variable.');
        $start = Data::getTimestampInMilliseconds();
        $decVars->setValue('dec_testing_param', 20943.67954748);
        $value = $decVars->getValue('dec_testing_param');
        $end = Data::getTimestampInMilliseconds();
        $executionTime = $end - $start;
        $this->info('Done in '.$executionTime.'ms. Value of dec_testing_param: ' . $value);
    }
}
