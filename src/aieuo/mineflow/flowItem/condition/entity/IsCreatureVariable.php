<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\condition\entity;

use aieuo\mineflow\flowItem\FlowItemExecutor;
use pocketmine\entity\Living;
use SOFe\AwaitGenerator\Await;

class IsCreatureVariable extends CheckEntityState {

    public function __construct(string $entity = "") {
        parent::__construct(self::IS_CREATURE_VARIABLE, entity: $entity);
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $entity = $this->getOnlineEntity($source);

        yield Await::ALL;
        return $entity instanceof Living;
    }
}
