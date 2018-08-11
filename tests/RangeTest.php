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

use rob006\ranges\Range;
use rob006\ranges\RangeInterface;

/**
 * Class RangeTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class RangeTest extends BaseRangeTest {

	protected function value(string $base, int $delay = 0) {
		return strtotime($base, static::TIME) + $delay;
	}

	protected function createRange($from, $to): RangeInterface {
		return new Range($from, $to);
	}
}
