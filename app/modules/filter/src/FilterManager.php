<?php

namespace Foxkit\Filter;

class FilterManager
{
    /**
     * @var array
     */
    protected $defaults = [
        'addrelnofollow' => 'Foxkit\Filter\AddRelNofollowFilter',
        'alnum'          => 'Foxkit\Filter\AlnumFilter',
        'alpha'          => 'Foxkit\Filter\AlphaFilter',
        'bool'           => 'Foxkit\Filter\BooleanFilter',
        'boolean'        => 'Foxkit\Filter\BooleanFilter',
        'digits'         => 'Foxkit\Filter\DigitsFilter',
        'int'            => 'Foxkit\Filter\IntFilter',
        'integer'        => 'Foxkit\Filter\IntFilter',
        'float'          => 'Foxkit\Filter\FloatFilter',
        'json'           => 'Foxkit\Filter\JsonFilter',
        'pregreplace'    => 'Foxkit\Filter\PregReplaceFilter',
        'slugify'        => 'Foxkit\Filter\SlugifyFilter',
        'string'         => 'Foxkit\Filter\StringFilter',
        'stripnewlines'  => 'Foxkit\Filter\StripNewlinesFilter'
    ];

    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults = null) {
        if (null !== $defaults) {
            $this->defaults = $defaults;
        }
    }

    /**
     * Apply shortcut.
     *
     * @see apply()
     */
    public function __invoke($value, $name, array $options = [])
    {
        return $this->apply($value, $name, $options);
    }

    /**
     * Apply a filter.
     *
     * @param  mixed  $value
     * @param  string $name
     * @param  array  $options
     * @return FilterInterface The filter
     * @throws \InvalidArgumentException
     */
    public function apply($value, $name, array $options = [])
    {
        return $this->get($name, $options)->filter($value);
    }

    /**
     * Gets a filter.
     *
     * @param  string $name
     * @param  array  $options
     * @return FilterInterface The filter
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = [])
    {
        if (array_key_exists($name, $this->defaults)) {
            $this->filters[$name] = $this->defaults[$name];
        }

        if (!array_key_exists($name, $this->filters)) {
            throw new \InvalidArgumentException(sprintf('Filter "%s" is not defined.', $name));
        }

        if (is_string($class = $this->filters[$name])) {
            $this->filters[$name] = new $class;
        }

        $filter = clone $this->filters[$name];
        $filter->setOptions($options);

        return $filter;
    }

    /**
     * Registers a filter.
     *
     * @param string $name
     * @param string|FilterInterface $filter
     * @throws \InvalidArgumentException
     */
    public function register($name, $filter)
    {
        if (array_key_exists($name, $this->filters)) {
            throw new \InvalidArgumentException(sprintf('Filter with the name "%s" is already defined.', $name));
        }

        if (is_string($filter) && !class_exists($filter)) {
            throw new \InvalidArgumentException(sprintf('Unknown filter with the class name "%s".', $filter));
        }

        $this->filters[$name] = $filter;
    }
}
