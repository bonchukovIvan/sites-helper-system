<?php 

    $DOMAINS_CSV_URL = $argv[0];
    $SLACK_TOKEN = $argv[1];
    $SLACK_CHANNEL = $argv[2];
    $MAIL_TO = $argv[3];

    function slack($message, $channel){
        $ch = curl_init("https://slack.com/api/chat.postMessage");
        $data = http_build_query([
            "token" => "",
            "channel" => $channel,
            "text" => $message,
            "username" => "sites-alarm",
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function ends_with($haystack, $needle) {
        return substr($haystack, -strlen($needle)) === $needle;
    }
    function starts_with($haystack, $needle) {
        return substr($haystack, strlen($needle)) === $needle;
    }

    function get_domains_from_csv($url) {
        $s = '';
        if (($handle = fopen($url, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $s .= ',' . $data[$c];
                }
            }
            fclose($handle);
        }

        $domains = array();
        
        foreach(str_getcsv($s) as $item) { 
            if (!ends_with($item, '.edu.ua')) {
                continue;
            }
            if (!starts_with($item,'https') || !starts_with($item,'http')) {
                array_push($domains, 'https://'.$item);
            }
            else {
                array_push($domains, $item);
            }  
        }
        return $domains;
    }

    function create_alarm_message($suspected_domains) {
        $str = '';
        $str .= date('m/d/Y h:m', time()) . ' Report' . PHP_EOL;
        $str .= '[OTHER SERVER] Suspected sites: '. count($suspected_domains) . PHP_EOL;
        if (!$suspected_domains) {
            return 'All sites works correctly!';
        }
        foreach($suspected_domains as $key => $value) {
            $str .= $key . ' => Status code: ' . $value .PHP_EOL;
        }
        return $str;
    }

    function generate_log_name() {
        return __DIR__ . '/logs' . '/log_' . date('m.d.Y_H.i.s', time()).'.log';
    }

    function write_to_log($domains) {
        $log = '';
        $i = 1;
        foreach($domains as $key => $value) {
            $log .= $i. '. '.' ' . $key . ' => Status code: ' . $value .PHP_EOL;
            $i++;
        }
        if (file_put_contents(generate_log_name(), $log, FILE_APPEND)) {
            return true;
        };
        return false;
    }

    function send_to_mail($domains) {
        $to = 'bonchukooov@gmail.com';
        $subject = 'Domains check report';
        $message = '';
        $message .= 'Suspected domains: '. PHP_EOL;
        $i = 0;
        if (empty($domains)) {
            $message = 'All sites works correctly!';
            return mail($to, $subject, $message);
        }
        foreach($domains as $key => $value) {
            $message .= $i. '. '.' ' . $key . ' => Status code: ' . $value .PHP_EOL;
            $i++;
        }
        return mail($to, $subject, $message);
    }

    function get_handles($domains) {
        $handles = array();
        foreach($domains as $domain) {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $domain);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handle, CURLOPT_VERBOSE, false);
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
            if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
                curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }

            $handles[$domain] = $handle;
        }
        return $handles;
    }
    function add_handles_to_multi($mh, $handles) {
        foreach($handles as $handle) {
            curl_multi_add_handle($mh, $handle);
        }
    }

    function get_http_codes($handles) {
        $http_array = array();
        foreach($handles as $domain => $handle) {
            $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            if (!$http_code) {
                $http_code = 500;
            }
            $http_array[$domain] = $http_code;
        }
        return $http_array;
    }
    
    function close_handle($mh, $handles) {
        foreach($handles as $handle) {
            curl_multi_remove_handle($mh, $handle);
        }
        curl_multi_close($mh);
    }

    function create_suspected_array($http_array) {
        $suspected_array = array();
        foreach($http_array as $domain => $http_code) {
            
            if($http_code >= 500) {
                $suspected_array[$domain] = $http_code;
            }
        }
        return $suspected_array;
    }

    function main($DOMAINS_CSV_URL) {  
        date_default_timezone_set('Etc/GMT-3');
        if (ini_get('max_execution_time') >=30) {
            ini_set('max_execution_time', 900);
        }

        $domains = get_domains_from_csv($DOMAINS_CSV_URL); 
        if (!$domains) {
            echo 'Domains list is empty :(';
            die();
        }

        $handles = get_handles($domains);
    
        $mh = curl_multi_init();
    
        add_handles_to_multi($mh, $handles);
    
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while($running > 0);
    
        $http_array = get_http_codes($handles);
        arsort($http_array);

        close_handle($mh, $handles);

        $suspected_array = create_suspected_array($http_array);
        
        write_to_log($http_array);    
    }

    main($TEST_URL);