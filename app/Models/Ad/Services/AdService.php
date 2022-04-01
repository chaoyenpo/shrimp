<?php

namespace App\Models\Ad\Services;

use App\Models\Ad\Entities\Ad;

class AdService
{
    public function calculate($minutes, $height, $weight)
    {
    	$cost = 0;

        if ($height = 2){
        	$cost += round($minutes * 5 / 1440, 2);
        }elseif ($height = 3){
        	$cost += round($minutes * 10 / 1440, 2);
        }elseif ($height = 4){
        	$cost += round($minutes * 15 / 1440, 2);
        }

        if ($weight > 0){
        	$cost += round(($minutes * (($weight/2)) / 1440), 2);
        }

        return $cost;
    }
}
