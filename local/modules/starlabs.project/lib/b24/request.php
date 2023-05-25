<?php
namespace Starlabs\Project\B24;

use Bitrix\Main\Web\HttpClient;
use Remark\Tools\p;

class Request
{
	public static $URL = 'https://4hvostru.bitrix24.ru/rest/1/1qh2zsubqmfe22e4/';
	/**
	 * @var HttpClient
	 */
	private $Http = null;

	/**
	 * @var Prototype
	 */
	private $Action;

	public function __construct($oAction)
	{
		$this->Action = $oAction;
		$this->Http = new HttpClient();
		$this->query();
	}

	public function response()
	{
		$response = json_decode($this->Http->getResult(),true);
		return $response;
	}

	private function query()
	{
        $params = $this->Action->getParams();
        $this->Http->query('POST',$this->getUrl(),$params);

        //region потворная отправка запроса через 2 секунды если уперлись в лимиты
        $error = json_decode($this->Http->getResult(), true)["error"];
        if ($error === 'QUERY_LIMIT_EXCEEDED') {
            sleep(2);
            $this->query();
        }
        //endregion
    }

    private function getUrl()
    {
	    $method = $this->Action->getMethod();
        return self::$URL . $method . '/';
    }
}
