<?php
declare(strict_types=1);

namespace UMLGenerationBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class UMLGenerationBundle extends AbstractPimcoreBundle
{
    public function getJsPaths(): array
    {
        return [
            '/bundles/umlgeneration/js/pimcore/startup.js',
        ];
    }
}
