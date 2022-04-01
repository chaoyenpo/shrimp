<?php

namespace App\Models\Booth\Forms;

use Illuminate\Support\Facades\Validator;
use App\Models\Booth\Entities\BoothOrder;

class BoothOrderForm
{
    public function checkExistUnFinishedOrder($booth_id, $user_id)
    {
        return BoothOrder::where('booth_id', '=', $booth_id)
                         ->where('customer_id', '=', $user_id)
                         ->where('can_evaluate', '=', 1)
                         ->where('is_close', '=', 0)
                         ->whereNotNull('customer_evaluation_id')
                         ->whereNotNull('owner_evaluation_id')
                         ->count() > 0 ? true : false;
    }
}
