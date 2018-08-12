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

namespace rob006\ranges;

use rob006\ranges\exceptions\InvalidRangeTypeException;
use function gettype;

/**
 * Class DateTimeRangesCollection.
 *
 * @see DateTimeRange
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class DateTimeRangesCollection extends RangesCollection {

	/**
	 * @param RangeInterface $range
	 * @throws InvalidRangeTypeException
	 */
	public function add(RangeInterface $range): void {
		if (!$range instanceof DateTimeRange) {
			throw new InvalidRangeTypeException(strtr('$range needs to be instance of DateTimeRange, {type} given.', [
				'{type}' => gettype($range),
			]));
		}

		parent::add($range);
	}
}
