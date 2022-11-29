<?php

declare(strict_types=1);

namespace stekycz\Cronner\Tasks;

use DateTime;
use DateTimeInterface;
use Nette\Reflection\Method;
use ReflectionClass;
use stekycz\Cronner\ITimestampStorage;

final class Task
{
	use \Nette\SmartObject;

	/**
	 * @var object
	 */
	private $object;

	/**
	 * @var Method
	 */
	private $method;

	/**
	 * @var ITimestampStorage
	 */
	private $timestampStorage;

	/**
	 * @var Parameters|null
	 */
	private $parameters = NULL;

	/**
	 * @var DateTimeInterface|null
	 */
	private $now = NULL;

	/**
	 * Creates instance of one task.
	 *
	 * @param object $object
	 * @param Method $method
	 * @param ITimestampStorage $timestampStorage
	 */
	public function __construct($object, Method $method, ITimestampStorage $timestampStorage, DateTimeInterface $now = NULL)
	{
		$this->object = $object;
		$this->method = $method;
		$this->timestampStorage = $timestampStorage;
		$this->setNow($now);
	}

	public function getObjectName() : string
	{
		return get_class($this->object);
	}

	public function getMethodReflection() : Method
	{
		return $this->method;
	}

	public function getObjectPath() : string
	{
		$reflection = new ReflectionClass($this->object);

		return $reflection->getFileName();
	}

	/**
	 * Returns True if given parameters should be run.
	 */
	public function shouldBeRun(DateTimeInterface $now = NULL) : bool
	{
		if ($now === NULL) {
			$now = new DateTime();
		}

		$parameters = $this->getParameters();
		if (!$parameters->isTask()) {
			return FALSE;
		}
		$this->timestampStorage->setTaskName($parameters->getName());

		return $parameters->isInDay($now)
			&& $parameters->isInTime($now)
			&& $parameters->isNextPeriod($now, $this->timestampStorage->loadLastRunTime())
			&& $parameters->isNextTryAllowed($now, $this->timestampStorage->loadLastTryTime())
			&& $parameters->isInDayOfMonth($now);
	}

	public function getName() : string
	{
		return $this->getParameters()->getName();
	}

	public function __invoke(DateTimeInterface $now)
	{
		$this->timestampStorage->setTaskName($this->getName());
		$this->timestampStorage->saveLastTryTime($now);
		$this->method->invoke($this->object);
		$this->timestampStorage->setTaskName($this->getName()); // ensure that task is returned back if some cron overwrites it (for example cron monitor service)
		$this->timestampStorage->saveRunTime($now);
		$this->timestampStorage->setTaskName();
	}

	/**
	 * Returns instance of parsed parameters.
	 */
	private function getParameters() : Parameters
	{
		if ($this->parameters === NULL) {
			$this->parameters = new Parameters(Parameters::parseParameters($this->method, $this->getNow()));
		}

		return $this->parameters;
	}


	public function setNow($now)
	{
		if ($now === NULL) {
			$now = new DateTime();
		}

		$this->now = $now;
	}

	public function getNow()
	{
		if ($this->now === NULL) {
			$this->now = new DateTime();
		}
		return $this->now;
	}
}

