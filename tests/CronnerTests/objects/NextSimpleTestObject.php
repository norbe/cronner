<?php

declare(strict_types=1);

namespace Bileto\Cronner\tests\objects;


class NextSimpleTestObject
{
	use \Nette\SmartObject;


	/**
	 * @cronner-task Test
	 */
	public function test01()
	{
	}
}
