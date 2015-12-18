<?php

/**
 * Extension to dataobjects that reacts to events to trigger a rebuild
 * of data
 *
 * @author marcus
 */
class ScriptGenieExtension extends DataExtension
{
	protected $jsonFields;
	
	public function setJSONFields($f) {
		$this->jsonFields = $f;
	}

    public function regenerateTypeData()
    {
        if (Config::inst()->get($this->owner->class, 'regenerate_scripts')) {
			singleton('ScriptGenieService')->generateScriptFilesFor($this->owner->class);
		}
    }

    public function onAfterWrite()
    {
        $this->regenerateTypeData();
    }
    
    public function onAfterDelete()
    {
        $this->regenerateTypeData();
    }

    public function onAfterPublish()
    {
		$this->regenerateTypeData();
    }

    public function onAfterUnpublish()
    {
		$this->regenerateTypeData();
    }

    public function AsJSON() {
		$map = array();
		if (method_exists($this->owner, 'genieMap')) {
            $map = $this->owner->genieMap();
        } else if (isset($this->jsonFields)) {
			 foreach ($this->jsonFields as $f) {
				 $f = trim($f);
				 $map[$f] = $this->owner->$f;
			 }
		} else {
			$map = $this->owner->toMap();
		}
        
        return json_encode($map);
    }
}