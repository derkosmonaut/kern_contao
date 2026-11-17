<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_content']['palettes']['text'] .= ';{kern_legend},kern_alert_style';
$GLOBALS['TL_DCA']['tl_content']['palettes']['hyperlink'] .= ';{kern_legend},kern_button_style';

$GLOBALS['TL_DCA']['tl_content']['fields']['kern_alert_style'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['kern_alert_style'],
    'inputType' => 'select',
    'options' => ['info', 'success', 'warning', 'danger'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['kern_alert_style_options'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['kern_button_style'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['kern_button_style'],
    'inputType' => 'select',
    'options' => ['primary', 'secondary', 'tertiary'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['kern_button_style_options'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(16) NOT NULL default ''",
];
