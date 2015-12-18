<?php

/**
 * serves generated JS files 
 *
 * @author marcus
 */
class ScriptGenieController extends Controller {
	
	private static $allowed_actions = array(
		'script'
	);
	
	private static $dependencies = array(
		'scriptService'		=> '%$ScriptGenieService'
	);
	
	/**
	 * @var ScriptGenieService
	 */
	public $scriptService;
	
	public function script() {
		// we don't allow access unless the user is logged in 
		if (!Member::currentUserID()) {
			return;
		}
		
		$type = $this->request->param('ID');
		$filename = $this->request->param('OtherID');
		
		$content = $this->scriptService->generateScriptDataFor($type, $filename, Versioned::current_archived_date());
		
		$this->response->addHeader('Content-type', 'text/javascript');
		
		return $content;
	}
}
