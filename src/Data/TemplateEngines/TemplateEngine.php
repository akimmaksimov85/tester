<?php
/**
 * TemplateEngineInterface
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\TemplateEngines;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TemplateEngine implements TemplateEngineInterface
{
    protected const DATA_TEST_STRUCTURE_VAR_TEMPLATE = '{{%s}}';

    /**
     * Services params
     *
     * @var array
     */
    protected array $servicesParams;

    /**
     * TemplateEngine constructor.
     *
     * @param ParameterBagInterface $servicesParams Service params (from services.yml)
     */
    public function __construct(ParameterBagInterface $servicesParams)
    {
        $this->servicesParams = $servicesParams->get('app');
    }

    /**
     * Fill template
     *
     * @param array $template Template
     * @param array $vars     Vars for filling
     *
     * @return array
     */
    public function fillTemplate(array $template, array $vars): array
    {
        foreach ($vars as $key => $value) {
            $vars[sprintf(self::DATA_TEST_STRUCTURE_VAR_TEMPLATE, $key)] = $value;
            unset($vars[$key]);
        }

        $servicesParams = [];

        foreach ($this->servicesParams as $key => $servicesParam) {
            $servicesParams[strtoupper(sprintf(self::DATA_TEST_STRUCTURE_VAR_TEMPLATE, $key))] = $servicesParam;
        }

        return $this->replaceArrayItems($template, $vars, $servicesParams);
    }

    /**
     * Replace var items in array
     *
     * @param array $array          Array
     * @param array $vars           Vars for replacing
     * @param array $servicesParams Services params
     *
     * @return array
     */
    protected function replaceArrayItems(array $array, array $vars, array $servicesParams): array
    {
        foreach ($array as $key => $item) {
            if (is_array($item) === true) {
                $array[$key] = $this->replaceArrayItems($item, $vars, $servicesParams);
                continue;
            }

            $pattern = (string) $item;

            if (strpos($pattern, '{{') !== false) {
                $start = strpos($pattern, '{{');
                $stop  = (strpos($pattern, '}}') + 2);

                $pattern = substr($pattern, $start, $stop);
            }

            if (isset($vars[$pattern]) === true) {
                $item = str_replace($pattern, $vars[$pattern], $item);
                settype($item, gettype($vars[$pattern]));
            } else if (isset($servicesParams[$pattern]) === true) {
                $item = str_replace($pattern, $servicesParams[$pattern], $item);
                settype($item, gettype($servicesParams[$pattern]));
            }

            $array[$key] = $item;
        }

        return $array;
    }

}
