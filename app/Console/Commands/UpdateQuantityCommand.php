<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PrestaController;
use App\Models\Log;
use App\Repositories\WfirmaApiRepository;
use Illuminate\Console\Command;
use Protechstudio\PrestashopWebService\PrestashopWebService;

class UpdateQuantityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integrator:update';

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
     * @return mixed
     */
    public function handle()
    {
        $log = Log::create([
            'status' => 1
        ]);
       // $new = new PrestashopWebService('https://belamesa-sklep.pl/', 'QETVZIIL3S8DJTPC2M7KNQD6CEYE7PG1', 'false');
        $wfirma = new DashboardController();
        $presta = new PrestaController();

        $this->info('start');
        //$wfirma->getWFirmaGoods($log);
        $log->update([
            'status' => 2
        ]);
        $this->info('WFirma Updated');
        $presta->getProducts($log);
        $log->update([
            'status' => 3
        ]);
        $this->info('Presta Updated');
    }
}
