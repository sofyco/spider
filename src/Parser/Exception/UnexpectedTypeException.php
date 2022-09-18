<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Exception;

use Sofyco\Spider\Parser\Builder\Node\Type;

final class UnexpectedTypeException extends \InvalidArgumentException
{
    public function __construct(Type $type)
    {
        parent::__construct(\sprintf('Unexpected node type "%s"', $type->name));
    }
}
