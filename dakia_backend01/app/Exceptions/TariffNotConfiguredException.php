<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when PricingEngine cannot find a real tariff/zone/rate for a
 * shipment. Never caught silently to fall back on a fake price — callers
 * decide what to do (e.g. ShipmentController lets a booking succeed without
 * a calculated price, and logs it, rather than inventing a number).
 */
class TariffNotConfiguredException extends RuntimeException
{
}
