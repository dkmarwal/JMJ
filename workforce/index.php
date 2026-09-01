<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Front Controller Entrypoint
 * Subdomain: admin.jmjenterprisessolutions.com
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

\Core\App::run();
