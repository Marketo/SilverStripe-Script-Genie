<?php

/**
 * @author marcus
 */
class GenieScriptService
{
    /**
     *
     * @var type 
     */
    public $typeConfiguration = array();

    public $defaultPath = 'assets/scripts';


    public function generateScriptDataFor($type) {
        if (!class_exists($type)) {
            throw new Exception("Invalid type defined, no data generated");
        }

        $config = $this->configFor($type);

        // TODO - allow for specifying things like strtotime things for dates in some manner
        $rules = isset($config['filter']) ? $config['filter'] : null;

        $list = $type::get();

        if ($rules) {
            $list = $list->filter($rules);
        }

        $template = isset($config['template']) ? $config['template'] : 'JsonSet';

        $order = isset($config['order']) ? $config['order'] : 'ID DESC';
        $list = $list->sort($order);

        $list = $list->filterByCallback(function ($item) {
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

    public function generateScriptFileFor($type) {
        $output = $this->generateScriptDataFor($type);
        $targetFile = isset($config['target']) ? $config['target'] : $this->defaultPath . DIRECTORY_SEPARATOR . $type . '.js';
        
        if (strlen($output) && $targetFile) {
            if ($targetFile{0} != '/') {
                $targetFile = Director::baseFolder().DIRECTORY_SEPARATOR.$targetFile;
            }
            Filesystem::makeFolder(dirname($targetFile));
            return file_put_contents($targetFile, $output);
        }
    }

    protected function configFor($type) {
        $config = isset($this->typeConfiguration[$type]) ? $this->typeConfiguration[$type] : array();
        return $config;
    }
}