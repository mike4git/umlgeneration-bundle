<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Formatter;

use UMLGenerationBundle\Model\ObjectClass;

class ClassFormatter
{
    public const INDENTATION = '    ';

    public function __construct(
        private AttributeFormatter $attributeFormatter,
    ) {
    }

    public function format(ObjectClass $objectClass): string
    {
        $labelDeclaration = sprintf(
            <<<HTML
            <table border="0" cellborder="1" cellspacing="0" cellpadding="4">
                        <tr><td>&lt;DataObject&gt;<br/><b>%s (ID: %s)</b></td></tr>%s
                    </table>
            HTML,
            $objectClass->getClassName(),
            $objectClass->getClassId(),
            $this->formatAttributes($objectClass),
        );

        return sprintf(
            <<<BOX
            %s [
                shape=plain
                label=<
                    %s
                >
            ];
            BOX,
            $objectClass->getClassName(),
            $labelDeclaration,
        );
    }

    private function formatAttributes(ObjectClass $objectClass): string
    {
        if (!empty($objectClass->getAttributes())) {
            $template = PHP_EOL . <<<ATTRIBUTES_TABLE
            <tr><td>
                <table border="0" cellborder="0" cellspacing="0">
                %s
                </table>
            </td></tr>
            ATTRIBUTES_TABLE;

            $attributesAsString = '';
            $result = [];
            foreach ($objectClass->getAttributes() as $attribute) {
                $result[] = $this->attributeFormatter->format($attribute);
            }
            $attributesAsString = self::INDENTATION . implode(PHP_EOL . str_repeat(self::INDENTATION, 5), $result) . PHP_EOL . str_repeat(self::INDENTATION, 4);

            return sprintf($template, $attributesAsString);
        }

        return '';
    }
}
