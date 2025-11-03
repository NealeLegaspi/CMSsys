<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class SystemHelper
{
    public static function getActiveQuarter()
    {
        return (int) DB::table('settings')->where('key', 'active_quarter')->value('value') ?? 1;
    }
    public static function setActiveQuarter($quarter)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'active_quarter'],
            ['value' => $quarter]
        );
    }
}
