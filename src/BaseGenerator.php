<?php

namespace PluginListGenerator;

class BaseGenerator
{

    protected $pluginInformation;
    protected $columnsDescriptions;
    protected $statuses;
    protected $input;
    protected $plugins;
    protected $activePlugins;
    protected $inactivePlugins;
    protected $updatePlugins;
    protected $muPlugins;

    public function __construct($input, $columns_descriptions)
    {
        $this->pluginInformation = $this->getSpecificData($input, 'include-');

        $this->columnsDescriptions = $columns_descriptions;
        $this->statuses = $this->getSpecificData($input, 'status-');
        $this->input = $input;
        $this->includeMuPlugins = isset($input['mu-plugins-included']);

        $this->getPlugins();

    }

    protected function getSpecificData($data, $includes)
    {

        return array_filter($data, function($key) use($includes) {
           return strpos($key, $includes) === 0;
        }, ARRAY_FILTER_USE_KEY);

    }

    protected function getPlugins()
    {

        $this->plugins = get_plugins();

        if(!$this->input['add-this-plugin'])
            unset($this->plugins['plugin-list-generator/plugin-list-generator.php']);

        $this->activePlugins   = [];
        $this->inactivePlugins = [];
        $this->updatePlugins   = [];
        $this->muPlugins       = get_mu_plugins();

        foreach($this->plugins as $key => $val){

            if (is_plugin_inactive($key)) {
                $this->inactivePlugins[$key] = $val;
            } else {
                $this->activePlugins[$key] = $val;
            }

            if (isset(get_site_transient( 'update_plugins' )->response[$key])) {
                $this->updatePlugins[$key] = $val; 
            }

        }

    }

}