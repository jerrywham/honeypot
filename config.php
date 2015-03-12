<?php
/**
 * Plugin honeypot
 *
 * @package     PLX
 * @version     1.1
 * @date        02/07/2012
 * @author      Cyril MAGUIRE
 **/
 
	if(!defined('PLX_ROOT')) exit; 
	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	if(!empty($_POST)) {
		$plxPlugin->setParam('httpbl_api_key', $_POST['httpbl_api_key'], 'cdata');
		$plxPlugin->saveParams();
		header('Location: parametres_plugin.php?p=honeypot');
		exit;
	}
?>

<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<p><?php $plxPlugin->lang('L_CONFIG_DESCRIPTION') ?></p>

<form action="parametres_plugin.php?p=honeypot" method="post">
	<fieldset class="withlabel">
		<p><?php echo $plxPlugin->getLang('L_CONFIG_API_KEY') ?></p>
		<?php plxUtils::printInput('httpbl_api_key',plxUtils::strCheck($plxPlugin->getParam('httpbl_api_key')), 'text'); ?>

	</fieldset>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="submit" value="<?php echo $plxPlugin->getLang('L_CONFIG_SAVE') ?>" />
</form>