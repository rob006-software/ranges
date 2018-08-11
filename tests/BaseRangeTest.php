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
		$this->assertSame($expected->getFrom(), $source->getFrom(), 'from');
		$this->assertSame($expected->getTo(), $source->getTo(), 'to');
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
				new RangeSplitException('@todo collection'),
			],
		];
	}

	abstract protected function createRange($from, $to): RangeInterface;

	abstract protected function value(string $base, int $offset = 0);
}
