<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\entity;

use aieuo\mineflow\exception\InvalidFlowValueException;
use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use aieuo\mineflow\formAPI\element\mineflow\ExampleInput;
use aieuo\mineflow\utils\EntityHolder;
use aieuo\mineflow\utils\Language;
use aieuo\mineflow\variable\DummyVariable;
use aieuo\mineflow\variable\object\EntityVariable;
use aieuo\mineflow\variable\object\PlayerVariable;
use SOFe\AwaitGenerator\Await;

class GetEntity extends FlowItem {
    use ActionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    protected string $returnValueType = self::RETURN_VARIABLE_NAME;

    public function __construct(private string $entityId = "", private string $resultName = "entity") {
        parent::__construct(self::GET_ENTITY, FlowItemCategory::ENTITY);
    }

    public function getDetailDefaultReplaces(): array {
        return ["id", "result"];
    }

    public function getDetailReplaces(): array {
        return [$this->getEntityId(), $this->getResultName()];
    }

    public function setEntityId(string $name): void {
        $this->entityId = $name;
    }

    public function getEntityId(): string {
        return $this->entityId;
    }

    public function setResultName(string $name): void {
        $this->resultName = $name;
    }

    public function getResultName(): string {
        return $this->resultName;
    }

    public function isDataValid(): bool {
        return $this->getEntityId() !== "" and !empty($this->getResultName());
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $id = $this->getInt($source->replaceVariables($this->getEntityId()), min: 0);
        $resultName = $source->replaceVariables($this->getResultName());

        $entity = EntityHolder::findEntity($id);
        if ($entity === null) {
            throw new InvalidFlowValueException($this->getName(), Language::get("action.getEntity.notFound", [(string)$id]));
        }
        $source->addVariable($resultName, EntityVariable::fromObject($entity));

        yield Await::ALL;
        return $this->getResultName();
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            new ExampleInput("@action.getEntity.form.target", "aieuo", $this->getEntityId(), true),
            new ExampleInput("@action.form.resultVariableName", "entity", $this->getResultName(), true),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->setEntityId($content[0]);
        $this->setResultName($content[1]);
    }

    public function serializeContents(): array {
        return [$this->getEntityId(), $this->getResultName()];
    }

    public function getAddingVariables(): array {
        return [
            $this->getResultName() => new DummyVariable(PlayerVariable::class, $this->getEntityId())
        ];
    }
}
