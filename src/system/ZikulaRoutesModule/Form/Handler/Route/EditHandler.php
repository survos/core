<?php
/**
 * Routes.
 *
 * @copyright Zikula contributors (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula contributors <support@zikula.org>.
 * @link http://www.zikula.org
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.6.2 (http://modulestudio.de).
 */

namespace Zikula\RoutesModule\Form\Handler\Route;

use ModUtil;
use Symfony\Component\Routing\RouteCollection;
use Zikula\RoutesModule\Entity\RouteEntity;
use Zikula\RoutesModule\Form\Handler\Route\Base\EditHandler as BaseEditHandler;
use Zikula\RoutesModule\Routing\Util as RoutingUtil;
use Zikula_Form_View;

/**
 * This handler class handles the page events of the Form called by the zikulaRoutesModule_admin_edit() function.
 * It aims on the route object type.
 */
class EditHandler extends BaseEditHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        $items = array();
        $urlNames = array();
        $modules = ModUtil::getModulesByState(3, 'displayname');
        foreach ($modules as $module) {
            $items[] = array(
                'text' => $module['displayname'],
                'value' => $module['name']
            );
            $urlNames[$module['name']] = $module['url'];
        }
        $view->assign('modules', $items);
        $view->assign('moduleUrlNames', $urlNames);

        $result = parent::initialize($view);

        if ($this->mode != 'create' || $this->hasTemplateId) {
            list($i18n, $bundlePrefix) = $this->convertRouteSettingsToOutput();
            $route = $view->getTplVar('route');
            $route['i18n'] = $i18n;
            $route['bundlePrefix'] = $bundlePrefix;
            $view->assign('route', $route);
        }

        return $result;
    }

    public function applyAction(array $args = array())
    {
        $return = parent::applyAction($args);

        $this->checkConflicts($this->entityRef);

        $cacheClearer = $this->view->getContainer()->get('zikula.cache_clearer');
        $cacheClearer->clear("symfony.routing");

        return $return;
    }

    protected function writeRelationDataToEntity(Zikula_Form_View $view, $entity, $entityData)
    {
        $entityData = $this->convertInputToRouteSettings($entityData);

        return parent::writeRelationDataToEntity($view, $entity, $entityData);
    }

    private function convertInputToRouteSettings($entityData)
    {
        $routingHelper = new RoutingUtil();

        $bundle = $entityData['bundle'];
        list ($controller, $type) = $routingHelper->sanitizeController($entityData['controller']);
        list($action, $func) = $routingHelper->sanitizeAction($entityData['action']);

        $entityData['controller'] = $controller;
        $entityData['action'] = $action;

        $controllerNamespace = $this->getControllerClass($bundle, $type);

        $entityData['defaults']['_controller'] = "$controllerNamespace::$action";

        if (empty($entityData['name'])) {
            // This route is newly created and not edited.
            $routeName = strtolower($bundle . "_" . $type . "_" . substr($action, 0, -6));
            // Check if name already exists.
            $dbRoute = $this->entityManager->getRepository('ZikulaRoutesModule:RouteEntity')->findOneBy(array('name' => $routeName));
            if ($dbRoute) {
                // This name already exists. We need to query the database using regexp. This function needs to be manually added.
                $config = $this->entityManager->getConfiguration();
                $config->addCustomStringFunction('REGEXP', 'Zikula\RoutesModule\Util\Regexp');

                // Get all entities with the same routename having a prefix.
                $query = $this->entityManager->createQuery('SELECT r.name FROM ZikulaRoutesModule:RouteEntity r WHERE REGEXP(r.name, :regexp) = 1');
                $query->setParameter('regexp', '^' . preg_quote($routeName) . '_[[:digit:]]*$');
                $results = $query->getArrayResult();
                $highestSuffix = 0;
                foreach ($results as $result) {
                    $name = explode('_', $result['name']);
                    $suffix = (int)$name[count($name) - 1];
                    $highestSuffix = $suffix > $highestSuffix ? $suffix : $highestSuffix;
                }
                $highestSuffix += 1;

                $routeName .= "_$highestSuffix";
            }
            $entityData['name'] = $routeName;
        }

        $entityData['options']['i18n'] = $entityData['i18n'];
        $entityData['options']['zkNoBundlePrefix'] = !$entityData['bundlePrefix'];
        unset($entityData['i18n'], $entityData['bundlePrefix']);

        $entityData['group'] = RouteEntity::POSITION_MIDDLE;
        $entityData['sort'] = 0;
        $entityData['userRoute'] = true;

        return $entityData;
    }

    private function getControllerClass($bundle, $type)
    {
        // @todo Throw error if class is not found!
        $class = ModUtil::getClass($bundle, $type, false, false);

        return $class;
    }

    private function convertRouteSettingsToOutput()
    {
        $options = $this->entityRef->getOptions();
        $i18n = isset($options['i18n']) ? $options['i18n'] : true;
        $bundlePrefix = isset($options['zkNoBundlePrefix']) ? !$options['zkNoBundlePrefix'] : true;

        return array($i18n, $bundlePrefix);
    }

    private function checkConflicts(RouteEntity $routeEntity)
    {
        $newPath = $routeEntity->getPathWithBundlePrefix();

        $router = $this->view->getContainer()->get('router');
        /** @var RouteCollection $routeCollection */
        $routeCollection = $router->getRouteCollection();

        $errors = array();
        foreach ($routeCollection->all() as $route) {
            $path = $route->getPath();
            if ($path === '/{url}') {
                continue;
            }

            if ($path === $newPath) {
                $errors[] = array(
                    'type' => 'SAME',
                    'path' => $path
                );
                continue;
            }

            $pathRegExp = preg_quote(preg_replace("/{(.+)}/", "____DUMMY____", $path), '/');
            $pathRegExp = "#^" . str_replace('____DUMMY____', '(.+)', $pathRegExp) . "$#";

            $matches = array();
            preg_match($pathRegExp, $newPath, $matches);
            if (count($matches)) {
                $errors[] = array(
                    'type' => 'SIMILAR',
                    'path' => $path
                );
            }
        }

        foreach ($errors as $error) {
            if ($error['type'] == 'SAME') {
                $message = $this->__('It looks like you created or updated a route with a path which already exists. This is an error in most cases.');
            } else {
                $message = $this->__f('The path of the route you created or updated looks similar to the following already existing path: %s Are you sure you haven\'t just introduced a conflict?', $error['path']);
            }

            \LogUtil::registerError($message);
        }
    }
}
