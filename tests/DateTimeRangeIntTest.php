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

use rob006\ranges\DateTimeRange;

/**
 * Class DateTimeRangeIntTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class DateTimeRangeIntTest extends DateTimeRangeTest {

	protected function value(string $base, int $delay = 0) {
		return strtotime($base, static::TIME) + $delay;
	}

	public function testIsActive() {
		/* @var $range DateTimeRange */
		$range = $this->createRange($this->timestamp('-10 days'), $this->timestamp('-9 days'));
		$this->assertTrue($range->isActive($this->timestamp('-10 days')));
		$this->assertTrue($range->isActive($this->timestamp('-9 days')));
		$this->assertTrue($range->isActive($this->timestamp('-10 days') + 1));
		$this->assertTrue($range->isActive($this->timestamp('-9 days') - 1));
		$this->assertFalse($range->isActive($this->timestamp('-10 days') - 1));
		$this->assertFalse($range->isActive($this->timestamp('-9 days') + 1));

		$range = $this->createRange(null, null);
		$this->assertTrue($range->isActive($this->timestamp('-10 days')));
		$this->assertTrue($range->isActive($this->timestamp('-9 days')));
	}
}
