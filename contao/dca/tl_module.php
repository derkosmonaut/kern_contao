<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_module']['palettes']['kern_display']
    = '{title_legend},name,headline,type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['palettes']['kern_service_links']
    = '{title_legend},name,headline,type;{config_legend},kern_easy_language,kern_sign_language;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

foreach (['kern_easy_language', 'kern_sign_language'] as $field) {
    $GLOBALS['TL_DCA']['tl_module']['fields'][$field] = [
        'inputType' => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval' => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
        'sql' => 'int(10) unsigned NOT NULL default 0',
        'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
    ];
}
