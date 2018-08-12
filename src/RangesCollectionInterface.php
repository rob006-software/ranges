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

/**
 * Interface RangesCollectionInterface.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
interface RangesCollectionInterface {

	public function __toString(): string;

	public function add(RangeInterface $range): void;

	public function addMultiple(iterable $ranges): void;

	public function remove(RangeInterface $range): void;

	public function removeMultiple(iterable $ranges): void;

	public function getRanges(): array;
}
