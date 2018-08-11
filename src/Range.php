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

use rob006\ranges\exceptions\InvalidRangeException;
use rob006\ranges\exceptions\RangeSplitException;
use rob006\ranges\exceptions\RangeWipedOutException;

/**
 * Class Range.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class Range implements RangeInterface {

	private $from;
	private $to;

	/**
	 * Range constructor.
	 *
	 * @param int|null $from
	 * @param int|null $to
	 * @throws InvalidRangeException
	 */
	public function __construct(?int $from, ?int $to) {
		$this->from = $from;
		$this->to = $to;

		if ($this->isEmpty()) {
			throw new InvalidRangeException(
				"\$to needs to be greater than \$from, $this given."
			);
		}
	}

	public function __toString(): string {
		return "{$this->getFromFormatted()} - {$this->getToFormatted()}";
	}

	public function getFromFormatted(): string {
		return $this->from !== null ? (string) $this->from : 'infinity';
	}

	public function getToFormatted(): string {
		return $this->to !== null ? (string) $this->to : 'infinity';
	}

	/**
	 * Removes specified range from current range.
	 *
	 * @param RangeInterface $range
	 *
	 * @throws RangeSplitException
	 * @throws RangeWipedOutException
	 */
	public function exclude(RangeInterface $range): void {
		$clone = clone $this;
		if (
			self::compare($clone->from, $range->getFrom(), true) > 0
			&& self::compare($clone->to, $range->getTo()) < 0
		) {
			throw new RangeSplitException('@todo collection');
		}

		// cut from the end
		if (
			self::compare($clone->to, $range->getTo()) >= 0
			&& self::compare($clone->to, $range->getFrom()) <= 0
		) {
			$clone->to = $range->getFrom();
		}
		// cut from the beginning
		if (
			self::compare($clone->from, $range->getFrom(), true) <= 0
			&& self::compare($clone->from, $range->getTo(), true) >= 0
		) {
			$clone->from = $range->getTo();
		}

		// wipe out by infinity
		if ($range->getFrom() === null && $range->getTo() === null) {
			$clone->from = 0;
			$clone->to = 0;
		}

		if ($clone->isEmpty()) {
			throw new RangeWipedOutException("Range was wiped out after excluding range: $range.");
		}

		$this->from = $clone->from;
		$this->to = $clone->to;
	}

	public function isEmpty(): bool {
		return $this->getDuration() <= 0;
	}

	public function getDuration() {
		if ($this->from === null || $this->to === null) {
			return INF;
		}

		return self::compare($this->from, $this->to);
	}

	public function getFrom(): ?int {
		return $this->from;
	}

	public function getTo(): ?int {
		return $this->to;
	}

	/**
	 * Compare 2 values:
	 * - `0` if values are the same
	 * - positive integer if $value is greater that $base
	 * - negative integer if $value is lower that $base
	 *
	 * @param int|null $base
	 * @param int|null $value
	 * @param bool $minus
	 * @return int|float
	 */
	private static function compare(?int $base, ?int $value, bool $minus = false) {
		if ($base === $value) {
			return 0;
		}

		if ($base === null) {
			return $minus ? INF : -INF;
		}
		if ($value === null) {
			return $minus ? -INF : INF;
		}

		return $value - $base;
	}
}
