<?php
    /*
    Project Honeypot protection library.
    Version 1.4 SSE.
     
    Purpose: This library protects a website against comment and email spammers,
             while warning the user he may be infected.
    Real users can continue to surf by confirming they are humain. Robots are blocked.
    It uses the Project HoneyPot http:BL API to detect spamming IP addresses.
     
    - You http:BL API key must be inserted below
      (You must register on the projecthoneypot.org website.)
    - To protect a page, simply do: require_once 'httpbl.php';
     
    This library is derived from:
    http://planetozh.com/blog/my-projects/honey-pot-httpbl-simple-php-script/ 
    */
    //define('HTTPBL_API_KEY','XXXXXXXXXXXXXXX');
    ob_start();
    if(!session_id()) session_start();
    if (isset($_COOKIE['notabot']))
    { 
        httpbl_logme(); 
        if ($_SESSION['httpbl']['activity']<8 && strpos($_SERVER['HTTP_USER_AGENT'],'Windows'))
            httpbl_infected();  // Only display infection banner if last spam was less than 8 days ago.
    } 
    else 
    { 
        httpbl_check(); 
    }
     
    function httpbl_lang()
    {
        // We try to auto-detect language (english by default)
        foreach(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']) as $language)
            { if ($language=='fr') { return 'fr'; } }
        return 'en';
    }
     
    function httpbl_infected() // Display infection message.
    {
        echo <<<HTML
    <style type="text/css">
    <!--
    #httpbl_warning { text-align:center; border:1px solid black; background-color:#39678E; color:white;}
    #httpbl_warning td {  color:white; font:13px sans-serif; padding:0 0 0 7px;}
    #httpbl_warning td#sign {  color:yellow;font-family:serif; font-size:42pt; font-weight:bold;padding:0 10 0 14; }
    #httpbl_warning td#note {  color:#ddd; font-size:11px;}
    #httpbl_warning a {  color:#AAE0AA; text-decoration:none; }
    #httpbl_warning a:hover {  color:#FFFFBE;  }
    -->
    </style>  
HTML;
        $ip=$_SERVER['REMOTE_ADDR'];
        $days = $_SESSION['httpbl']['activity'];
        if (httpbl_lang()=='fr')
        {
            $daysmsg=($days>1?" il y a $days jours":'');
            echo <<<HTML
    <div id="httpbl_warning">       
    <table align="center"><tr>
    <td id="sign" rowspan="2">!</td>
    <td><b>Votre adresse IP a &eacute;t&eacute; d&eacute;tect&eacute;e comme &eacute;mettant du <a href="http://www.projecthoneypot.org/ip_$ip">spam</a>$daysmsg. Il est possible que votre ordinateur soit infect&eacute;.<br>
    Merci de vous en assurer en utilisant un de ces antivirus gratuits: <a href="https://www.microsoft.com/security/scanner/fr-fr/">Microsoft Safety Scanner</a>, 
    <a href="http://housecall.trendmicro.com/fr/">HouseCall TrendMicro</a> ou <a href="http://www.malwarebytes.org/mbam-download.php">Malwarebytes AntiMalware</a>.</b></td>
    <td id="note" style="width:20%;">Ce message s'affiche car ce site web participe &agrave; <a href="http://www.projecthoneypot.org/">Project Honeypot</a> pour la lutte contre le spam.</td>
    </tr>
    <tr><td id="note">Ce message continuera &agrave; s'afficher plusieurs jours apr&egrave;s une &eacute;ventuelle d&eacute;sinfection de votre ordinateur.</td></tr>
    </table>
    </div>
HTML;
        }
        else
        {
            $daysmsg=($days>1?" $days days ago":'');
            echo <<<HTML
    <div id="httpbl_warning">       
    <table align="center"><tr>
    <td><b>Your IP address has been detected as <a href="http://www.projecthoneypot.org/ip_$ip">spammer</a>$daysmsg. Your computer may be infected.<br>
    Please use one of these free antiviruses: <a href="https://www.microsoft.com/security/scanner/">Microsoft Safety Scanner</a>, 
    <a href="http://housecall.trendmicro.com/">HouseCall TrendMicro</a> or <a href="http://www.malwarebytes.org/mbam-download.php">Malwarebytes AntiMalware</a>.</b></td>
    <td id="note" style="width:20%;">You see this message because this website takes part in <a href="http://www.projecthoneypot.org/">Project Honeypot</a> to fight spam.</td>
    </tr>
    <tr><td id="note">This message will continue to show up several days after the disinfection of your computer.</td></tr>
    </table>
    </div>
HTML;
        }
    }
     
    function httpbl_check() {    
        $ip = $_SERVER['REMOTE_ADDR'];
     
        // build the lookup DNS query
        // Example : for '127.9.1.2' you should query 'abcdefghijkl.2.1.9.127.dnsbl.httpbl.org'
        $lookup = HTTPBL_API_KEY . '.' . implode('.', array_reverse(explode ('.', $ip ))) . '.dnsbl.httpbl.org';
     
        // check query response
        $result = explode( '.', gethostbyname($lookup));
     
        if ($result[0] == 127) 
        {
            // Query successful !
            $a = array('activity'=>$result[1], 'threat'=>$result[2], 'type'=>$result[3]);
            $typemeaning='';
            if ($a['type'] & 0) $typemeaning .= 'Search Engine, ';
            if ($a['type'] & 1) $typemeaning .= 'Suspicious, ';
            if ($a['type'] & 2) $typemeaning .= 'Harvester, ';
            if ($a['type'] & 4) $typemeaning .= 'Comment Spammer, ';
            $a['typemeaning'] = trim($typemeaning,', ');
     
            // Now determine some blocking policy
            $a['block']=0;
            if (
                ($a['type'] >= 4 && $a['threat'] > 0) // Comment spammer with any threat level
                ||
                ($a['type'] < 4 && $a['threat'] > 20) // Other types, with threat level greater than 20
               ) 
            {
                $a['block'] = 1;
            }
     
            $_SESSION['httpbl']=$a;
     
            if ($a['block']!=0) {
                httpbl_logme();
                httpbl_blockme();
                exit();
            }
        }
    }
     
    function httpbl_logme() {
        $log = fopen($_SERVER["DOCUMENT_ROOT"].'/httpbl.txt','a');
        $stamp = date('Y-m-d :: H-i-s');
        $page = $_SERVER['REQUEST_URI'];
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (!isset($_COOKIE['notabot'])) {
            fputs($log,"$stamp :: BLOCKED ".$_SERVER['REMOTE_ADDR']." :: ".$_SESSION['httpbl']['type']." :: ".$_SESSION['httpbl']['threat']." :: ".$_SESSION['httpbl']['activity']." :: $page :: $ua\n");
        } else {
            fputs($log,"$stamp :: UNBLCKD ".$_SERVER['REMOTE_ADDR']." :: $page :: $ua\n");
        }
        fclose($log);
    }
     
     
    function httpbl_blockme() {
        header('HTTP/1.0 403 Forbidden');
        echo '<!DOCTYPE html><html><body>';
        httpbl_infected();
        echo <<<HTML
        <script type="text/javascript">
        function setcookie( name, value, expires, path, domain, secure ) {
            // set time, it's in milliseconds
            var today = new Date();
            today.setTime( today.getTime() );
     
            if ( expires ) {
                expires = expires * 1000 * 60 * 60 * 24;
            }
            var expires_date = new Date( today.getTime() + (expires) );
     
            document.cookie = name + "=" +escape( value ) +
            ( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + 
            ( ( path ) ? ";path=" + path : "" ) + 
            ( ( domain ) ? ";domain=" + domain : "" ) +
            ( ( secure ) ? ";secure" : "" );
        }    
        function letmein() {
            setcookie('notabot','true',1,'/', '', '');
            location.reload(true);
        }
        </script>
        <br>
HTML;
     
        if (httpbl_lang()=='fr')
        { echo '<div style="font:14px sans-serif;">Pour continuer la navigation, merci de cliquer sur <a href="javascript:letmein()">ce lien</a>. D&eacute;sol&eacute; du d&eacute;rangement.</div>'; }
        else
        { echo '<div style="font:14px sans-serif;">Please click <a href="javascript:letmein()">this link</a> to continue. Sorry for the inconvenience.</div>'; }    
        echo '</body></html>';
    }
    ob_end_flush();
?>