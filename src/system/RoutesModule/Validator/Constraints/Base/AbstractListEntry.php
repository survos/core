<?php
/**
 * Routes.
 *
 * @copyright Zikula contributors (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula contributors <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\RoutesModule\Validator\Constraints\Base;

use Symfony\Component\Validator\Constraint;

/**
 * List entry validation constraint.
 */
abstract class AbstractListEntry extends Constraint
{
    /**
     * Entity name
     * @var string
     */
    public $entityName = '';

    /**
     * Property name
     * @var string
     */
    public $propertyName = '';

    /**
     * Whether multiple list values are allowed or not
     * @var boolean
     */
    public $multiple = false;

    /**
     * Minimum amount of values for multiple lists
     * @var integer
     */
    public $min;

    /**
     * Maximum amount of values for multiple lists
     * @var integer
     */
    public $max;
}
