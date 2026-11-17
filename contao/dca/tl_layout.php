<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] .= ';{kern_legend},kern_ux';
$GLOBALS['TL_DCA']['tl_layout']['palettes']['modern'] .= ';{kern_legend},kern_ux';

$GLOBALS['TL_DCA']['tl_layout']['fields']['kern_ux'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['kern_ux'],
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => "char(1) NOT NULL default ''",
];
