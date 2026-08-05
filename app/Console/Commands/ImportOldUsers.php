<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Accounts;
use App\Models\UserAccounts; // اضافه کردن مدل مربوطه

class ImportOldUsers extends Command
{
    protected $signature = 'users:import';
    protected $description = 'Import 12k users from Accounts table to Users table';

    public function handle()
    {
        $this->info("Starting the import process... This might take a few minutes.");

        // استفاده از Transaction برای جلوگیری از به هم ریختگی دیتابیس
        DB::transaction(function () {

            $this->info("Deleting old users...");
            User::where('role', 'user')->delete();

            $this->info("Fetching and importing accounts...");

            // استفاده از Chunk برای لود کردن 500 تا 500 تا به جای کل 12 هزار تا در RAM
            Accounts::where('role', 'user')->chunk(500, function ($build_account) {

                foreach ($build_account as $row) {
                    $create_user = User::create([
                        'phone' => $row->phone ?: '',
                        'creator' => $row->creator,
                        'role' => 'customer',
                        'name' => $row->name ?: $row->username,
                        'email' => $row->username . "@mail.com",
                        'password' => Hash::make($row->password),
                        'is_active' => 1,
                    ]);

                    UserAccounts::create([
                        'user_id' => $create_user->id,
                        'account_id' => $row->id,
                    ]);
                }

                $this->info("Processed 500 users...");
            });

            $this->info("Cleaning up admin/manager/agent accounts...");
            Accounts::whereIn('role', ['agent', 'manager', 'admin'])->delete();

            $this->info("Updating agent roles...");
            User::where('role', 'agent')
                ->where('creator', '>', 0)
                ->update(['role' => 'sub_agent']); // آپدیت یکجا (بدون حلقه)

        });

        $this->info("All users imported successfully!");
    }
}
