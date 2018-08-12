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
use rob006\ranges\DateTimeRangesCollection;
use rob006\ranges\RangeInterface;
use rob006\ranges\RangesCollectionInterface;

/**
 * Class DateTimeRangesCollectionTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
abstract class DateTimeRangesCollectionTest extends BaseRangesCollectionTest {

	protected function compareRanges(RangeInterface $range, RangeInterface $expected) {
		parent::compareRanges($range, $expected);

		/* @var $expected DateTimeRange */
		/* @var $range DateTimeRange */
		$this->assertSame($expected->getFromDate(), $range->getFromDate(), 'from');
		$this->assertSame($expected->getToDate(), $range->getToDate(), 'to');
	}

	protected function createRange($from, $to): RangeInterface {
		return new DateTimeRange($from, $to);
	}

	protected function createCollection(): RangesCollectionInterface {
		return new DateTimeRangesCollection();
	}
}
