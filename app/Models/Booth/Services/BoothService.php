<?php

namespace App\Models\Booth\Services;

use App\Models\Booth\Entities\Booth;
use App\Models\Booth\Entities\BoothOrder;
use Carbon\Carbon;

class BoothService
{
    static public function disableBoothOnHourBefore($hour)
    {
    	return Booth::where('is_enabled', '=', 1)
		            ->where('updated_at', '<', Carbon::now()->subhours($hour))
		            ->whereHas('user', function($query){
		                $query->where('is_shrimper', '=', 0);
		              })
		            ->update(['is_enabled' => 0]);
    }

    static public function closeOrderByHour($hour)
    {
    	return BoothOrder::where('is_close', '=', 0)
		                 ->where('updated_at', '<', Carbon::now()->subhours($hour))
		                 ->update(['is_close' => 1]);
    }
}
