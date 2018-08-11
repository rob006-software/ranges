<?php

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
use rob006\ranges\RangeInterface;

/**
 * Class DateTimeRangeTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
abstract class DateTimeRangeTest extends BaseRangeTest {

	/**
	 * @dataProvider simpleOverlapOnExcludingDataProvider()
	 *
	 * @param DateTimeRange $source
	 * @param DateTimeRange $excluded
	 * @param DateTimeRange $expected
	 */
	public function testSimpleOverlapOnExcluding($source, $excluded, $expected) {
		parent::testSimpleOverlapOnExcluding($source, $excluded, $expected);

		$this->assertSame($expected->getFromDate(), $source->getFromDate(), 'from');
		$this->assertSame($expected->getToDate(), $source->getToDate(), 'to');
	}

	protected function createRange($from, $to): RangeInterface {
		return new DateTimeRange($from, $to);
	}
}
