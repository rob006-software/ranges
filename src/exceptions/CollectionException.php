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

use Exception;
use rob006\ranges\RangesCollection;

/**
 * Base exception for operations performed by RangesCollection.
 *
 * @see RangesCollection
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
abstract class CollectionException extends Exception {

}
