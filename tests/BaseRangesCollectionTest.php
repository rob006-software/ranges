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
use rob006\ranges\RangeInterface;
use rob006\ranges\RangesCollectionInterface;
use function count;

/**
 * Class BaseRangesCollectionTest.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
abstract class BaseRangesCollectionTest extends TestCase {

	protected const TIME = 1526679515;

	/**
	 * @dataProvider addDataProvider()
	 *
	 * @param RangeInterface[] $ranges
	 * @param RangeInterface[] $expected
	 */
	public function testAdd(array $ranges, array $expected) {
		$collection = $this->createCollection();
		$collection->addMultiple($ranges);
		$collectionRanges = $collection->getRanges();
		$this->assertCount(count($expected), $collectionRanges);
		foreach ($expected as $i => $range) {
			$this->compareRanges($range, $collectionRanges[$i]);
		}
	}

	protected function compareRanges(RangeInterface $range, RangeInterface $expected) {
		$this->assertSame($expected->getFrom(), $range->getFrom(), 'from');
		$this->assertSame($expected->getTo(), $range->getTo(), 'to');
	}

	public function addDataProvider() {
		return [
			'no overlap' => [
				[
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap - invalid order' => [
				[
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				],
				[
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap - invalid order with infinity 1' => [
				[
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
					$this->createRange(null, $this->value('-6 days')),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap - invalid order with infinity 2' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				],
				[
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
			],
			'no overlap - invalid order with infinity 3' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange(null, $this->value('-6 days')),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
			],
			'overlap by infinity' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange(null, null),
				],
				[
					$this->createRange(null, null),
				],
			],
			'extend from the beginning' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange($this->value('-12 days'), $this->value('-6 days')),
					$this->createRange($this->value('-30 days'), $this->value('-10 days')),
				],
				[
					$this->createRange($this->value('-30 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
			],
			'extend from the beginning with infinity' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange($this->value('-12 days'), $this->value('-6 days')),
					$this->createRange(null, $this->value('-10 days')),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
			],
			'extend from the end' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-6 days')),
					$this->createRange($this->value('-30 days'), $this->value('-17 days')),
					$this->createRange($this->value('-10 days'), $this->value('-3 days')),
				],
				[
					$this->createRange($this->value('-30 days'), $this->value('-17 days')),
					$this->createRange($this->value('-12 days'), $this->value('-3 days')),
				],
			],
			'extend from the end with infinity' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-6 days')),
					$this->createRange($this->value('-30 days'), $this->value('-17 days')),
					$this->createRange($this->value('-10 days'), null),
				],
				[
					$this->createRange($this->value('-30 days'), $this->value('-17 days')),
					$this->createRange($this->value('-12 days'), null),
				],
			],
			'fill the gap' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days'), $this->value('-5 days')),
				],
				[
					$this->createRange(null, null),
				],
			],
			'almost fill the gap' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days', 1), $this->value('-5 days', -1)),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days', 1), $this->value('-5 days', -1)),
					$this->createRange($this->value('-5 days'), null),
				],
			],
			'fill the gap with overlap 1' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days', 1), $this->value('-5 days', -1)),
					$this->createRange($this->value('-5 days', -1), $this->value('-5 days')),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days', 1), null),
				],
			],
			'fill the gap with overlap 2' => [
				[
					$this->createRange($this->value('-5 days'), null),
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days', 1), $this->value('-5 days', -1)),
					$this->createRange($this->value('-5 days', -1), $this->value('-5 days', 2)),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
					$this->createRange($this->value('-6 days', 1), null),
				],
			],
		];
	}

	/**
	 * @dataProvider removeDataProvider()
	 *
	 * @param RangeInterface[] $ranges
	 * @param RangeInterface[] $removed
	 * @param RangeInterface[] $expected
	 */
	public function testRemove(array $ranges, array $removed, array $expected) {
		$collection = $this->createCollection();
		$collection->addMultiple($ranges);
		$collection->removeMultiple($removed);
		$collectionRanges = $collection->getRanges();
		$this->assertCount(count($expected), $collectionRanges);
		foreach ($expected as $i => $range) {
			$this->compareRanges($range, $collectionRanges[$i]);
		}
	}

	public function removeDataProvider() {
		return [
			'no overlap' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-3 days'), $this->value('-2 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap with touch' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-4 days'), $this->value('-2 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap with infinity from beginning' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange(null, $this->value('-12 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap with infinity from end' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-4 days'), null),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'no overlap from middle' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-6 days'), $this->value('-5 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'decrease 2 ranges from middle' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-6 days', -100), $this->value('-5 days', 100)),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days', -100)),
					$this->createRange($this->value('-5 days', 100), $this->value('-4 days')),
				],
			],
			'wipe one range by exact range' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'wipe one range by bigger range' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-7 days', -100), $this->value('-6 days', 100)),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'wipe couple ranges by infinite end range' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-7 days'), null),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
				],
			],
			'infinity end wars' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
				[
					$this->createRange($this->value('-7 days'), null),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
				],
			],
			'cut infinity end' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
				[
					$this->createRange($this->value('-1 days'), null),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-1 days')),
				],
			],
			'wipe couple ranges by infinite begin range' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
				],
				[
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'infinity begin war' => [
				[
					$this->createRange(null, $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange(null, $this->value('-6 days')),
				],
				[
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'cut infinity begin' => [
				[
					$this->createRange(null, $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange(null, $this->value('-10 days')),
				],
				[
					$this->createRange($this->value('-10 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'infinity war' => [
				[
					$this->createRange(null, $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
				[
					$this->createRange(null, null),
				],
				[],
			],
			'slice hole in infinity' => [
				[
					$this->createRange(null, $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), null),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-4 days')),
				],
				[
					$this->createRange(null, $this->value('-12 days')),
					$this->createRange($this->value('-4 days'), null),
				],
			],
			'wipe by one range' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-4 days')),
				],
				[],
			],
			'split one range into two' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-11 days'), $this->value('-10 days')),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-11 days')),
					$this->createRange($this->value('-10 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
			],
			'massacre' => [
				[
					$this->createRange($this->value('-12 days'), $this->value('-9 days')),
					$this->createRange($this->value('-7 days'), $this->value('-6 days')),
					$this->createRange($this->value('-5 days'), $this->value('-4 days')),
				],
				[
					$this->createRange($this->value('-11 days'), $this->value('-5 days', 100)),
				],
				[
					$this->createRange($this->value('-12 days'), $this->value('-11 days')),
					$this->createRange($this->value('-5 days', 100), $this->value('-4 days')),
				],
			],
		];
	}

	public function testIteratorInterface() {
		$collection = $this->createCollection();
		$ranges = [
			$this->createRange($this->value('-12 days'), $this->value('-11 days')),
			$this->createRange($this->value('-10 days'), $this->value('-9 days')),
			$this->createRange($this->value('-7 days'), $this->value('-6 days')),
			$this->createRange($this->value('-5 days'), $this->value('-4 days')),
		];
		$collection->addMultiple($ranges);

		$count = 0;
		foreach ($collection as $index => $range) {
			$this->assertSame($ranges[$index], $range);
			$count++;
		}

		$this->assertSame(4, $count);
	}

	public function testCountableInterface() {
		$collection = $this->createCollection();
		$ranges = [
			$this->createRange($this->value('-12 days'), $this->value('-11 days')),
			$this->createRange($this->value('-10 days'), $this->value('-9 days')),
			$this->createRange($this->value('-7 days'), $this->value('-6 days')),
			$this->createRange($this->value('-5 days'), $this->value('-4 days')),
		];
		$collection->addMultiple($ranges);

		/* @noinspection PhpParamsInspection */
		$this->assertCount(4, $collection);
	}

	abstract protected function createCollection(): RangesCollectionInterface;

	abstract protected function createRange($from, $to): RangeInterface;

	abstract protected function value(string $base, int $delay = 0);
}
