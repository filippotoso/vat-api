<?php

namespace FilippoToso\VatApi;

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;

class VatApi
{

    const REQUEST_COUNTRY = 'LU';
    const REQUEST_VAT_ID = '26375245';

    /**
     * The Guzzle HTTP client
     * @var [type]
     */
    protected $client;

    /**
     * Create a new VatApi instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client;
    }

    /**
     * Get the Vat ID details
     * @method get
     * @param  string $vat The current vat id
     * @return array
     */
    public function details($vat) {

        $vat = trim($vat);

        $valid = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES', 'FI', 'FR', 'GB', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
        $pattern = sprintf('#^(%s)(.*)#si', implode('|', $valid));
        if (!preg_match($pattern, $vat, $matches)) {
            throw new \Exception('Invalid VAT ID format');
        }

        $html = $this->get($matches[1], $matches[2]);

        return $this->parse($html);

    }

    /**
     * Get the detauls from the VIES database
     * @method get
     * @param  string $country_id The vat id country id
     * @param  string $vat_id The current vat id
     * @return string
     */
    protected function get($country_id, $vat_id) {

        $response = $this->client->post('http://ec.europa.eu/taxation_customs/vies/vatResponse.html', [
            'form_params' => [
                'action' => 'check',
                'check' => 'Verificare',
                'memberStateCode' => $country_id,
                'number' => $vat_id,
                'requesterMemberStateCode' => static::REQUEST_COUNTRY,
                'requesterNumber' => static::REQUEST_VAT_ID,
                'traderCity' => '',
                'traderName' => '',
                'traderPostalCode' => '',
                'traderStreet' => '',
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Referer' => 'http://ec.europa.eu/taxation_customs/vies/vatRequest.html',
            ],
        ]);
        return (string) $response->getBody();

    }

    /**
     * Parse the HTML content
     * @method parse
     * @param  string $html The html response
     * @return array
     */
    protected function parse($html) {

        $dom = HtmlDomParser::str_get_html($html);

        $result = [
            'valid' => null,
            'vat_id' => '',
            'name' => '',
            'addresses' => '',
            'address' => '',
            'error' => FALSE,
        ];

        $trs = $dom->find('#vatResponseFormTable tr');

        foreach($trs as $tr) {

            $tds = $tr->find('td');

            if (count($tds) == 1) {

                $current = trim($tds[0]->plaintext);

                if (starts_with($current, 'Yes, valid VAT number')) {
                    $result['valid'] = TRUE;
                } elseif (starts_with($current, 'No, invalid VAT number')) {
                    $result['valid'] = FALSE;
                } elseif (starts_with($current, 'Request time-out')) {
                    $result['error'] = 'Request time-out';
                }

            } elseif (($result['valid']) && (count($tds) == 2)) {

                $name = trim($tds[0]->plaintext);
                $value = trim($tds[1]->plaintext);

                $fields = [
                    'VAT Number' => 'vat_id',
                    'Name' => 'name',
                    'Address' => 'addresses',
                ];

                foreach ($fields as $field => $key) {
                    if ($name == $field) {
                        $result[$key] = $value;
                    }
                }

            }

        }

        $result['addresses'] = empty($result['addresses']) ? [] : explode("\r\n", $result['addresses']);
        $result['address'] = preg_replace('#\s+#', ' ', implode(' ', $result['addresses']));

        return $result;

    }

}
