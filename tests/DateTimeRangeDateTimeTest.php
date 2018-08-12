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

namespace rob006\ranges\tests;

use DateTime;

/**
 * Class DateTimeRangeDateTimeTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class DateTimeRangeDateTimeTest extends DateTimeRangeTest {

	protected function value(string $base, int $delay = 0) {
		return (new DateTime())->setTimestamp(strtotime($base, static::TIME) + $delay);
	}
}
