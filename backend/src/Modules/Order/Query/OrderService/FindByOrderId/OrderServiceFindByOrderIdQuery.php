<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderService\FindByOrderId;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderServiceFindByOrderIdQuery
{
    #[Assert\Positive]
    public int $orderId;
}
