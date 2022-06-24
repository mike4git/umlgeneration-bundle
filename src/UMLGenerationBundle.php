<?php

namespace UMLGenerationBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class UMLGenerationBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/umlgeneration/js/pimcore/startup.js',
        ];
    }
}
