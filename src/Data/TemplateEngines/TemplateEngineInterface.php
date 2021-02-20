<?php
/**
 * TemplateEngineInterface
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\TemplateEngines;

interface TemplateEngineInterface
{

    /**
     * Fill template
     *
     * @param array $template Template
     * @param array $vars     Var for filling
     *
     * @return array
     */
    public function fillTemplate(array $template, array $vars): array;

}
