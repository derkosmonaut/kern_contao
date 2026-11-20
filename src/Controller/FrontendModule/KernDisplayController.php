<?php

declare(strict_types=1);

namespace Derkosmonaut\KernContaoBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * „Darstellung anpassen“: Schrift, Farbschema und Textabstände für Besucher.
 * Die Logik liegt in public/js/kern-display.js, die Auswahl wird im Browser gespeichert.
 */
#[AsFrontendModule(type: 'kern_display', category: 'kern', template: 'frontend_module/kern_display')]
final class KernDisplayController extends AbstractFrontendModuleController
{
    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $template->set('panel_id', 'kern-display-'.$model->id);

        return $template->getResponse();
    }
}
