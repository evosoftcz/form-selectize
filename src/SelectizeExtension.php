<?php
/**
 * Copyright (c) 2014 Petr Olišar (http://olisar.eu)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace Selecize\Form\Control;

use Nette;
use Nette\PhpGenerator as Code;
use Nette\Schema\Expect;

/**
 * Description of ControlsExtension
 *
 * @author Petr Olišar <petr.olisar@gmail.com>
 * @author Ondřej Sochůrek <o.sochurek@gmail.com>
 */
class SelectizeExtension extends Nette\DI\CompilerExtension
{

    /**
     * @return Nette\Schema\Schema
     */
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
             'mode' => Expect::string('full')->dynamic(),
             'create' => Expect::bool(true)->dynamic(),
             'maxItems' => Expect::int()->dynamic(),
             'delimiter' => Expect::string('#/')->dynamic(),
             'plugins' => Expect::array()->default(['remove_button']),
             'valueField' => Expect::string('id')->dynamic(),
             'labelField' => Expect::string('name')->dynamic(),
             'searchField' => Expect::string('name')->dynamic(),
         ])->castTo('array');
    }

    /**
     * @param Code\ClassType $class
     */
	public function afterCompile(Code\ClassType $class)
    {
		parent::afterCompile($class);
		$init = $class->methods['initialize'];
		$init->addBody('\App\Form\Control\Selectize::register(?, ?);', ['addSelectize', $this->config]);
	}

}