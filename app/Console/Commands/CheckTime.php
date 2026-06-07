<?php

// app/Console/Commands/CheckTime.php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTime extends Command
{
    protected $signature = 'check:time';
    
    public function handle()
    {
        $this->info('Current time: ' . date('Y-m-d H:i:s'));
        $this->info('Timestamp: ' . time());
        return 0;
    }
}