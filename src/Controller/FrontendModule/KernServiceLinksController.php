<?php

declare(strict_types=1);

namespace Derkosmonaut\KernContaoBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Links zu „Leichte Sprache“ und „Gebärdensprache“ mit den KERN-Icons.
 */
#[AsFrontendModule(type: 'kern_service_links', category: 'kern', template: 'frontend_module/kern_service_links')]
final class KernServiceLinksController extends AbstractFrontendModuleController
{
    private const LINKS = [
        'kern_easy_language' => 'easy-language',
        'kern_sign_language' => 'sign-language',
    ];

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $pageAdapter = $this->getContaoAdapter(PageModel::class);
        $currentPage = $this->getPageModel();
        $links = [];

        foreach (self::LINKS as $field => $icon) {
            if (!$model->{$field} || !$page = $pageAdapter->findPublishedById((int) $model->{$field})) {
                continue;
            }

            $links[] = [
                'href' => $this->generateContentUrl($page),
                'title' => $page->pageTitle ?: $page->title,
                'icon' => $icon,
                'active' => $currentPage && (int) $currentPage->id === (int) $page->id,
            ];
        }

        if (!$links) {
            return new Response();
        }

        $template->set('links', $links);

        return $template->getResponse();
    }
}
