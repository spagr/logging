<?php

namespace Contributte\Logging\Tracy\Logger;

use DirectoryIterator;
use Exception;
use Throwable;

/**
 * TracyLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
abstract class TracyLogger implements ILogger
{

	/** @var string */
	protected $directory;

	/**
	 * @param string $directory
	 */
	public function __construct($directory)
	{
		$this->directory = $directory;
	}

	/**
	 * @param Exception|Throwable $exception
	 * @return string
	 */
	protected function getExceptionFile($exception)
	{
		while ($exception) {
			$data[] = [
				$exception->getMessage(),
				$exception->getCode(),
				$exception->getFile(),
				$exception->getLine(),
				array_map(function ($item) {
					unset($item['args']);

					return $item;
				}, $exception->getTrace()),
			];
			$exception = $exception->getPrevious();
		}
		$hash = substr(md5(serialize($data)), 0, 10);
		foreach (new DirectoryIterator($this->directory) as $file) {
			if (strpos($file, $hash)) {
				return $this->directory . $file;
			}
		}

		return $this->directory . 'exception--' . @date('Y-m-d--H-i') . '--' . $hash . '.html'; // @ timezone may not be set
	}

}