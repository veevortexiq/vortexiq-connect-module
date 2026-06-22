<?php
namespace Vortexiq\Connect\Model;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Vortexiq\Connect\Helper\AbstractData;
use Laminas\Http\Request;
use Laminas\Http\Response;

class Activate extends DataObject
{
    /**
     * Localhost maybe not active via https
     * @inheritdoc
     */
    public const X247COMMERCE_ACTIVATE_URL = 'https://license.247commerce.co.uk/license.php';

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * Activate constructor.
     *
     * @param CurlFactory $curlFactory
     * @param array $data
     */
    public function __construct(
        CurlFactory $curlFactory,
        array $data = []
    ) {
        $this->curlFactory = $curlFactory;

        parent::__construct($data);
    }

    /**
     * Active
     *
     * @param array $params
     *
     * @return array
     */
    public function activate($params = [])
    {
        $result = ['success' => false];
        $curl = $this->curlFactory->create();
        if ($params['domain'] == '127.0.0.1' || $params['domain'] == 'localhost') {
            $curl->setConfig(["verifypeer" => false]);
        }
        $curl->write(
            \Laminas\Http\Request::METHOD_POST,
            self::X247COMMERCE_ACTIVATE_URL,
            '1.1',
            [],
            http_build_query($params, '', '&')
        );

        try {
            $resultCurl = $curl->read();

            if (empty($resultCurl)) {
                $result['message'] = __('Cannot connect to server. Please try again later.');
            } else {
                $responseBody = $this->extractBody($resultCurl);
                $result += AbstractData::jsonDecode($responseBody);
                if (isset($result['status']) && in_array($result['status'], [200, 201])) {
                    $result['success'] = true;
                }
                if (isset($result['status']) && in_array($result['status'], [400, 500])) {
                    $result['success'] = false;
                }
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }

        $curl->close();

        return $result;
    }

             /**
              * Func
              *
              * @param array $response_str
              *
              * @return array
              */
    public function extractBody($response_str)
    {
        $parts = preg_split('|(?:\r\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return $parts[1];
        }
        return '';
    }
}
