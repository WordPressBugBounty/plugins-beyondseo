<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\Inflector\Rules\French;

use BeyondSEODeps\Doctrine\Inflector\GenericLanguageInflectorFactory;
use BeyondSEODeps\Doctrine\Inflector\Rules\Ruleset;

final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset(): Ruleset
    {
        return Rules::getSingularRuleset();
    }

    protected function getPluralRuleset(): Ruleset
    {
        return Rules::getPluralRuleset();
    }
}
