<?php

namespace UMLGenerationBundle\Formatter;

use UMLGenerationBundle\Model\Attribute;

class AttributeFormatter
{
    public function format(Attribute $attribute): string
    {
        $additionalInfo = '';
        if ($attribute->getAdditionalInfo()) {
            $additionalInfo = sprintf(' (%s)', $attribute->getAdditionalInfo());
        }

        return sprintf(
            <<<TABLEROW
            <tr><td>%s %s</td><td>%s%s</td></tr>
            TABLEROW,
            '#',
            $attribute->getName(),
            $attribute->getType(),
            $additionalInfo,
        );
    }
}
