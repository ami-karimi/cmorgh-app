<?php

namespace App\Console\Commands;

use App\Models\Accounts;
use App\Models\RadAcct;
use App\Models\RadPostAuth;
use Illuminate\Console\Command;

class ClearRadius extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vpn:clear-radius';

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
        $data = Accounts::whereHas('group', function ($query) {
            $query->where('group_type', 'expire');
        })->where('service_group', 'l2tp_cisco')->where('limited', 0)->get();
        foreach ($data as $item){
            RadAcct::where('username',$item->username)->where('acctstoptime','!=',NULL)->delete();
            RadPostAuth::where('username',$item->username)->delete();
        }

    }
}
