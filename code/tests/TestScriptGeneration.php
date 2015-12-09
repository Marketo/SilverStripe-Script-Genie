<?php

/**
 * @author marcus
 */
class TestScriptGeneration extends SapphireTest {
	protected $extraDataObjects = array('GenieTestObject');
	
	public function testGeneratedScript() {
		$this->logInWithPermission('ADMIN');
		
		$data = GenieTestObject::create(array(
			'Title'		=> 'Titleone',
			'FirstField'	=> 'OneFieldone',
			'SecondField'	=> 'TwoFieldone',
		));
		$data->write();
		
		$data = GenieTestObject::create(array(
			'Title'		=> 'Titletwo',
			'FirstField'	=> 'OneFieldtwo',
			'SecondField'	=> 'TwoFieldtwo',
		));
		$data->write();
		
		$svc = new GenieScriptService();
		$svc->typeConfiguration = array(
			'GenieTestObject' => array(
				'default'		=> array(
					'target_path'	=> __DIR__.'/data'
				)
			)
		);
		
		$svc->generateScriptFilesFor('GenieTestObject');
		$generated = __DIR__.'/data/default-GenieTestObject.js';
		$this->assertTrue(file_exists($generated));
		
		unlink($generated);
	}
	
}

class GenieTestObject extends DataObject implements TestOnly {
	private static $db = array(
		'Title'		=> 'Varchar',
		'FirstField'	=> 'Varchar',
		'SecondField'	=> 'Varchar',
	);
	
	private static $extensions = array('GenieExtension');
}