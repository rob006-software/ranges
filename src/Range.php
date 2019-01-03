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

	public function getFrom(): ?int {
		return $this->from;
	}

	public function getNumericalFrom() {
		return $this->from ?? -INF;
	}

	public function withFrom(?int $value): RangeInterface {
		$clone = clone $this;
		$clone->from = $value;
		return $clone;
	}

	public function getTo(): ?int {
		return $this->to;
	}

	public function getNumericalTo() {
		return $this->to ?? INF;
	}

	public function withTo(?int $value): RangeInterface {
		$clone = clone $this;
		$clone->to = $value;
		return $clone;
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
		if (
			$this->getNumericalFrom() < $range->getNumericalFrom()
			&& $this->getNumericalTo() > $range->getNumericalTo()
		) {
			throw new RangeSplitException(
				"Range was split into two ranges after excluding $range from $this.",
				$this->withTo($range->getFrom()),
				$this->withFrom($range->getTo())
			);
		}

		if (
			$this->getNumericalFrom() >= $range->getNumericalFrom()
			&& $this->getNumericalTo() <= $range->getNumericalTo()
		) {
			throw new RangeWipedOutException("Range was wiped out after excluding range: $range.");
		}

		// cut from the end
		/* @noinspection NotOptimalIfConditionsInspection */
		if (
			$this->getNumericalTo() <= $range->getNumericalTo()
			&& $this->getNumericalTo() >= $range->getNumericalFrom()
		) {
			$this->to = $range->getFrom();
		}
		// cut from the beginning
		/* @noinspection NotOptimalIfConditionsInspection */
		if (
			$this->getNumericalFrom() >= $range->getNumericalFrom()
			&& $this->getNumericalFrom() <= $range->getNumericalTo()
		) {
			$this->from = $range->getTo();
		}
	}

	/**
	 * @param RangeInterface $range
	 * @throws RangeSplitException
	 */
	public function mergeWith(RangeInterface $range): void {
		if ($this->getDistanceBetween($range) > 0) {
			throw new RangeSplitException(
				"Ranges $this and $range cannot be merged into one.",
				$this,
				$range
			);
		}

		// extend from the end
		if ($this->getNumericalTo() < $range->getNumericalTo()) {
			$this->to = $range->getTo();
		}
		// extend from the beginning
		if ($this->getNumericalFrom() > $range->getNumericalFrom()) {
			$this->from = $range->getFrom();
		}
	}

	private function compareWith(RangeInterface $range): int {
		if ($this->getNumericalTo() <= $range->getNumericalFrom()) {
			return -1;
		}
		if ($this->getNumericalFrom() >= $range->getNumericalTo()) {
			return 1;
		}

		return 0;
	}

	public function isPreceding(RangeInterface $range): bool {
		return $this->compareWith($range) === -1;
	}

	public function isFollowing(RangeInterface $range): bool {
		return $this->compareWith($range) === 1;
	}

	public function isOverlapping(RangeInterface $range): bool {
		return $this->compareWith($range) === 0;
	}

	public function getDistanceBetween(RangeInterface $range) {
		switch ($this->compareWith($range)) {
			case -1:
				return $range->getNumericalFrom() - $this->getNumericalTo();
			case 1:
				return $this->getNumericalFrom() - $range->getNumericalTo();
			case 0:
				return 0;
		}
	}

	public function isEmpty(): bool {
		return $this->getDuration() <= 0;
	}

	public function getDuration() {
		if ($this->from === null || $this->to === null) {
			return INF;
		}

		return $this->to - $this->from;
	}
}
