<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\base;

use aieuo\mineflow\exception\InvalidFlowValueException;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use pocketmine\event\Event;

interface EventFlowItem {

    public function getEventVariableName(string $name = ""): string;

    public function setEventVariableName(string $event, string $name = ""): void;

    /** @throws InvalidFlowValueException */
    public function getEvent(FlowItemExecutor $source, string $name = ""): Event;

    public function createTypeMismatchedException(string $variableName, string $eventName): InvalidFlowValueException;

}
