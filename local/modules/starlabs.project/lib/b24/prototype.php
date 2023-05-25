<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 2018-12-07
 * Time: 20:34
 */

namespace Starlabs\Project\B24;


trait Prototype
{
	private $method = '';
	private $params = null;
	/**
     * @var Request
     */
    private $Request = null;

    public function __construct($params)
    {
        $this->params = $params;
        return $o = new Request($this);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getData()
    {
        return $this->Request->response();
    }

    public function getResult()
    {
        return $this->Request->response()["result"];
    }

    /**
     * Есть или нету ошибки
     * @return bool
     */
    public function getError()
    {
        return !is_null($this->Request->response()["error"]);
    }

    /**
     * Текст ошибки
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->Request->response()["error_description"];
    }

    public function getDescription()
    {
        return $this->Request->response()["description"];
    }

}
