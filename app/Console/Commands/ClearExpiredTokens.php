<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Sanctum personal access tokens older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info(Carbon::now() . " Clearing expired tokens.");
        DB::table('personal_access_tokens')
            ->where('created_at', '<', now()->subDay())
            ->delete();
        Log::info(Carbon::now() . " Expired tokens cleared successfully.");
    }
}
