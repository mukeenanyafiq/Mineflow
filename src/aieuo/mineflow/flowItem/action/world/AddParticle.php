<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\world;

use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\base\PositionFlowItem;
use aieuo\mineflow\flowItem\base\PositionFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\mineflow\ExampleInput;
use aieuo\mineflow\formAPI\element\mineflow\ExampleNumberInput;
use aieuo\mineflow\formAPI\element\mineflow\PositionVariableDropdown;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\Server;
use SOFe\AwaitGenerator\Await;

class AddParticle extends FlowItem implements PositionFlowItem {
    use PositionFlowItemTrait;
    use ActionNameWithMineflowLanguage;

    public function __construct(
        string         $position = "",
        private string $particle = "",
        private string $amount = "1"
    ) {
        parent::__construct(self::ADD_PARTICLE, FlowItemCategory::WORLD);

        $this->setPositionVariableName($position);
    }

    public function getDetailDefaultReplaces(): array {
        return ["position", "particle", "amount", ""];
    }

    public function getDetailReplaces(): array {
        return [$this->getPositionVariableName(), $this->getParticle(), $this->getAmount(), $this->getAmount() === "1" ? "" : "s"];
    }

    public function getPermissions(): array {
        return [self::PERMISSION_LOOP];
    }

    public function setParticle(string $particle): void {
        $this->particle = $particle;
    }

    public function getParticle(): string {
        return $this->particle;
    }

    public function setAmount(string $amount): void {
        $this->amount = $amount;
    }

    public function getAmount(): string {
        return $this->amount;
    }

    public function isDataValid(): bool {
        return $this->getPositionVariableName() !== "" and $this->particle !== "" and $this->amount !== "";
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $particleName = $source->replaceVariables($this->getParticle());
        $amount = $this->getInt($source->replaceVariables($this->getAmount()), 1);

        $position = $this->getPosition($source);

        for ($i = 0; $i < $amount; $i++) {
            $pk = new SpawnParticleEffectPacket();
            $pk->position = $position;
            $pk->particleName = $particleName;
            $pk->molangVariablesJson = "";
            Server::getInstance()->broadcastPackets($position->world->getPlayers(), [$pk]);
        }

        yield Await::ALL;
    }

    public function getEditFormElements(array $variables): array {
        return [
            new PositionVariableDropdown($variables, $this->getPositionVariableName()),
            new ExampleInput("@action.addParticle.form.particle", "minecraft:explosion_particle", $this->getParticle(), true),
            new ExampleNumberInput("@action.addParticle.form.amount", "1", $this->getAmount(), true, 1),
        ];
    }

    public function loadSaveData(array $content): FlowItem {
        $this->setPositionVariableName($content[0]);
        $this->setParticle($content[1]);
        $this->setAmount($content[2]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getPositionVariableName(), $this->getParticle(), $this->getAmount()];
    }
}
