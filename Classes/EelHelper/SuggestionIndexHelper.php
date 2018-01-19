<?php
namespace Flowpack\SearchPlugin\EelHelper;

/*
 * This file is part of the Flowpack.SearchPlugin package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\SearchPlugin\Exception;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;

/**
 * Helper for building suggestion configurations
 *
 * @Flow\Proxy(false)
 */
class SuggestionIndexHelper implements ProtectedContextAwareInterface
{
    /**
     * @param string|array $input The input to store, this can be a an array of strings or just a string. This field is mandatory.
     * @param string $output The result is de-duplicated if several documents have the same output, i.e. only one is returned as part of the suggest result.
     * @param array $payload An arbitrary JSON object, which is simply returned in the suggest option.
     * @param int $weight A positive integer or a string containing a positive integer, which defines a weight and allows you to rank your suggestions.
     * @return array
     */
    public function build($input, $output = '', array $payload = [], $weight = 1)
    {
        return [
            'input' => $this->prepareInput($input),
            'output' => $this->prepareOutput($output),
            'payload' => json_encode($payload),
            'weight' => $weight
        ];
    }

    /**
     * @param string|array $input
     * @return array
     * @throws Exception
     */
    protected function prepareInput($input)
    {
        $process = function ($input) {
            if (!\is_string($input)) {
                throw new Exception('Only string are supported as input', 1512733297);
            }
            $input = preg_replace("/\r|\n/", '', $input);
            return array_values(array_filter(explode(' ', preg_replace("/[^[:alnum:][:space:]]/u", ' ', strip_tags($input)))));
        };
        if (\is_string($input)) {
            return $process($input);
        } elseif (\is_array($input)) {
            $data = [];
            foreach (array_map($process, $input) as $values) {
                $data = \array_merge($data, $values);
            }
            return $data;
        } else {
            throw new Exception('Only string or array are supported as input', 1512733287);
        }
    }

    /**
     * @param string $input
     * @return array
     */
    protected function prepareOutput($input)
    {
        return strip_tags($input);
    }

    /**
     * All methods are considered safe
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}