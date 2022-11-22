<?php

declare(strict_types=1);

namespace stekycz\Cronner;

use DateTimeInterface;

interface ITimestampStorage
{

	/**
	 * Sets name of current task.
	 *
	 * @param string|null $taskName
	 */
	public function setTaskName(string $taskName = NULL);

	/**
	 * Saves current date and time as last invocation time.
	 *
	 * @param DateTimeInterface $now
	 */
	public function saveRunTime(DateTimeInterface $now);

	/**
	 * Saves current date and time as last invocation try time.
	 */
	public function saveLastTryTime(DateTimeInterface $tryTime);

	/**
	 * Returns date and time of last cron task invocation.
	 *
	 * @return DateTimeInterface|null
	 */
	public function loadLastRunTime();


	/**
	 * Returns date and time of last cron task invocation try.
	 *
	 * @return DateTimeInterface|null
	 */
	public function loadLastTryTime();

}
