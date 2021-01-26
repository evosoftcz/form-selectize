<?php
/**
 * Copyright (c) 2014 Petr Olišar (http://olisar.eu)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace Selectize\Form\Control;

use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Rules;
use Nette\Utils\Strings;

/**
 * Description of Selectize
 *
 * @author Petr Olišar <petr.olisar@gmail.com>
 * @author Ondřej Sochůrek <o.sochurek@gmail.com>
 */
class Selectize extends BaseControl
{

    private $entity;
    private $labelName;
    private $selectize;
    private $selectizeBack;
    private $options;
    private $prompt = FALSE;

    /**
     * @var Rules
     */
    private $original_rules;

    /**
     * @var string
     */
    private $selectizeClass = 'selectize show-hidden-error';


    public function __construct($label = null, array $entity = NULL, array $config = NULL)
    {
        parent::__construct($label);
        $this->entity = is_null($entity) ? [] : $entity;
        $this->labelName = $label;
        $this->options = $config;
        $this->selectizeClass = 'selectize show-hidden-error';
    }

    /**
     * @return string
     */
    public function getSelectizeClass(): string
    {
        return $this->selectizeClass;
    }

    /**
     * @param string $selectizeClass
     * @return Selectize
     */
    public function setSelectizeClass(string $selectizeClass): Selectize
    {
        $this->selectizeClass = $selectizeClass . ' show-hidden-error';
        return $this;
    }

    /**
     * @return mixed
     */
    private function getOriginalRules()
    {
        return $this->original_rules;
    }

    /**
     * @param Rules $original_rules
     * @return Selectize
     */
    private function setOriginalRules(Rules $original_rules): Selectize
    {
        $this->original_rules = $original_rules;
        return $this;
    }


