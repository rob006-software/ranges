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

use Countable;
use Iterator;
use rob006\ranges\exceptions\InvalidRangeTypeException;
use rob006\ranges\exceptions\RangeSplitException;
use rob006\ranges\exceptions\RangeWipedOutException;
use function count;
use function gettype;

/**
 * Class RangesCollection.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class RangesCollection implements RangesCollectionInterface, Iterator, Countable {

	/**
	 * @var RangeInterface[]
	 */
	private $ranges = [];
	private $position = 0;

	/**
	 * RangesCollection constructor.
	 *
	 * @param RangeInterface[] $ranges
	 * @throws InvalidRangeTypeException
	 */
	public function __construct(array $ranges = []) {
		$this->addMultiple($ranges);
	}

	public function __toString(): string {
		return implode("\n", $this->ranges);
	}

	/**
	 * @param RangeInterface $range
	 * @throws InvalidRangeTypeException
	 */
	public function add(RangeInterface $range): void {
		if (!$range instanceof Range) {
			throw new InvalidRangeTypeException(strtr('$range needs to be instance of Range, {type} given.', [
				'{type}' => gettype($range),
			]));
		}

		$overlapped = [];
		$ranges = [];
		$pushed = false;
		foreach ($this->ranges as $rangeKey => $existingRange) {
			$compareResult = $range->compare($existingRange);
			if ($compareResult <= 0) {
				if (!$pushed) {
					$ranges[] = $range;
					$pushed = true;
				}
				if ($compareResult === 0) {
					$overlapped[] = $existingRange;
				} else {
					$ranges[] = $existingRange;
				}
			} else {
				$ranges[] = $existingRange;
			}
		}

		// make sure that range is pushed, even if existing regions array is empty
		if (!$pushed) {
			$ranges[] = $range;
		}

		if (!empty($overlapped)) {
			foreach ($overlapped as $overlappedRange) {
				$range->merge($overlappedRange);
			}
		}

		$this->ranges = $ranges;
	}

	/**
	 * @param iterable $ranges
	 * @throws InvalidRangeTypeException
	 */
	public function addMultiple(iterable $ranges): void {
		foreach ($ranges as $range) {
			$this->add($range);
		}
	}

	public function remove(RangeInterface $range): void {
		$ranges = [];
		foreach ($this->ranges as $i => $existingRange) {
			try {
				$existingRange->exclude($range);
				$ranges[] = $existingRange;
			} catch (RangeWipedOutException $exception) {
				// if range was wiped out, we simply don't adding it to $ranges
			} catch (RangeSplitException $exception) {
				$ranges[] = $exception->getRanges()[0];
				$ranges[] = $exception->getRanges()[1];
			}
		}

		$this->ranges = $ranges;
	}

	public function removeMultiple(iterable $ranges): void {
		foreach ($ranges as $range) {
			$this->remove($range);
		}
	}

	public function getRanges(): array {
		return $this->ranges;
	}

	public function rewind() {
		$this->position = 0;
	}

	public function current() {
		return $this->ranges[$this->position];
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		$this->position++;
	}

	public function valid() {
		return isset($this->array[$this->position]);
	}

	public function count() {
		return count($this->ranges);
	}
}
