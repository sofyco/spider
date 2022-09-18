<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Builder\Node;

enum Type: string
{
    case TEXT = 'text';
    case HTML = 'html';
    case ATTRIBUTE = 'attribute';
    case LARGEST_NESTED_CONTENT = 'largest_nested_content';
}
