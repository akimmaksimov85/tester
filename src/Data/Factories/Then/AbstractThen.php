<?php
/**
 * AbstractThen
 */

declare(strict_types=1);

namespace Akimmaksimov85\TesterBundle\Data\Factories\Functional\Then;


use Coduo\PHPMatcher\PHPMatcher;

abstract class AbstractThen
{
    protected const ERROR_KEY_NOT_FOUND  = 'Key %s Not found';
    protected const ERROR_INVALID_VALUE  = 'Invalid value. Actual: %s. Expected: %s';
    protected const ERROR_REDUNDANT_KEYS = 'Invalid value. Actual result has redundant keys';
    protected const ERROR_EMPTY_VALUE    = 'is empty';

    protected const SPECIAL_VALIDATE_SYMBOL            = '@';
    protected const DELIMITER_FOR_MATCHER_TEMPLATES    = '.';
    protected const OR_CONDITION_FOR_MATCHER_TEMPLATES = '||';

    /**
     * PHPMatcher
     *
     * @var PHPMatcher
     */
    private PHPMatcher $phpMatcher;

    /**
     * SuitePath
     *
     * @var string
     */
    private string $suitePath;

    /**
     * AbstractThen constructor.
     *
     * @param string $suitePath SuitePath
     */
    public function __construct(string $suitePath)
    {
        $this->suitePath  = $suitePath;
        $this->phpMatcher = new PHPMatcher();
    }

    /**
     * Return errors as string
     *
     * @param array $errors Errors
     *
     * @return false|string
     */
    protected function getErrorAsString(array $errors): string
    {
        if (empty($errors) === true) {
            return '';
        }

        return sprintf(
            "\e[1;31mTestSuite %s, assert %s: %s\e[0m",
            $this->suitePath,
            static::class,
            json_encode(
                $errors,
                (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT)
            )
        );
    }

    /**
     * Compare digits
     *
     * @param int $actual   Actual code
     * @param int $expected Expected code
     *
     * @return string
     */
    protected function compareDigits(int $actual, int $expected): string
    {
        if ($actual === $expected) {
            return '';
        }

        return sprintf(
            self::ERROR_INVALID_VALUE,
            $actual,
            $expected
        );
    }

    /**
     * Compare arrays
     *
     * @param array $actual   Data for validation
     * @param array $expected Validate schema
     * @param bool  $strong   Strong matching (check redundant keys in actual response)
     * @param array $errors   Errors
     *
     * @return array
     */
    protected function compareArrays(array $actual, array $expected, bool $strong = false, array $errors = []): array
    {
        $expected = $this->makeArrayKeysToLower($expected);
        $actual   = $this->makeArrayKeysToLower($actual);

        foreach ($expected as $key => $value) {
            if (isset($actual[$key]) === false) {
                $errors[$key] = sprintf(self::ERROR_KEY_NOT_FOUND, $key);
                continue;
            }

            if (is_array($actual[$key]) === true
                && is_array($expected[$key]) === true
            ) {
                $errors[$key] = [];
                $errors[$key] = $this->compareArrays($actual[$key], $expected[$key], false, $errors[$key]);
                continue;
            }

            $this->phpMatcher->match($actual[$key], $expected[$key]);

            if (empty($this->phpMatcher->error()) === false) {
                if (empty($actual[$key]) === true) {
                    $actual[$key] = self::ERROR_EMPTY_VALUE;
                }

                $errors[$key] = sprintf(
                    self::ERROR_INVALID_VALUE,
                    $actual[$key],
                    $this->prepareExpectedValue($expected[$key])
                );
            }
        }

        if ($strong === true) {
            $this->phpMatcher->match(
                $expected,
                array_replace_recursive($actual, $expected)
            );

            if (empty($this->phpMatcher->error()) === false) {
                $errors[] = self::ERROR_REDUNDANT_KEYS;
            }
        }

        return array_filter($errors);
    }

    /**
     * Make understandable response, expected value is matcher template like @double@
     *
     * @param string $expected Expected value
     *
     * @return string
     */
    private function prepareExpectedValue(string $expected): string
    {
        if (substr_count($expected, self::SPECIAL_VALIDATE_SYMBOL) > 1) {
            $expected = explode(self::OR_CONDITION_FOR_MATCHER_TEMPLATES, $expected);
            $messages = [];

            foreach ($expected as $conditions) {
                $conditions        = array_filter(explode(self::SPECIAL_VALIDATE_SYMBOL, $conditions));
                $conditionMessages = [];

                foreach ($conditions as $condition) {
                    if (substr($condition, 0, 1) === self::DELIMITER_FOR_MATCHER_TEMPLATES) {
                        $clearCondition = preg_replace('#[^a-zа-яё0-9]#ius', ' ', $condition);
                        $conditionWords = preg_split('/(?=[A-Z])/', $clearCondition);

                        if (is_array($conditionWords) === false) {
                            continue;
                        }

                        $conditionMessages[] = implode(
                            ' ',
                            array_map(
                                function ($conditionWord) {
                                    return mb_strtolower($conditionWord);
                                },
                                $conditionWords
                            )
                        );
                        continue;
                    }

                    $conditionMessages[] = $condition . ' type';
                }

                $messages[] = trim(implode(' and ', $conditionMessages));
            }

            return implode(' or ', $messages);
        }

        return $expected;
    }

    /**
     * Make array keys to lower
     *
     * @param array $array Array of items
     *
     * @return array
     */
    private function makeArrayKeysToLower(array $array): array
    {
        $result = [];

        foreach ($array as $key => $item) {
            $result[mb_strtolower((string) $key)] = $item;
        }

        return $result;
    }

}
