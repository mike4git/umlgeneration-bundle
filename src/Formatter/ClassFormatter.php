<?php

namespace UMLGenerationBundle\Formatter;

use UMLGenerationBundle\Model\ObjectClass;

class ClassFormatter
{
    const INDENTATION = '    ';

    public function __construct(
        private AttributeFormatter $attributeFormatter
    )
    {
    }

    public function format(ObjectClass $objectClass): string
    {
        $labelDeclaration = sprintf(
            <<<HTML
            <table border="0" cellborder="1" cellspacing="0" cellpadding="4">
                        <tr><td>&lt;DataObject&gt;<br/><b>%s (ID: %s)</b></td></tr>
                        <tr><td>
                            %s
                        </td></tr>
                    </table>
            HTML,
            $objectClass->getClassName(),
            $objectClass->getClassId(),
            $this->formatAttributes($objectClass)
        );
        return sprintf(<<<BOX
            %s [
                shape=plain
                label=<
                    %s
                >
            ];
            BOX,
            $objectClass->getClassName(),
            $labelDeclaration
        );

    }

    private function formatAttributes(ObjectClass $objectClass): string
    {

        $template = <<<ATTRIBUTES_TABLE
            <table border="0" cellborder="0" cellspacing="0">
                            %s</table>
            ATTRIBUTES_TABLE;

        $attributesAsString = '';
        if (!empty($objectClass->getAttributes())) {
            $result = [];
            foreach ($objectClass->getAttributes() as $attribute) {
                $result[] = $this->attributeFormatter->format($attribute);
            }
            $attributesAsString = self::INDENTATION . implode(PHP_EOL . str_repeat(self::INDENTATION, 5), $result) . PHP_EOL. str_repeat(self::INDENTATION, 4);
        }
        return sprintf($template, $attributesAsString);
    }
}
