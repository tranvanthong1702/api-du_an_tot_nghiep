<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vouchers;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ActiveVoucherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:active';

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
        $listVoucher = Vouchers::all();
        $users = User::all();
        $user_id = [];
        foreach ($users as $u) {
            $user_id[] = $u->id;
        }
        foreach ($listVoucher as $v) {
            if ($v->planning == 1) {
                // so sánh ngày active voucher
                $start_day = $v->start_day;
                $start_day = Carbon::create($start_day);
                $now_day = Carbon::create(Carbon::now()->toDateString());
                if ($now_day->diffInDays($start_day) == 0) {
                    // active vovcher
                    $v->update(['active' => 1]);
                    // give voucher for user
                    if ($v->classify_voucher_id != 1) {
                        $v->users()->sync($user_id);
                    }
                }
            }
        }
        // return Command::SUCCESS;
    }
}
