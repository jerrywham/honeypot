<?php
/**
* Classe honeypot
*
* @version 1.1
* @date	03/07/2012
* @author	Cyril MAGUIRE
**/
class honeypot extends plxPlugin {

	/**
	* Constructeur de la classe
	*
	* @return	null
	* @author	Cyril MAGUIRE
	**/	
	public function __construct($default_lang) {
		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Ajouts des hooks
		$this->addHook('plxMotorDemarrageBegin', 'plxMotorDemarrageBegin');
	}

	/**
	* Méthode qui insère le projet Honeypot, antispam
	*/
	public function plxMotorDemarrageBegin() {
		define('HTTPBL_API_KEY',plxUtils::strCheck($this->getParam('httpbl_api_key')));
		require_once PLX_PLUGINS.'honeypot/httpbl.php';
	}
}
?>