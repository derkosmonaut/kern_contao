<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] .= ';{kern_legend},kern_container';

$GLOBALS['TL_DCA']['tl_article']['fields']['kern_container'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_article']['kern_container'],
    'inputType' => 'select',
    'options' => ['fluid'],
    'reference' => &$GLOBALS['TL_LANG']['tl_article']['kern_container_options'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(16) NOT NULL default ''",
];
