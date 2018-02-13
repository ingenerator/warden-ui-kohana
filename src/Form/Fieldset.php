<?php

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Warden\UI\Kohana\Form;

class Fieldset implements \ArrayAccess
{
    protected $error_validation_class = 'has-error';
    protected $success_validation_class;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    protected $values;

    public function __construct(array $values, array $errors)
    {
        $this->values                   = $values;
        $this->errors                   = $errors;
        $this->success_validation_class = empty($this->errors) ? NULL : 'has-success';
    }

    public function offsetExists($offset)
    {
        return TRUE;
    }

    public function offsetGet($offset)
    {
        $value = \Arr::get($this->values, $offset);
        $error = \Arr::get($this->errors, $offset);

        return [
            'value'            => $value,
            'errors'           => $error,
            'validation_class' => $error ? $this->error_validation_class : $this->success_validation_class,
        ];
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException(get_class($this).' is immutable, cannot call '.__METHOD__);
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException(get_class($this).' is immutable, cannot call '.__METHOD__);
    }

}
