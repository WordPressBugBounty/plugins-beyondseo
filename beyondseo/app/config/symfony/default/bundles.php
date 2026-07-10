<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BeyondSEO\Symfony\Bundles\FrameworkBundle;
use BeyondSEODeps\DDD\DDDBundle;

return [
	/*Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],*/
	/*Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],*/
	FrameworkBundle::class => ['all' => true],
    BeyondSEODeps\Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    BeyondSEODeps\Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    BeyondSEODeps\Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
	DDDBundle::class => ['all' => true]
];
