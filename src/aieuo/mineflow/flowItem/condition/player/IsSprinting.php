<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\condition\player;

use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\libs\_eab1cdb1577fc133\SOFe\AwaitGenerator\Await;

class IsSprinting extends CheckPlayerState {

    public function __construct(string $player = "") {
        parent::__construct(self::IS_SPRINTING, player: $player);
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $player = $this->getPlayer()->getOnlinePlayer($source);

        yield Await::ALL;
        return $player->isSprinting();
    }
}