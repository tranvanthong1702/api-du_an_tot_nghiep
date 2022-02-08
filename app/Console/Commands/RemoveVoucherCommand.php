<?php

namespace App\Console\Commands;

use App\Models\Vouchers;
use Illuminate\Console\Command;
use Carbon\Carbon;
class RemoveVoucherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $listVoucher=Vouchers::all();
        foreach($listVoucher as $v){
            if($v->active==1){
                // so sánh ngày hiện tại với end_day voucher
                $end_day = $v->end_day;
                $end_day = Carbon::create($end_day);
                $now_day = Carbon::create(Carbon::now()->toDateString());
                if($now_day->diffInDays($end_day) == 0){
                     // sotf delete voucher
                    $v->delete();
                }
            }
        }
        // return Command::SUCCESS;
    }
}
