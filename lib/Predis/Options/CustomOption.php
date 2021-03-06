<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Options;

/**
 * Implements a generic class used to dinamically define a client option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class CustomOption implements IOption
{
    private $validate;
    private $default;

    /**
     * @param array $options List of options
     */
    public function __construct(Array $options)
    {
        $this->validate = $this->filterCallable($options, 'validate');
        $this->default  = $this->filterCallable($options, 'default');
    }

    /**
     * Checks if the specified value in the options array is a callable object.
     *
     * @param array $options Array of options
     * @param string $key Target option.
     */
    private function filterCallable($options, $key)
    {
        if (!isset($options[$key])) {
            return;
        }

        $callable = $options[$key];
        if (is_callable($callable)) {
            return $callable;
        }

        throw new \InvalidArgumentException("The parameter $key must be callable");
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        if (isset($value)) {
            if ($this->validate === null) {
                return $value;
            }
            $validator = $this->validate;

            return $validator($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        if (!isset($this->default)) {
            return;
        }
        $default = $this->default;

        return $default();
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($value)
    {
        if (isset($value)) {
            return $this->validate($value);
        }

        return $this->getDefault();
    }
}
