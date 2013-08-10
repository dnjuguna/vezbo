<?php

namespace helpers;

class TranslationException extends \Exception{};

class Translation {
	
	protected $sandbox = NULL;
		
	protected $translations = NULL;
	
	public function __construct(&$sandbox) {
		$this->sandbox = &$sandbox;
		$this->init();
	}
	
	public function translate($index = NULL){
		$key = (string) $index;
		if(array_key_exists($key, $this->translations)){
			return (string) $this->translations[$key]; 
		} else {
			return str_replace('.label', '', str_replace('.title', '', str_replace('.placeholder', '', $key)));
		}
	}
	
	public function init(){
		$source = $this->getSource();
		foreach($source->label as $translation){
			$index = (string) $translation->attributes()->index;
			$this->translations[$index] = (string) $translation;
		}
	}
	
	protected function getSource(){
		$site = $this->sandbox->getMeta('site');
		$settings = $this->sandbox->getMeta('settings');
		$home = $site['home'];
		$base = $this->sandbox->getMeta('base');
		$language = $settings['language'];
		$filename = "$base/sites/$home/$language.xml";
		if(!file_exists($filename)) {
			throw new TranslationException("Locale '{$filename}' not found");
		}
		$locale = simplexml_load_file($filename);
		if(!$locale) {
			throw new TranslationException("Locale '{$filename}' is not a valid XML file");
		}
		return $locale;
	}
	
	public function getLocale(){
		return $this->translations;
	}
}

?>