    /**
     * @param array $options
     * @return Selectize
     */
    public function setOptions(array $options): Selectize
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
        return $this;
    }

    /**
     * @param string $mode
     * @return Selectize
     */
    public function setMode(string $mode): Selectize
    {
        $this->options['mode'] = $mode;
        return $this;
    }

    /**
     * @param bool $create
     * @return $this
     */
    public function setCreate(bool $create): Selectize
    {
        $this->options['create'] = $create;
        return $this;
    }

    /**
     * @param int $items
     * @return Selectize
     */
    public function maxItems(int $items): Selectize
    {
        $this->options['maxItems'] = $items;
        return $this;
    }

    /**
     * @param string $delimiter
     * @return Selectize
     */
    public function setDelimiter(string $delimiter): Selectize
    {
        $this->options['delimiter'] = $delimiter;
        return $this;
    }

    /**
     * @param array $plugins
     * @return Selectize
     */
    public function setPlugins(array $plugins): Selectize
    {
        $this->options['plugins'] = $plugins;
        return $this;
    }

    /**
     * @param string $valueField
     * @return Selectize
     */
    public function setValueField(string $valueField): Selectize
    {
        $this->options['valueField'] = $valueField;
        return $this;
    }

    /**
     * @param string $labelField
     * @return Selectize
     */
    public function setLabelField(string $labelField): Selectize
    {
        $this->options['labelField'] = $labelField;
        return $this;
    }

    /**
     * @param array $searchField
     * @return Selectize
     */
    public function setSearchField(array $searchField): Selectize
    {
        $this->options['searchField'] = $searchField;
        return $this;
    }

    /**
     * @param string $class
     * @return Selectize
     */
    public function setClass(string $class): Selectize
    {
        $class = str_replace('form-control', '', $class);
        $this->options['class'] = $class;
        return $this;
    }

    /**
     * @param string $ajaxURL
     * @return Selectize
     */
    public function setAjaxURL(string $ajaxURL): Selectize
    {
        $this->options['ajaxURL'] = $ajaxURL;
        return $this;
    }


    /**
     * @param string $prompt
     * @return Selectize
     */
    public function setPrompt(string $prompt): Selectize
    {
        $this->prompt = $prompt;
        return $this;
    }


    /**
     * Returns first prompt item?
     * @return mixed
     */
    public function getPrompt()
    {
        return $this->prompt;
    }


    /**
     * Sets options and option groups from which to choose.
     * @param array $items
     * @return array
     */
    public function setItems(array $items): array
    {
        return $this->entity = $items;
    }


    /**
     * Gets items
     * @return array
     */
    public function getItems(): array
    {
        return $this->entity;
    }


    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        if (!is_null($value)) {
            if (is_array($value)) {
                $i = 0;
                foreach ($value as $key => $slug) {
                    $i++;
                    $idName = $this->options['valueField'];
                    $this->selectizeBack .= isset($slug->$idName) ? $slug->$idName : $key;

                    if ($i < count($value)) {
                        $this->selectizeBack .= $this->options['delimiter'];
                    }
                }
            } else {
                $this->selectizeBack = $value;
            }
        }

        $this->selectize = $this->selectizeBack;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return @count($this->selectize) ? $this->selectize : NULL; // @ because of php7.2
    }

    /**
     *
     */
    public function loadHttpData(): void
    {
        if ($this->options['mode'] === 'select') {
            $value = $this->getHttpData(Form::DATA_LINE);
            if ($value === "") {
                $value = NULL;
            }
            $this->selectizeBack = $this->selectize = $value;
        } else {
            $this->prepareData();
        }
    }

    /**
     * @return \Nette\Utils\Html|string
     */
    public function getControl()
    {

        $this->setOption('rendered', TRUE);
        $name = $this->getHtmlName();
        $el = clone $this->control;
        $this->setOriginalRules($this->getRules());
        if (array_key_exists('ajaxURL', $this->options)) {
            $this->entity = $this->findActiveValue($this->entity, $this->options['valueField'], $this->selectizeBack);
        }

        $orig_attributes = parent::getControl()->attrs;
        $required = $orig_attributes['required'];
        $disabled = $orig_attributes['disabled'];
        $autocomplete = isset($orig_attributes['autocomplete']) ? $orig_attributes['autocomplete'] : null;
        $rules = isset($orig_attributes['data-nette-rules']) ? $orig_attributes['data-nette-rules'] : null;
        $class = isset($orig_attributes['class']) ? str_replace('form-control', '', $orig_attributes['class']) : null;

        if ($this->options['mode'] === 'full') {

            return $el->addAttributes(['id' => $this->getHtmlId(),
                                          'type' => 'text',
                                          'name' => $name,
                                          'class' => array(isset($this->options['class']) ? $this->options['class'] : '' . ' ' . $class . ' ' . $this->selectizeClass . ' ' . ' text '),
                                          'data-entity' => $this->entity,
                                          'data-options' => $this->options,
                                          'value' => $this->selectizeBack,
                                          'data-nette-rules' => $rules,
                                          'required' => $required,
                                          'disabled' => $disabled,
                                          'autocomplete' => $autocomplete]);
        } else {

            $this->entity = $this->prompt === FALSE ? $this->entity : self::arrayUnshiftAssoc($this->entity, '', $this->translate($this->prompt));
            $x = Helpers::createSelectBox($this->entity, ['selected?' => $this->selectizeBack])
                        ->id($this->getHtmlId())
                        ->name($name)
                        ->data('entity', $this->entity)
                        ->data('options', $this->options)
                        ->addAttributes(parent::getControl()->attrs)
                        ->setValue($this->selectizeBack);

            return $x->class(isset($this->options['class']) ? $this->options['class'] : '' . ' ' . $class . ' ' . $this->selectizeClass . ' ');
        }
    }

    /**
     * @param array|string $array
     * @param string      $key
     * @param string|null $value
     * @return array
     */
    function findActiveValue($array, string $key, ?string $value): array
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->findActiveValue($subarray, $key, $value));
            }
        }

        return $results;
    }


    /**
     * @param array  $arr
     * @param string $key
     * @param string $val
     * @return array
     */
    private static function arrayUnshiftAssoc(array &$arr, string $key, string $val): array
    {
        $arr = array_reverse($arr, true);
        $arr[$key] = $val;
        return array_reverse($arr, true);
    }


    /**
     *
     */
    private function prepareData(): void
    {
        $this->selectize = $this->split($this->getHttpData(Form::DATA_LINE));
        $this->selectizeBack = $this->getHttpData(Form::DATA_LINE);
        $iteration = false;
        foreach ($this->selectize as $key => $value) {
            if (!$this->myInArray($this->entity, $value, $this->options['valueField'])) {
                $iteration ?: $this->selectize['new'] = [];
                array_push($this->entity, [$this->options['valueField'] => $value, 'name' => $value]);
                array_push($this->selectize['new'], $value);
                unset($this->selectize[$key]);
                $iteration = true;
            }
        }
    }

    /**
     * @param string|null $selectize
     * @return array
     */
    private function split(?string $selectize): array
    {
        if ($selectize === null) {
            return [];
        }
        $return = Strings::split($selectize, '~' . $this->options['delimiter'] . '\s*~');
        return $return[0] === "" ? [] : $return;
    }


    /**
     * @param array  $array
     * @param string $value
     * @param string $key
     * @return bool
     */
    private function myInArray(array $array, string $value, string $key): bool
    {
        if (isset($array[$key]) AND $array[$key] == $value) {
            return true;
        }

        foreach ($array as $val) {
            if (is_array($val)) {
                if ($this->myInArray($val, $value, $key)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $method
     * @param array  $config
     */
    public static function register(string $method = 'addSelectize', array $config = []): void
    {
        Container::extensionMethod(
            $method,
            function (
                Container $container,
                $name,
                $label,
                $entity = null,
                array $options = null
            ) use ($config) {
                $component = new Selectize(
                    $label,
                    $entity,
                    is_array($options) ? array_replace($config, $options) : $config
                );
                $container->addComponent($component, $name);
                return $component;
            }
        );
    }
}
