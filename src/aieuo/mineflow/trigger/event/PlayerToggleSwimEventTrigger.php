<?php

namespace aieuo\mineflow\trigger\event;

use aieuo\mineflow\variable\BooleanVariable;
use aieuo\mineflow\variable\DefaultVariables;
use aieuo\mineflow\variable\DummyVariable;
use aieuo\mineflow\variable\object\PlayerVariable;
use pocketmine\event\player\PlayerToggleSwimEvent;

class PlayerToggleSwimEventTrigger extends EventTrigger {
    public function __construct() {
        parent::__construct("PlayerToggleSwimEvent", PlayerToggleSwimEvent::class);
    }

    public function getVariables(mixed $event): array {
        /** @var PlayerToggleSwimEvent $event */
        $target = $event->getPlayer();
        $variables = DefaultVariables::getPlayerVariables($target);
        $variables["state"] = new BooleanVariable($event->isSwimming());
        return $variables;
    }

    public function getVariablesDummy(): array {
        return [
            "target" => new DummyVariable(PlayerVariable::class),
            "state" => new DummyVariable(BooleanVariable::class),
        ];
    }
}
