<?php
namespace Starlabs\Project\B24;

class crmContactList
{
	use  Prototype;

	/**
	 * crmContactList constructor.
	 * @param array $filter['order'=>'','filter'=>'','select'=>'']
	 */
	public function __construct(array $filter = [])
	{

		$this->method = 'crm.contact.list';
		$this->params = $filter;
		$this->Request = new Request($this);
	}
}