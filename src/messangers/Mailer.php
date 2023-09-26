<?php

namespace app\messangers;

use app\messangers\BaseMessanger;

class Mailer extends BaseMessanger {

    protected $lang_section = 'mail_section';
    public function send_to_mail($suspected_domains, $to) { 
        $message = self::create_report_message($suspected_domains);
        $subject = self::get_localization()['REPORT_SUBJECT'];
        return mail($to, $subject, $message); 
    } 
}