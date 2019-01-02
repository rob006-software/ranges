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

use PHPUnit\Framework\TestCase;
use rob006\ranges\exceptions\RangeSplitException;
use rob006\ranges\exceptions\RangeWipedOutException;
use rob006\ranges\RangeInterface;

/**
 * Class BaseRangeTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
abstract class BaseRangeTest extends TestCase {

	protected const TIME = 1526679515;

	/**
	 * @dataProvider simpleOverlapOnExcludingDataProvider()
	 *
	 * @param RangeInterface $source
	 * @param RangeInterface $excluded
	 * @param RangeInterface $expected
	 */
	public function testSimpleOverlapOnExcluding($source, $excluded, $expected) {
		$source->exclude($excluded);
		$this->compareRanges($source, $expected);
	}

	protected function compareRanges(RangeInterface $range, RangeInterface $expected) {
		$this->assertSame($expected->getFrom(), $range->getFrom(), 'from');
		$this->assertSame($expected->getTo(), $range->getTo(), 'to');
	}

	public function simpleOverlapOnExcludingDataProvider() {
		return [
			'end range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-6 days'), null),
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
			],
			'end range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days'), null),
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
			],
			'end range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days', -1), null),
				$this->createRange($this->value('-7 days'), $this->value('-5 days', -1)),
			],
			'end range overlap with infinity' => [
				$this->createRange($this->value('-7 days'), null),
				$this->createRange($this->value('-6 days'), null),
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
			],
			'begin range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-10 days'), $this->value('-6 days')),
				$this->createRange($this->value('-6 days'), $this->value('-5 days')),
			],
			'begin range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days')),
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
			],
			'begin range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days', 1)),
				$this->createRange($this->value('-7 days', 1), $this->value('-5 days')),
			],
			'begin range overlap with infinity' => [
				$this->createRange(null, $this->value('-5 days')),
				$this->createRange(null, $this->value('-6 days')),
				$this->createRange($this->value('-6 days'), $this->value('-5 days')),
			],
			'cut infinity from beginning' => [
				$this->createRange(null, null),
				$this->createRange(null, $this->value('-6 days')),
				$this->createRange($this->value('-6 days'), null),
			],
			'cut infinity from end' => [
				$this->createRange(null, null),
				$this->createRange($this->value('-6 days'), null),
				$this->createRange(null, $this->value('-6 days')),
			],
			'no overlap from end' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
			],
			'no overlap from beginning' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
			],
		];
	}

	/**
	 * @dataProvider overlapsOnExcludingResultingExceptionsDataProvider()
	 *
	 * @param RangeInterface $source
	 * @param RangeInterface $excluded
	 * @param $expectedException
	 */
	public function testOverlapOnExcludingResultingExceptions($source, $excluded, $expectedException) {
		$this->expectExceptionObject($expectedException);
		$source->exclude($excluded);
	}

	public function overlapsOnExcludingResultingExceptionsDataProvider() {
		return [
			'full overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-8 days'), $this->value('-5 days')),
				new RangeWipedOutException(
					'Range was wiped out after excluding range: ' .
					$this->createRange($this->value('-8 days'), $this->value('-5 days')) . '.'
				),
			],
			'full overlap by infinity' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange(null, null),
				new RangeWipedOutException(
					'Range was wiped out after excluding range: ' .
					$this->createRange(null, null) . '.'
				),
			],
			'full overlap by infinity from beginning' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange(null, $this->value('-5 days')),
				new RangeWipedOutException(
					'Range was wiped out after excluding range: ' .
					$this->createRange(null, $this->value('-5 days')) . '.'
				),
			],
			'full overlap by infinity from end' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-8 days'), null),
				new RangeWipedOutException(
					'Range was wiped out after excluding range: ' .
					$this->createRange($this->value('-8 days'), null) . '.'
				),
			],
			'full overlap by equal range' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				new RangeWipedOutException(
					'Range was wiped out after excluding range: ' .
					$this->createRange($this->value('-7 days'), $this->value('-6 days')) . '.'
				),
			],
			'split' => [
				$this->createRange($this->value('-10 days'), $this->value('-5 days')),
				$this->createRange($this->value('-8 days'), $this->value('-6 days')),
				new RangeSplitException(
					"Range was split into two ranges after excluding {$this->createRange($this->value('-8 days'), $this->value('-6 days'))} from {$this->createRange($this->value('-10 days'), $this->value('-5 days'))}",
					$this->createRange($this->value('-10 days'), $this->value('-5 days')),
					$this->createRange($this->value('-8 days'), $this->value('-6 days'))
				),
			],
		];
	}

	/**
	 * @dataProvider simpleOverlapOnMergingDataProvider()
	 *
	 * @param RangeInterface $source
	 * @param RangeInterface $merged
	 * @param RangeInterface $expected
	 */
	public function testSimpleOverlapOnMerging($source, $merged, $expected) {
		$sourceClone = clone $source;
		$sourceClone->mergeWith($merged);
		$this->compareRanges($sourceClone, $expected);

		// order of merging should change result
		$mergedClone = clone $merged;
		$mergedClone->mergeWith($source);
		$this->compareRanges($mergedClone, $expected);
	}

	public function simpleOverlapOnMergingDataProvider() {
		return [
			'end range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-6 days'), null),
				$this->createRange($this->value('-7 days'), null),
			],
			'end range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days'), null),
				$this->createRange($this->value('-7 days'), null),
			],
			'end range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days', -1), null),
				$this->createRange($this->value('-7 days'), null),
			],
			'end range overlap with infinity' => [
				$this->createRange($this->value('-7 days'), null),
				$this->createRange($this->value('-6 days'), null),
				$this->createRange($this->value('-7 days'), null),
			],
			'begin range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-10 days'), $this->value('-6 days')),
				$this->createRange($this->value('-10 days'), $this->value('-5 days')),
			],
			'begin range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days')),
				$this->createRange(null, $this->value('-5 days')),
			],
			'begin range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days', 1)),
				$this->createRange(null, $this->value('-5 days')),
			],
			'begin range overlap with infinity' => [
				$this->createRange(null, $this->value('-5 days')),
				$this->createRange(null, $this->value('-6 days')),
				$this->createRange(null, $this->value('-5 days')),
			],
			'merge infinity from beginning' => [
				$this->createRange(null, null),
				$this->createRange(null, $this->value('-6 days')),
				$this->createRange(null, null),
			],
			'merge infinity from end' => [
				$this->createRange(null, null),
				$this->createRange($this->value('-6 days'), null),
				$this->createRange(null, null),
			],
			'infinity war' => [
				$this->createRange(null, null),
				$this->createRange(null, null),
				$this->createRange(null, null),
			],
			'full overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-8 days'), $this->value('-5 days')),
				$this->createRange($this->value('-8 days'), $this->value('-5 days')),
			],
			'full overlap by infinity' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange(null, null),
				$this->createRange(null, null),
			],
			'full overlap by infinity from beginning' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange(null, $this->value('-5 days')),
				$this->createRange(null, $this->value('-5 days')),
			],
			'full overlap by infinity from end' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-8 days'), null),
				$this->createRange($this->value('-8 days'), null),
			],
			'full overlap by equal range' => [
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				$this->createRange($this->value('-7 days'), $this->value('-6 days')),
			],
		];
	}

	/**
	 * @dataProvider overlapsOnMergingResultingExceptionsDataProvider()
	 *
	 * @param RangeInterface $source
	 * @param RangeInterface $excluded
	 */
	public function testOverlapOnMergingResultingExceptions($source, $excluded) {
		$this->expectExceptionObject(new RangeSplitException(
			"Ranges $source and $excluded cannot be merged into one.",
			$source,
			$excluded
		));
		$source->mergeWith($excluded);
	}

	/**
	 * @dataProvider overlapsOnMergingResultingExceptionsDataProvider()
	 *
	 * @param RangeInterface $source
	 * @param RangeInterface $excluded
	 */
	public function testOverlapOnMergingResultingExceptionsReverse($source, $excluded) {
		$this->expectExceptionObject(new RangeSplitException(
			"Ranges $excluded and $source cannot be merged into one.",
			$excluded,
			$source
		));
		$excluded->mergeWith($source);
	}

	public function overlapsOnMergingResultingExceptionsDataProvider() {
		return [
			'no overlap from end' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
			],
			'no overlap from end with infinity 1' => [
				$this->createRange(null, $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
			],
			'no overlap from end with infinity 2' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), null),
			],
			'no overlap from end with infinity 3' => [
				$this->createRange(null, $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), null),
			],
			'no overlap from beginning' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
			],
			'no overlap from beginning with infinity 1' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange(null, $this->value('-13 days')),
			],
			'no overlap from beginning with infinity 2' => [
				$this->createRange($this->value('-10 days'), null),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
			],
			'no overlap from beginning with infinity 3' => [
				$this->createRange($this->value('-10 days'), null),
				$this->createRange(null, $this->value('-13 days')),
			],
		];
	}

	/**
	 * @dataProvider compareDataProvider()
	 *
	 * @param RangeInterface $range1
	 * @param RangeInterface $range2
	 * @param int $result
	 */
	public function testCompare(RangeInterface $range1, RangeInterface $range2, int $result) {
		$this->assertSame($range1->compareWith($range2), $result);
		$this->assertSame($range2->compareWith($range1), 0 - $result);
	}

	public function compareDataProvider() {
		return [
			'end range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-6 days'), null),
				0,
			],
			'end range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days'), null),
				0,
			],
			'end range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days', -1), null),
				0,
			],
			'end range overlap with infinity' => [
				$this->createRange($this->value('-7 days'), null),
				$this->createRange($this->value('-6 days'), null),
				0,
			],
			'begin range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-10 days'), $this->value('-6 days')),
				0,
			],
			'begin range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days')),
				0,
			],
			'begin range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days', 1)),
				0,
			],
			'begin range overlap with infinity' => [
				$this->createRange(null, $this->value('-5 days')),
				$this->createRange(null, $this->value('-6 days')),
				0,
			],
			'cut infinity from beginning' => [
				$this->createRange(null, null),
				$this->createRange(null, $this->value('-6 days')),
				0,
			],
			'cut infinity from end' => [
				$this->createRange(null, null),
				$this->createRange($this->value('-6 days'), null),
				0,
			],
			'no overlap from end' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
				-1,
			],
			'no overlap from beginning' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
				1,
			],
			'full overlap' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-16 days'), $this->value('-1 days')),
				0,
			],
			'no overlap from end with infinity 1' => [
				$this->createRange(null, $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
				-1,
			],
			'no overlap from end with infinity 2' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), null),
				-1,
			],
			'no overlap from end with infinity 3' => [
				$this->createRange(null, $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), null),
				-1,
			],
			'no overlap from beginning with infinity 1' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange(null, $this->value('-13 days')),
				1,
			],
			'no overlap from beginning with infinity 2' => [
				$this->createRange($this->value('-10 days'), null),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
				1,
			],
			'no overlap from beginning with infinity 3' => [
				$this->createRange($this->value('-10 days'), null),
				$this->createRange(null, $this->value('-13 days')),
				1,
			],
		];
	}

	/**
	 * @dataProvider gapDataProvider()
	 *
	 * @param RangeInterface $range1
	 * @param RangeInterface $range2
	 * @param int $result
	 */
	public function testGap(RangeInterface $range1, RangeInterface $range2, int $result) {
		$this->assertSame($range1->getGapBetween($range2), $result);
		$this->assertSame($range2->getGapBetween($range1), $result);
	}

	public function gapDataProvider() {
		return [
			'end range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-6 days'), null),
				0,
			],
			'end range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days'), null),
				0,
			],
			'end range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-5 days', -1), null),
				0,
			],
			'end range overlap with infinity' => [
				$this->createRange($this->value('-7 days'), null),
				$this->createRange($this->value('-6 days'), null),
				0,
			],
			'begin range overlap' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange($this->value('-10 days'), $this->value('-6 days')),
				0,
			],
			'begin range overlap (touch)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days')),
				0,
			],
			'begin range overlap (1 second overlap)' => [
				$this->createRange($this->value('-7 days'), $this->value('-5 days')),
				$this->createRange(null, $this->value('-7 days', 1)),
				0,
			],
			'begin range overlap with infinity' => [
				$this->createRange(null, $this->value('-5 days')),
				$this->createRange(null, $this->value('-6 days')),
				0,
			],
			'cut infinity from beginning' => [
				$this->createRange(null, null),
				$this->createRange(null, $this->value('-6 days')),
				0,
			],
			'cut infinity from end' => [
				$this->createRange(null, null),
				$this->createRange($this->value('-6 days'), null),
				0,
			],
			'no overlap from end' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
				$this->timestamp('-6 days') - $this->timestamp('-8 days'),
			],
			'no overlap from beginning' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
				$this->timestamp('-10 days') - $this->timestamp('-13 days'),
			],
			'full overlap' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-16 days'), $this->value('-1 days')),
				0,
			],
			'no overlap from end with infinity 1' => [
				$this->createRange(null, $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), $this->value('-3 days')),
				$this->timestamp('-6 days') - $this->timestamp('-8 days'),
			],
			'no overlap from end with infinity 2' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), null),
				$this->timestamp('-6 days') - $this->timestamp('-8 days'),
			],
			'no overlap from end with infinity 3' => [
				$this->createRange(null, $this->value('-8 days')),
				$this->createRange($this->value('-6 days'), null),
				$this->timestamp('-6 days') - $this->timestamp('-8 days'),
			],
			'no overlap from beginning with infinity 1' => [
				$this->createRange($this->value('-10 days'), $this->value('-8 days')),
				$this->createRange(null, $this->value('-13 days')),
				$this->timestamp('-10 days') - $this->timestamp('-13 days'),
			],
			'no overlap from beginning with infinity 2' => [
				$this->createRange($this->value('-10 days'), null),
				$this->createRange($this->value('-16 days'), $this->value('-13 days')),
				$this->timestamp('-10 days') - $this->timestamp('-13 days'),
			],
			'no overlap from beginning with infinity 3' => [
				$this->createRange($this->value('-10 days'), null),
				$this->createRange(null, $this->value('-13 days')),
				$this->timestamp('-10 days') - $this->timestamp('-13 days'),
			],
		];
	}

	abstract protected function createRange($from, $to): RangeInterface;

	abstract protected function value(string $base, int $delay = 0);

	protected function timestamp(string $base, int $delay = 0) {
		return strtotime($base, static::TIME) + $delay;
	}
}
