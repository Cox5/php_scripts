<?php 

if (empty($_GET)) { // if $_GET is empty, form is shown and ready for input

?>

<h3> Office 365 DNS Configurator</h3>

<table width="600">
    <form action="dns_handler_new.php" method="get">
        Domain: <input type="text" name="domain" placeholder="example.com"><br>
        MS = <input type="text" name="ms" placeholder="ms000000"><br>
        <input type="submit">
    </form>
</table>


<?php

} else { 

    $domain =  $_GET["domain"]; 
    $ms =  $_GET["ms"];  
    $hyphen_domain = str_replace('.', '-', $domain);

    $string = 
                "  
                    # Record Type TXT 
                    %%DOMAIN%%.   3600  TXT   MS=%%MS%%  
                    %%DOMAIN%%.   3600  TXT   \"v=spf1 include:spf.protection.outlook.com -all\" 
                    # Record Type MX 
                    %%DOMAIN%%.   300   MX    0 %%HYPHENURL%%.mail.protection.outlook.com. 
                    # Record Type CNAME 
                    autodiscover             3600  CNAME autodiscover.outlook.com. 
                    sip                      3600  CNAME sipdir.online.lync.com. 
                    lyncdiscover             3600  CNAME webdir.online.lync.com. 
                    msoid                    3600  CNAME clientconfig.microsoftonline-p.net. 
                    enterpriseregistration   3600  CNAME enterpriseregistration.windows.net. 
                    enterpriseenrollment     3600  CNAME enterpriseenrollment.manage.microsoft.com. 
                    # Record type SRV
                    _sip._tls                3600  SRV   100 1 443   sipdir.online.lync.com. 
                    _sipfederationtls._tcp   3600  SRV   100 1 5061  sipfed.online.lync.com. 
                ";

    if($domain != null && $ms != null) {
        
        $string = preg_replace('/%%DOMAIN%%/', $domain, $string);
        $string = preg_replace('/%%MS%%/', $ms, $string);
        $string = preg_replace('/%%HYPHENURL%%/', $hyphen_domain, $string);
        
    } else {
        echo "Fields are empty!";
    }

	
        $extension = ".txt";
        $filename = $domain.$extension;

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . mb_strlen($string, '8bit')); 
        echo($string);
        exit;


}
?>