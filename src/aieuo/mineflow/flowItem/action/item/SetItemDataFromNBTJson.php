<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\item;

use aieuo\mineflow\exception\InvalidFlowValueException;
use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\base\ItemFlowItem;
use aieuo\mineflow\flowItem\base\ItemFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\mineflow\ExampleInput;
use aieuo\mineflow\formAPI\element\mineflow\ItemVariableDropdown;
use aieuo\mineflow\Main;
use aieuo\mineflow\Mineflow;
use aieuo\mineflow\utils\Language;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\nbt\NbtException;
use SOFe\AwaitGenerator\Await;

class SetItemDataFromNBTJson extends FlowItem implements ItemFlowItem {
    use ItemFlowItemTrait;
    use ActionNameWithMineflowLanguage;

    protected string $returnValueType = self::RETURN_VARIABLE_NAME;

    public function __construct(
        string         $item = "",
        private string $json = "",
    ) {
        parent::__construct(self::SET_ITEM_DATA_FROM_NBT_JSON, FlowItemCategory::ITEM);

        $this->setItemVariableName($item);
    }

    public function getDetailDefaultReplaces(): array {
        return ["item", "json"];
    }

    public function getDetailReplaces(): array {
        return [$this->getItemVariableName(), $this->getJson()];
    }

    public function setJson(string $json): void {
        $this->json = $json;
    }

    public function getJson(): string {
        return $this->json;
    }

    public function isDataValid(): bool {
        return $this->getItemVariableName() !== "" and $this->getJson() !== "";
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $item = $this->getItem($source);
        $json = $this->getJson();

        try {
            $tags = JsonNbtParser::parseJson($json);
            $item->setNamedTag($tags);
        } catch (\UnexpectedValueException|NbtException $e) {
            if (Mineflow::isDebug()) Main::getInstance()->getLogger()->logException($e);
            throw new InvalidFlowValueException(Language::get("variable.convert.nbt.failed", [$e->getMessage(), $json]));
        }

        yield Await::ALL;
        return $this->getItemVariableName();
    }

    public function getEditFormElements(array $variables): array {
        return [
            new ItemVariableDropdown($variables, $this->getItemVariableName()),
            new ExampleInput("@action.setItemData.form.value", "{display:{Lore:}", $this->getJson(), true),
        ];
    }

    public function loadSaveData(array $content): FlowItem {
        $this->setItemVariableName($content[0]);
        $this->setJson($content[1]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getItemVariableName(), $this->getJson()];
    }
}
