<?php

/**
 * Bound to model admin classes so that contained model admin listings can trigger
 * a regeneration of the data
 * 
 * @author marcus
 */
class GenieAdminExtension extends Extension
{
	private static $allowed_actions = array('regenerate');
	private $modelClass;
	
	/**
	 * @var GenieScriptService
	 */
	public $scriptService;
	
    public function updateEditForm(Form $form) {
		$sng = singleton($this->modelClass());
		if ($sng->hasExtension('GenieExtension')) {
			$form->Actions()->push(FormAction::create('regenerate', 'Regenerate Data'));
		}
	}
	
	protected function modelClass() {
		if ($this->modelClass) {
			return $this->modelClass;
		}
		$models = $this->owner->getManagedModels();

		if($this->owner->getRequest()->param('ModelClass')) {
			$this->modelClass = str_replace('-', '\\', $this->owner->getRequest()->param('ModelClass'));
		} else {
			reset($models);
			$this->modelClass = key($models);
		}
		return $this->modelClass;
	}
	
	public function regenerate($data, Form $form) {
		$form->sessionMessage('Regenerated script files', 'good');
		$class = $this->modelClass();
		$this->scriptService->generateScriptFilesFor($class);
		$this->owner->redirectBack();
	}
}