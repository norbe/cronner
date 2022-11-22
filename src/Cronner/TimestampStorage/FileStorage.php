<?php

declare(strict_types=1);

namespace Bileto\Cronner\TimestampStorage;


use DateTime;
use DateTimeInterface;
use Nette\SmartObject;
use Nette\Utils\FileSystem;
use Nette\Utils\SafeStream;
use Nette\Utils\Strings;
use Bileto\Cronner\Exceptions\EmptyTaskNameException;
use Bileto\Cronner\Exceptions\InvalidTaskNameException;
use Bileto\Cronner\ITimestampStorage;

class FileStorage implements ITimestampStorage
{
	use SmartObject;

	public const DATETIME_FORMAT = 'Y-m-d H:i:s O';

	/** @var string */
	private $directory;

	/** @var string|null */
	private $taskName;

	public function __construct(string $directory)
	{
		SafeStream::register();
		$directory = rtrim($directory, DIRECTORY_SEPARATOR);
		FileSystem::createDir($directory);
		$this->directory = $directory;
	}

	/**
	 * Sets name of current task.
	 */
	public function setTaskName(?string $taskName = null): void
	{
		if ($taskName !== null && Strings::length($taskName) <= 0) {
			throw new InvalidTaskNameException('Given task name is not valid.');
		}
		$this->taskName = $taskName;
	}

	/**
	 * Saves current date and time as last invocation time.
	 */
	public function saveRunTime(DateTimeInterface $now): void
	{
		$filepath = $this->buildFilePath();
		file_put_contents($filepath, $now->format(self::DATETIME_FORMAT));
		$this->unsetLastTryTime();
	}

	/**
	 * Returns date and time of last successful cron task invocation.
	 */
	public function loadLastRunTime(): ?DateTimeInterface
	{
		$date = null;
		$filepath = $this->buildFilePath();
		if (file_exists($filepath)) {
			$date = file_get_contents($filepath);
			$date = DateTime::createFromFormat(self::DATETIME_FORMAT, $date);
		}

		return $date ? $date : null;
	}

	/**
	 * Builds file path from directory and task name.
	 */
	private function buildFilePath(bool $forLastTry = false): string
	{
		if ($this->taskName === null) {
			throw new EmptyTaskNameException('Task name was not set.');
		}

		return SafeStream::PROTOCOL . '://' . $this->directory . '/' . sha1($this->taskName) . ($forLastTry ? '_try' : '');
	}

	/**
	 * Saves current date and time as last invocation try time.
	 */
	public function saveLastTryTime(DateTimeInterface $now): void
	{
		$filepath = $this->buildFilePath(true);
		file_put_contents($filepath, $now->format(self::DATETIME_FORMAT));
	}

	private function unsetLastTryTime()
	{
		$filepath = $this->buildFilePath(true);
		if (file_exists($filepath)) {
			unlink($filepath);
		}
	}

	/**
	 * Returns date and time of last cron task invocation try.
	 */
	public function loadLastTryTime(): ?DateTimeInterface
	{
		$date = null;
		$filepath = $this->buildFilePath(true);
		if (file_exists($filepath)) {
			$date = file_get_contents($filepath);
			$date = DateTime::createFromFormat(self::DATETIME_FORMAT, $date);
		}

		return $date ? $date : null;
	}
}
