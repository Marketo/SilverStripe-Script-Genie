<?php

/**
 * Extension to dataobjects that reacts to events to trigger a rebuild
 * of data
 *
 * @author marcus
 */
class GenieExtension extends DataExtension
{

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
        $map = $this->owner->toMap();
        if (method_exists($this->owner, 'genieMap')) {
            $map = $this->owner->genieMap();
        }
        return json_encode($map);
    }
}