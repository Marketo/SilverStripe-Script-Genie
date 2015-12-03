<?php

/**
 * @author marcus
 */
class GenieScriptService
{
    public $typeConfiguration = array();

    public $filterRules = array();

    public $dataTemplates = array();

    public $defaultPath = 'assets/scripts';
    
    public function generateScriptDataFor($type) {
        if (!class_exists($type)) {
            throw new Exception("Invalid type defined, no data generated");
        }

        $config = isset($this->typeConfiguration[$type]) ? $this->typeConfiguration[$type] : array();

        $rules = isset($config['filter']) ? $config['filter'] : null;

        $list = $type::get();

        if ($rules) {
            $list = $list->filter($rules);
        }

        $template = isset($config['templates']) ? $config['templates'] : 'JsonSet';

        $targetFile = isset($config['target']) ? $config['target'] : $this->defaultPath . DIRECTORY_SEPARATOR . $type . '.js';

        
    }
}