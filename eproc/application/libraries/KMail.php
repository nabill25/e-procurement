<?php
/**
 * @package     eProcurement Application
 * @author      eproc2025
 * @since       25. Version 3.1
 *
 */

//include_once 'class.phpmailer.php';
require 'mail/PHPMailerAutoload.php';

class KMail extends PHPMailer{
    function __construct($exceptions = false) {
        parent::__construct($exceptions);

        $this->IsSMTP();
		$this->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

    $this->SMTPDebug = 0;
    //Ask for HTML-friendly debug output
    $this->Host     = "salamander.ui.ac.id";
    $this->Port     = 587; // 465 or 587
    $this->SMTPAuth = TRUE;
    $this->Username = 'aplikasi.eproc';
    $this->Password = 'tef7tex0';

    $this->From     = "noreply@ui.ac.id";
    $this->FromName = "Sistem Pengadaan Barang Jasa";
    $this->SMTPSecure  = "tls";

    $this->WordWrap = 50;
    $this->Priority = 1;
    $this->CharSet = "UTF-8";
    $this->IsHTML(TRUE);
    $this->AltBody    = "To view the message, please use an HTML compatible email viewer!";

    }
}

?>
