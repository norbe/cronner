<?php

declare(strict_types=1);

namespace Bileto\Cronner;

use DateTimeInterface;

interface ITimestampStorage
{

	/**
	 * Sets name of current task.
	 */
	public function setTaskName(?string $taskName = null): void;

	/**
	 * Saves current date and time as last successful invocation time.
	 */
	public function saveRunTime(DateTimeInterface $runTime): void;
	/**
	 * Saves current date and time as last invocation try time.
	 */
	public function saveLastTryTime(DateTimeInterface $tryTime): void;

	/**
	 * Returns date and time of last successful cron task invocation.
	 */
	public function loadLastRunTime(): ?DateTimeInterface;

	/**
	 * Returns date and time of last cron task invocation try.
	 */
	public function loadLastTryTime(): ?DateTimeInterface;
}
