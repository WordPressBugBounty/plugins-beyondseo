<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Exceptions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use InvalidArgumentException;

/**
 * Class InvalidQuestionException
 */
class InvalidQuestionException extends InvalidArgumentException
{

}