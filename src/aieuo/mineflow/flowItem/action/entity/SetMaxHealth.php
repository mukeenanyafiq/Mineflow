<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\entity;

use aieuo\mineflow\flowItem\FlowItemExecutor;
use SOFe\AwaitGenerator\Await;

class SetMaxHealth extends SetHealthBase {

    public function __construct(string $entity = "", string $health = "") {
        parent::__construct(self::SET_MAX_HEALTH, entity: $entity, health: $health);
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $health = $this->getInt($source->replaceVariables($this->getHealth()), min: 1);
        $entity = $this->getOnlineEntity($source);

        $entity->setMaxHealth($health);

        yield Await::ALL;
    }
}
