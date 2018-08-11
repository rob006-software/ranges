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

	public function getTo();

	public function getFromFormatted(): string;

	public function getToFormatted(): string;

	public function getDuration();

	public function isEmpty(): bool;

	public function exclude(RangeInterface $range): void;
}
