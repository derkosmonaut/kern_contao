<?php

declare(strict_types=1);

foreach (['default', 'modern'] as $palette) {
    $GLOBALS['TL_DCA']['tl_layout']['palettes'][$palette] .= ';{kern_legend},kern_ux,kern_kopfzeile';
}

$GLOBALS['TL_DCA']['tl_layout']['fields']['kern_ux'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['kern_ux'],
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_layout']['fields']['kern_kopfzeile'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['kern_kopfzeile'],
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];
