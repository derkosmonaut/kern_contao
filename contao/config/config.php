<?php

declare(strict_types=1);

use Derkosmonaut\KernContaoBundle\Setup\KernSetupModule;

// Layout → Themes → „KERN-Grundeinrichtung“
$GLOBALS['BE_MOD']['design']['themes']['kern_setup'] = [KernSetupModule::class, 'run'];
