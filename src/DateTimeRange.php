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

use DateTime;
use rob006\ranges\exceptions\InvalidRangeException;
use function is_int;
use function is_string;

/**
 * Entity for representing date and time ranges.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class DateTimeRange extends Range {

	/**
	 * DateTimeRange constructor.
	 *
	 * @param string|int|DateTime|null $from
	 * @param string|int|DateTime|null $to
	 * @throws InvalidRangeException
	 */
	public function __construct($from, $to) {
		if (is_string($from)) {
			$from = strtotime($from);
		}
		if ($from instanceof DateTime) {
			$from = $from->getTimestamp();
		}
		if (is_string($to)) {
			$to = strtotime($to);
		}
		if ($to instanceof DateTime) {
			$to = $to->getTimestamp();
		}

		if (!is_int($from) && $from !== null) {
			throw new InvalidRangeException(
				'$from needs to be string, integer, null or instance of DateTime.'
			);
		}
		if (!is_int($to) && $to !== null) {
			throw new InvalidRangeException(
				'$to needs to be string, integer, null or instance of DateTime.'
			);
		}

		parent::__construct($from, $to);
	}

	public function getFromFormatted(): string {
		return $this->getFromDate() ?? 'infinity';
	}

	public function getToFormatted(): string {
		return $this->getToDate() ?? 'infinity';
	}

	public function getFromDate(): ?string {
		return $this->getFrom() !== null ? date('Y-m-d H:i:s', $this->getFrom()) : null;
	}

	public function getToDate(): ?string {
		return $this->getTo() !== null ? date('Y-m-d H:i:s', $this->getTo()) : null;
	}
}
