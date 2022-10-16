<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\entity;

use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\base\EntityFlowItem;
use aieuo\mineflow\flowItem\base\EntityFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\mineflow\EntityVariableDropdown;
use aieuo\mineflow\formAPI\element\mineflow\ExampleNumberInput;
use pocketmine\event\entity\EntityDamageEvent;
use SOFe\AwaitGenerator\Await;

class AddDamage extends FlowItem implements EntityFlowItem {
    use EntityFlowItemTrait;
    use ActionNameWithMineflowLanguage;

    public function __construct(
        string         $entity = "",
        private string $damage = "",
        private int    $cause = EntityDamageEvent::CAUSE_ENTITY_ATTACK
    ) {
        parent::__construct(self::ADD_DAMAGE, FlowItemCategory::ENTITY);

        $this->setEntityVariableName($entity);
    }

    public function getDetailDefaultReplaces(): array {
        return ["entity", "damage"];
    }

    public function getDetailReplaces(): array {
        return [$this->getEntityVariableName(), $this->getDamage()];
    }

    public function setDamage(string $damage): void {
        $this->damage = $damage;
    }

    public function getDamage(): string {
        return $this->damage;
    }

    public function setCause(int $cause): void {
        $this->cause = $cause;
    }

    public function getCause(): int {
        return $this->cause;
    }

    public function isDataValid(): bool {
        return $this->damage !== "";
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $damage = $source->replaceVariables($this->getDamage());
        $cause = $this->getCause();

        $this->throwIfInvalidNumber($damage, 1);

        $entity = $this->getEntity($source);
        $this->throwIfInvalidEntity($entity);

        $event = new EntityDamageEvent($entity, $cause, (float)$damage);
        $entity->attack($event);

        yield Await::ALL;
    }

    public function getEditFormElements(array $variables): array {
        return [
            new EntityVariableDropdown($variables, $this->getEntityVariableName()),
            new ExampleNumberInput("@action.addDamage.form.damage", "10", $this->getDamage(), true, 1),
        ];
    }

    public function loadSaveData(array $content): FlowItem {
        $this->setEntityVariableName($content[0]);
        $this->setDamage($content[1]);
        if (isset($content[2])) $this->setCause((int)$content[2]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getEntityVariableName(), $this->getDamage()];
    }
}
