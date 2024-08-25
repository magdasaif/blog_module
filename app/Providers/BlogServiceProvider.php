<?php

namespace Modules\Blog\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    //==============================================================================================
    protected string $moduleName = 'Blog';

    protected string $moduleNameLower = 'blog';
    //==============================================================================================
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));

        //==============================================================================================
        // publish all package folder
        $this->publishes([
            dirname(__DIR__) .'/..' => base_path('Modules/'.$this->moduleName)        
        ], $this->moduleNameLower.'-module');
        //==============================================================================================
        $this->handleModulesStatusJsonFile();
        //==============================================================================================
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
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }
    //==============================================================================================
    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }
    //==============================================================================================
    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }
    //==============================================================================================
    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }
    //==============================================================================================
    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);
    }
    //==============================================================================================
    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace').'\\'.$this->moduleName.'\\'.ltrim(config('modules.paths.generator.component-class.path'), config('modules.paths.app_folder', '')));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }
    //==============================================================================================
    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
    }
    //==============================================================================================
    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
    //==============================================================================================
}
