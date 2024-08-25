<?php

namespace Modules\Blog\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishModuleCommand extends Command
{
    //==============================================================================================
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'Blog:publish';

    /**
     * The console command description.
     */
    protected $description = 'Publish package files and update module status.';
    //==============================================================================================
    protected string $moduleName = 'Blog';

    protected string $moduleNameLower = 'blog';
    //==============================================================================================
    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }
    //==============================================================================================
    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo 'publish package folders';
        $this->call('vendor:publish', ['--tag' => $this->moduleNameLower.'-module']);

        // echo 'handle module status json file';
        // ModuleStatusHandler::handle();
        $this->handleModulesStatusJsonFile();
        $this->info('Module files published and status updated.');
    }
    //==============================================================================================
    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }
    //==============================================================================================
    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
    //==============================================================================================
    public function handleModulesStatusJsonFile(){
        //==============================================================================================
        // Check if modules_statuses.json exists
        $statusesFilePath = base_path('modules_statuses.json');
        $add_attribute=false;
        if (File::exists($statusesFilePath)) {
            $statuses = json_decode(File::get($statusesFilePath), true);
            //==============================================================================================
            $json = file_get_contents($statusesFilePath); // or json string
            $data = json_decode($json, true);
        
            if (\Illuminate\Support\Arr::has($data, $this->moduleName)) {
                // return 'key exists do something';
            } else {
                // return ' key DOES NOT exist do something else';
                $add_attribute=true;
            }
            //==============================================================================================
        } else {
            $statuses = [];
            $add_attribute=true;
        }
        //==============================================================================================
        if($add_attribute){
            // Update the statuses based on the package being published
            $package = $this->moduleName; // Change this dynamically based on your package
            $statuses[$package] = true;
        }
        //==============================================================================================
        // Write the updated statuses back to modules_statuses.json
        File::put($statusesFilePath, json_encode($statuses, JSON_PRETTY_PRINT));
        //==============================================================================================
    }
    //==============================================================================================
}
