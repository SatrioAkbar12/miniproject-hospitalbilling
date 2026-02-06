<?php

namespace App\Console\Commands;

use App\Exports\Transaction\DailyTransactionReportExport;
use App\Mail\Transaction\DailyTransactionReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendDailyTransactionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-transaction-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = 'Report_'.now()->subDay()->format('Y-m-d').'.xlsx';
        $filePath = 'reports/'.$fileName;

        Excel::store(new DailyTransactionReportExport, $filePath);
        Mail::to('interview.deltasurya@yopmail.com')->send(new DailyTransactionReportMail($filePath));

        $this->info('Daily transaction report sent successfully.');
    }
}
