<?php

declare(strict_types=1);

/*
 * This file is part of the rob006/ranges package.
 *
 * Copyright (c) 2018 Robert Korulczyk <robert@korulczyk.pl>
 *
 * Licensed under MIT License. For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace rob006\ranges\exceptions;

use rob006\ranges\RangeInterface;
use Throwable;

/**
 * Exception thrown when excluding range resulting in split one range into two ranges.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class RangeSplitException extends RangeException {

	private $ranges;

	public function __construct(
		string $message,
		RangeInterface $range1,
		RangeInterface $range2,
		int $code = 0,
		Throwable $previous = null
	) {
		$this->ranges = [$range1, $range2];

		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return RangeInterface[]
	 */
	public function getRanges(): array {
		return $this->ranges;
	}
}
