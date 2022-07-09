<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Formatter;

use UMLGenerationBundle\Model\Relation;

class RelationsFormatter
{
    /**
     * @param Relation[] $relations
     */
    public function format(array $relations): string
    {
        $result = [];
        foreach ($relations as $relation) {
            $name = sprintf('%s -> %s', $relation->getSourceType(), $relation->getTargetType());

            $dir = sprintf('dir=%s', $relation->isBidirectional() ? 'none' : 'both');

            $arrow = '';
            $label = '';

            if ($relation->isInheritance()) {
                $arrow = 'arrowtail=none';
                $label = 'label="<<extends>>"';
            } elseif ($relation->isAggregation()) {
                $arrow = 'arrowtail=odiamond';
                $label = sprintf('label="%s %s"', $relation->getSourceRolename(), $this->determineCardinality($relation));
            }

            $result[] = sprintf('%s [%s %s %s];', $name, $dir, $arrow, $label);
        }

        return implode(PHP_EOL, $result);
    }

    private function determineCardinality(Relation $relation): string
    {
        $result = '(%s..%s)';
        $minimum = $relation->getMinimum() ?: '0';
        $maximum = $relation->getMaximum() ?: 'n';
        if ($minimum === $maximum) {
            return sprintf('(%s)', $minimum);
        }

        return sprintf($result, $minimum, $maximum);
    }
}
