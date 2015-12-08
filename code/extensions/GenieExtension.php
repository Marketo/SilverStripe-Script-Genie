<?php

/**
 * Extension to dataobjects that reacts to events to trigger a rebuild
 * of data
 *
 * @author marcus
 */
class GenieExtension extends DataExtension
{
	protected $jsonFields;
	
	public function setJSONFields($f) {
		$this->jsonFields = $f;
	}

    public function regenerateTypeData()
    {
        
    }

    public function onAfterWrite()
    {
        
    }
    
    public function onAfterDelete()
    {
        
    }

    public function onAfterPublish()
    {

    }

    public function onAfterUnpublish()
    {

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