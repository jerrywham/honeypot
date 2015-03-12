<?php if(!defined('PLX_ROOT')) exit; ?>
<h2>Aide</h2>
<p>Fichier d&#039;aide du plugin HoneyPot</p>
<h3>Projet HoneyPot c'est quoi ?</h3>
<p>(Extrait du <a href="http://sebsauvage.net/wiki/doku.php?id=project_honeypot" onclick="window.open(this.href);return false;">wiki de sebsauvage</a>)</p>
<blockquote>
Project HoneyPot est un projet collaboratif pour aider à repérer et bloquer les spammeurs (voir ces deux articles: <a href="http://www.sebsauvage.net/rhaa/index.php?2011/06/27/13/17/04-project-honeypot-une-alternative-a-akismet" onclick="window.open(this.href);return false;">lien1</a> <a href="http://sebsauvage.net/rhaa/index.php?2011/08/01/07/13/04-retour-sur-project-honeypot" onclick="window.open(this.href);return false;">lien2</a>). Notez que vous n'avez pas l'obligation de participer au repérage des spammeurs. Vous pouvez juste profiter de la protection de Project Honeypot.<br/>

La petite bibliothèque php (incluse dans ce plugin) vous permettra de bloquer les spammeurs de votre site en utilisant Project Honeypot tout en prévenant les internautes d'une éventuelle infection de leur PC. Cela ne bloquera pas leur navigation: Ils auront la possibilité de continuer à naviguer sur le site en prouvant qu'ils sont bien des humains.
</blockquote>
<p>&nbsp;</p>
<h3>Installation</h3>
<p>Glissez le dossier honeypot/ dans le dossier plugins/.</p>
<p>Inscrivez-vous à <a href="http://www.projecthoneypot.org/" onclick="window.open(this.href);return false;">Project Honeypot</a> : C'est obligatoire pour obtenir une clé d'API. La bibliothèque ci-dessous ne fonctionnera pas sans la clé. Cette inscription est gratuite.
La clé est dans votre dashboard: Une fois connecté sur le site du projet, cliquez sur “Home” > “Dashboard” et regardez ”Your http:BL API key:”. La clé d'activation peut également être donnée dans le mail d'activation lors de votre inscription.</p>
<p><strong>Recopiez cette clé dans le fichier de configuration du plugin</strong>.</p>
<p>À la racine de votre site, vous pouvez consulter le fichier httpbl.txt qui enregistre tous les blocages et déblocage.</p>
<p>Pour plus d'informations, je vous renvoie à la <a href="http://sebsauvage.net/wiki/doku.php?id=project_honeypot" onclick="window.open(this.href);return false;">page du projet</a>.</p>
<p>Enjoy !</p>