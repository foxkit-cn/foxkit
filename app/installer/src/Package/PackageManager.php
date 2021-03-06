<?php

namespace Foxkit\Installer\Package;

use Foxkit\Application as App;
use Foxkit\Installer\Helper\Composer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class PackageManager
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * Constructor.
     *
     * @param mixed $output
     */
    public function __construct($output = null)
    {
        $this->output = $output ?: new StreamOutput(fopen('php://output', 'w'));

        $config = [];
        foreach (['path.temp', 'path.cache', 'path.vendor', 'path.artifact', 'path.packages', 'system.api'] as $key) {
            $config[$key] = App::get($key);
        }

        $this->composer = new Composer($config, $output);
    }

    /**
     * @param  array $install
     * @param bool $packagist
     * @param bool $preferSource
     * @return bool
     */
    public function install(array $install = [], $packagist = false, $preferSource = false)
    {
        $previousPackageConfigs = App::package()->all(null, true);

        $this->composer->install($install, $packagist, $preferSource);

        $packages = App::package()->all(null, true);
        foreach ($install as $name => $version) {
            $moduleAlreadyExisted = isset($previousPackageConfigs[$name]) && App::module($previousPackageConfigs[$name]->get('module'));

            if ($moduleAlreadyExisted == true) {
                $previousPackageConfig = isset($previousPackageConfigs[$name]) ? $previousPackageConfigs[$name] : null;
                $this->enable($packages[$name], $previousPackageConfig);
            } elseif (isset($packages[$name])) {
                $this->doInstall($packages[$name]);
            }
        }
    }

    /**
     * @param  array $uninstall
     * @return bool
     */
    public function uninstall($uninstall)
    {
        foreach ((array) $uninstall as $name) {
            if (!$package = App::package($name)) {
                throw new \RuntimeException(__('Unable to find "%name%".', ['%name%' => $name]));
            }

            $this->disable($package);
            $this->getScripts($package)->uninstall();
            App::config('system')->remove('packages.' . $package->get('module'));

            if ($this->composer->isInstalled($package->getName())) {
                $this->composer->uninstall($package->getName());
            } else {
                if (!$path = $package->get('path')) {
                    throw new \RuntimeException(__('Package path is missing.'));
                }

                $this->output->writeln(__("Removing package folder."));

                App::file()->delete($path);
                @rmdir(dirname($path));
            }
        }
    }

    /**
     * @param $packages
     * @param $previousPackageConfigs
     */
    public function enable($packages, $previousPackageConfigs = [])
    {
        if (!is_array($packages)) {
            $packages = [$packages];
        }

        if (!is_array($previousPackageConfigs)) {
            $previousPackageConfigs = [$previousPackageConfigs];
        }

        foreach ($packages as $package) {

            // Get the old package config if provided. If there is no old config available, then use the new config (usually fist installation).
            $previousPackageConfig = $package;
            foreach ($previousPackageConfigs as $packageConfig) {
                if ($packageConfig->get('name') == $package->get('name')) {
                    $previousPackageConfig = $packageConfig;
                    break;
                }
            }

            App::trigger('package.enable', [$package]);

            if (!$current = App::config('system')->get('packages.' . $previousPackageConfig->get('module'))) {
                $current = $this->doInstall($package);
            }

            $scripts = $this->getScripts($package, $current);
            if ($scripts->hasUpdates()) {
                $scripts->update();
            }

            $version = $this->getVersion($package);
            App::config('system')->set('packages.' . $package->get('module'), $version);

            $scripts->enable();

            if ($package->getType() == 'foxkit-theme') {
                App::config('system')->set('site.theme', $package->get('module'));
            } elseif ($package->getType() == 'foxkit-extension') {
                App::config('system')->push('extensions', $package->get('module'));
            }
        }
    }

    /**
     * @param $packages
     */
    public function disable($packages)
    {
        if (!is_array($packages)) {
            $packages = [$packages];
        }

        foreach ($packages as $package) {
            $this->getScripts($package)->disable();

            if ($package->getType() == 'foxkit-extension') {
                App::config('system')->pull('extensions', $package->get('module'));
            }
        }
    }

    /**
     * @param  array $package
     * @param  string $current
     * @return PackageScripts
     */
    protected function getScripts($package, $current = null)
    {
        if (!$scripts = $package->get('extra.scripts')) {
            return new PackageScripts(null, $current);
        }

        if (!$path = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        return new PackageScripts($path . '/' . $scripts, $current);
    }

    /**
     * @param  $package
     * @return string
     */
    protected function doInstall($package)
    {
        $this->getScripts($package)->install();
        $version = $this->getVersion($package);

        App::config('system')->set('packages.' . $package->get('module'), $version);

        return $version;
    }

    /**
     * Tries to obtain package version from 'composer.json' or installation log.
     *
     * @param  $package
     * @return string
     */
    protected function getVersion($package)
    {
        if (!$path = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        if (!file_exists($file = $path . '/composer.json')) {
            throw new \RuntimeException(__('\'composer.json\' is missing.'));
        }

        $package = json_decode(file_get_contents($file), true);
        if (isset($package['version'])) {
            return $package['version'];
        }

        if (file_exists(App::get('path.packages') . '/composer/installed.json')) {
            $installed = json_decode(file_get_contents($file), true);

            foreach ($installed as $package) {
                if ($package['name'] === $package->getName()) {
                    return $package['version'];
                }
            }
        }

        return '0.0.0';
    }
}
