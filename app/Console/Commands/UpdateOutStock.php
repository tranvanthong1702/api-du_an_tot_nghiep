<?php

namespace App\Console\Commands;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateOutStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:outStock';

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
        $products=Product::all();
        $now_day = Carbon::create(Carbon::now()->toDateString());
        foreach($products as $p){
            if ($now_day->diffInDays($p->expiration_date) == 0) {
                $p->update(['status'=>0]);
            }
        }
        // return Command::SUCCESS;
    }
}
