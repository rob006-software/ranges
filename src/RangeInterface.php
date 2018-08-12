<?php

/*
 * This file is part of the rob006/ranges package.
 *
 * Copyright (c) 2018 Robert Korulczyk <robert@korulczyk.pl>
 *
 * Licensed under MIT License. For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace rob006\ranges;

/**
 * Interface RangeInterface.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
interface RangeInterface {

	public function __toString(): string;

	public function getFrom();

	public function getNumericalFrom();

	public function withFrom(?int $value): RangeInterface;

	public function getTo();

	public function getNumericalTo();

	public function withTo(?int $value): RangeInterface;

	public function getFromFormatted(): string;

	public function getToFormatted(): string;

	public function getDuration();

	public function isEmpty(): bool;

	public function compare(RangeInterface $range): int;

	public function merge(RangeInterface $range): void;

	public function exclude(RangeInterface $range): void;
}
