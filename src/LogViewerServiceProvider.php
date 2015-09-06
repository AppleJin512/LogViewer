<?php namespace Arcanedev\LogViewer;

use Arcanedev\Support\Laravel\PackageServiceProvider;

/**
 * Class     LogViewerServiceProvider
 *
 * @package  Arcanedev\LogViewer
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LogViewerServiceProvider extends PackageServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $package = 'log-viewer';

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return __DIR__ . '/..';
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();

        $this->app->register(Providers\UtilitiesServiceProvider::class);

        $this->registerLogViewer();

        $this->app->register(Providers\CommandsServiceProvider::class);
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishConfig();
        $this->registerViews();
        $this->registerTranslations();

        // @codeCoverageIgnoreStart
        if ($this->app->environment() !== 'testing') {
            $this->app->register(Providers\RouteServiceProvider::class);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['log-viewer'];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Resources
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get config file path
     *
     * @return string
     */
    protected function getConfigFile()
    {
        return realpath($this->getBasePath() . "/config/{$this->package}.php");
    }

    /**
     * Register configs.
     */
    private function registerConfig()
    {
        $this->mergeConfigFrom($this->getConfigFile(), $this->package);
    }

    /**
     * Publishes configs.
     */
    private function publishConfig()
    {
        $this->publishes([
            $this->getConfigFile() => config_path("{$this->package}.php")
        ], 'config');
    }

    /**
     * Register and publishes Translations.
     */
    private function registerTranslations()
    {
        $langPath = $this->getBasePath() . '/resources/lang';

        $this->loadTranslationsFrom($langPath, $this->package);
        $this->publishes([
            $langPath => base_path("resources/lang/arcanedev/{$this->package}"),
        ], 'translations');
    }

    /**
     * Register and published Views.
     */
    private function registerViews()
    {
        $viewsPath = $this->getBasePath() . '/resources/views';

        $this->loadViewsFrom($viewsPath, $this->package);
        $this->publishes([
            $viewsPath => base_path("resources/views/arcanedev/{$this->package}"),
        ], 'views');
    }

    /* ------------------------------------------------------------------------------------------------
     |  Services
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the log data class.
     */
    private function registerLogViewer()
    {
        $this->app->singleton($this->package, function ($app) {
            /**
             * @var  Contracts\FactoryInterface     $factory
             * @var  Contracts\FilesystemInterface  $filesystem
             * @var  Contracts\LogLevelsInterface   $levels
             */
            $factory    = $app[$this->package . '.factory'];
            $filesystem = $app[$this->package . '.filesystem'];
            $levels     = $app[$this->package . '.levels'];

            return new LogViewer($factory, $filesystem, $levels);
        });

        $this->app->alias($this->package,                      LogViewer::class);
        $this->app->alias(Contracts\LogViewerInterface::class, LogViewer::class);

        // Registering the Facade
        $this->addFacade('LogViewer', Facades\LogViewer::class);
    }
}
