<?php

/**
 * @author marcus
 */
class ScriptGenieService
{
    /**
     *
     * @var type 
     */
    public $typeConfiguration = array();

    public $defaultPath = 'assets/scripts';

    public function generateScriptDataFor($type, $file = null, $stage = 'Live')
    {
        if ($stage && Object::has_extension($type, 'Versioned')) {
            Versioned::reading_stage($stage);
        }

        if (!class_exists($type)) {
            throw new Exception("Invalid type defined, no data generated");
        }

        $typeConfig = $this->configFor($type);
        
        $config = isset($typeConfig[$file]) ? $typeConfig[$file] : array();
        
        // TODO - allow for specifying things like strtotime things for dates in some manner
        $rules = isset($config['filter']) ? $config['filter'] : null;
        
        $list = $type::get();
        if (isset($config['generator'])) {
            $generator = Injector::inst()->create($config['generator']);
            if ($generator) {
                $list = $generator->getList();
            }
        }

        if ($rules) {
            $list = $this->applyRulesToList($list, $rules);
        }

        $template = isset($config['template']) ? $config['template'] : 'JsonSet';
        
        $setFields = isset($config['fields']) ? $config['fields'] : null;
        if ($setFields) {
            $setFields = explode(',', $setFields);
        }

        if (isset($config['limit'])) {
            $list = $list->limit($config['limit']);
        }

        $order = isset($config['order']) ? $config['order'] : 'ID DESC';
        $list = $list->sort($order);

        $list = $list->filterByCallback(function ($item) use ($setFields) {
            // extension check was done on the type earlier, here we're just being careful
            if ($item->hasExtension('ScriptGenieExtension') && $setFields) {
                $item->setJSONFields($setFields);
            }
            return $item->canView();
        });

        $data = ArrayData::create(array(
            'RootObject'    => isset($config['rootObject']) ? $config['rootObject'] : 'window',
            'Type'          => $type,
            'Items'         => $list,
        ));

        $output = $data->renderWith($template);

        return $output;
    }

    public function generateScriptFilesFor($type)
    {
        $typeConfig = $this->configFor($type);

        $files = array();
        foreach ($typeConfig as $target => $config) {
            $output = $this->generateScriptDataFor($type, $target);
            $target = $target == 'default' ? $target . '-' . $type . '.js' : $target;
            $targetFile = isset($config['target_path']) ? $config['target_path'] . DIRECTORY_SEPARATOR . $target : $this->defaultPath . DIRECTORY_SEPARATOR . $target;

            if (strlen($output) && $targetFile) {
                if ($targetFile{0} != '/') {
                    $files[] = $targetFile;
                    $targetFile = Director::baseFolder().DIRECTORY_SEPARATOR.$targetFile;
                } else {
                    // only record the basename if it's not a relative folder to the project
                    $files[] = basename($targetFile);
                }

                Filesystem::makeFolder(dirname($targetFile));
                file_put_contents($targetFile, $output);
            }
        }
        return $files;
    }
    
    /**
     * Applies a bunch of filters to a list, using some keywords for interpreting 
     * some dynamic things such as strtotime
     * 
     * @param type $list
     * @param type $rules
     */
    protected function applyRulesToList($list, $rules)
    {
        foreach ($rules as $field => $value) {
            $list = $list->filter($field, $this->ruleValue($value));
        }
        return $list;
    }
    
    /**
     * Applies rules to a particular value, continuously until no more
     * 
     * eg 
     * 
     * %date%strtotime|
     */
    protected function ruleValue($input)
    {
        $output = $input;
        if (preg_match('/%([a-z]+)\|([^%]*?)%/i', $input, $matches)) {
            // 1 = method, 2 = param
            $method = $matches[1] . 'Rule';
            $arg = $matches[2];
            if (method_exists($this, $method)) {
                $newInput = $this->$method($arg);
                $output = str_replace($matches[0], $newInput, $input);
                // now recurse
                $output = $this->ruleValue($output);
            }
        }
        return $output;
    }
    
    protected function strtotimeRule($input)
    {
        $bits = explode('|', $input);
        $time = strtotime($bits[0]);
        
        if (count($bits) > 1) {
            $time = date($bits[1], $time);
        }
        
        return $time;
    }

    protected function configFor($type)
    {
        $one = singleton($type);
        if (!$one->hasExtension('ScriptGenieExtension')) {
            throw new Exception("Please rub the genie's bottle for $type");
        }
        $classes = array_values(ClassInfo::ancestry($type));
        $classes = array_reverse($classes);
        
        $config = array('default' => array());
        foreach ($classes as $cls) {
            if (isset($this->typeConfiguration[$cls])) {
                $config = $this->typeConfiguration[$cls];
                break;
            }
        }
        
        return $config;
    }
}
