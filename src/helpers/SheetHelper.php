<?php

namespace app\helpers;

use Google\Client;
use app\helpers\ConfigHelper;

use UnexpectedValueException;

class SheetHelper {

    public string $api_name;
    public string $api_key;
    public string $sheet_id;
    public string $range;
    public string $suspected_range;
    public string $domains_col_symbol;
    public string $suspected_col_symbol;

    public Client $client;
    public \Google_Service_Sheets $service;

    private function init(): bool {
        $this->client = new Client();
        $this->client->setApplicationName($this->api_name);
        $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig(dirname(__DIR__, 2).'/'.'credentials.json');
        
        $this->service = new \Google_Service_Sheets($this->client);

        return true;
    } 

    function __construct() {
        $conf = new ConfigHelper();
        $this->api_name = $conf->get_config()['API_NAME'];
        $this->api_key = $conf->get_config()['API_KEY'];
        $this->sheet_id = $conf->get_config()['SHEET_ID'];
        $this->range = $conf->get_config()['RANGE'];
        $this->suspected_range = $conf->get_config()['SUSPECTED_RANGE'];
        $this->domains_col_symbol = $conf->get_config()['COLUMN_SYMBOL'];
        $this->suspected_col_symbol= $conf->get_config()['SUSPECTED_COLUMN_SYMBOL'];
        
        self::init();
    }

    public function get_domains(): array {
        $response = $this->service->spreadsheets_values->get($this->sheet_id, $this->range);
        $rows = $response->getValues();
        $headers = array_shift($rows);
        $array = [];
        foreach ($rows as $row) {
            $array[] = array_combine($headers, $row);
        }
        $domains_array = array();
        foreach ($array as $item) {
            array_push($domains_array, $item['domain']);
        }
        return $domains_array;
    }

    public function get_suspected_domains() {
        $response = $this->service->spreadsheets_values->get($this->sheet_id, $this->suspected_range);
        $rows = $response->getValues();
        $headers = array_shift($rows);
        $array = [];
        foreach ($rows as $row) {
            $array[] = array_combine($headers, $row);
        }
        $domains_array = array();
        foreach ($array as $item) {
            array_push($domains_array, $item['domain']);
        }
        return $domains_array;
    }

    public function clear_domains($count) {
        $request = new \Google_Service_Sheets_ClearValuesRequest();
        $this->service->spreadsheets_values->clear($this->sheet_id, $this->range.'!'.$this->domains_col_symbol.'2:'.$this->domains_col_symbol.$count+1, $request);
        return true;
    }

    public function update_domains($data) {
        $column = $this->domains_col_symbol;
        
        $range = $this->range.'!' . $column . '2'; 
        
        $body = new \Google_Service_Sheets_ValueRange([
            'majorDimension' => 'COLUMNS',
            'values' => [$data],
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $response = $this->service->spreadsheets_values->update($this->sheet_id, $range, $body, $params);

        return null;
    }

    public function update_suspected_domains($data) {
        $count = count($this->get_suspected_domains())+2;

        $column = $this->suspected_col_symbol;
        $range = $this->suspected_range.'!' . $column . $count; 
        
        $body = new \Google_Service_Sheets_ValueRange([
            'majorDimension' => 'COLUMNS',
            'values' => [$data],
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $response = $this->service->spreadsheets_values->update($this->sheet_id, $range, $body, $params);

        return null;
    }
}