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

/**
 * Class DateTimeRangeIntTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class DateTimeRangeIntTest extends DateTimeRangeTest {

	protected function value(string $base, int $delay = 0) {
		return strtotime($base, static::TIME) + $delay;
	}
}
