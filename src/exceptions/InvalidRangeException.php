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

namespace rob006\ranges\exceptions;

use rob006\ranges\Range;

/**
 * Exception thrown when invalid range is provided for range constructor.
 *
 * @see Range::__construct()
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class InvalidRangeException extends RangeException {

}
