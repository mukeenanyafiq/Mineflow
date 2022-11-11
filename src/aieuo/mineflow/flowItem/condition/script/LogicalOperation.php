<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\condition\script;

use aieuo\mineflow\flowItem\base\ConditionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\condition\Condition;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemContainer;
use aieuo\mineflow\flowItem\FlowItemContainerTrait;
use aieuo\mineflow\formAPI\element\Button;
use aieuo\mineflow\ui\FlowItemContainerForm;
use pocketmine\player\Player;

abstract class LogicalOperation extends FlowItem implements Condition, FlowItemContainer {
    use FlowItemContainerTrait;
    use ConditionNameWithMineflowLanguage;

    public function __construct(
        string $id,
        string $category = FlowItemCategory::SCRIPT,
    ) {
        parent::__construct($id, $category);
    }

    public function getDetail(): string {
        $details = ["----------".$this->getId()."-----------"];
        foreach ($this->getConditions() as $condition) {
            $details[] = $condition->getShortDetail();
        }
        $details[] = "------------------------";
        return implode("\n", $details);
    }

    public function getContainerName(): string {
        return empty($this->getCustomName()) ? $this->getName() : $this->getCustomName();
    }

    public function hasCustomMenu(): bool {
        return true;
    }

    public function getCustomMenuButtons(): array {
        return [
            new Button("@condition.edit", fn(Player $player) => (new FlowItemContainerForm)->sendActionList($player, $this, FlowItemContainer::CONDITION)),
        ];
    }

    public function loadSaveData(array $contents): FlowItem {
        foreach ($contents as $content) {
            $condition = FlowItem::loadEachSaveData($content);
            $this->addCondition($condition);
        }
        return $this;
    }

    public function serializeContents(): array {
        return $this->getConditions();
    }

    public function isDataValid(): bool {
        return true;
    }

    public function __clone() {
        $conditions = [];
        foreach ($this->getConditions() as $k => $condition) {
            $conditions[$k] = clone $condition;
        }
        $this->setConditions($conditions);
    }
}
