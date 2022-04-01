<?php
/**
 *
 * PHP version >= 7.0
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Http\Controllers\GoogleSheetController;
use App\Models\Game\Services\GameService;
use App\Models\System\Services\FCMService;


/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class UpdatePendingCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "update:pending";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "pending";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FCMService $service_fcm)
    {
        try {
            GameService::updatePending($service_fcm);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
