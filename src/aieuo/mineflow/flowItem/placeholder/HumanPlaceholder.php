<?php
declare(strict_types=1);


namespace aieuo\mineflow\flowItem\placeholder;

use aieuo\mineflow\exception\InvalidPlaceholderValueException;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\Element;
use aieuo\mineflow\formAPI\element\mineflow\HumanVariableDropdown;
use aieuo\mineflow\utils\Language;
use aieuo\mineflow\variable\object\HumanVariable;
use pocketmine\entity\Human;
use pocketmine\player\Player;

class HumanPlaceholder extends Placeholder {

    public function __construct(string $name, string $value = "", string $description = null, bool $optional = false) {
        parent::__construct($name, $value, $description ?? "@action.form.target.human", $optional);
    }

    /**
     * @throws InvalidPlaceholderValueException
     */
    public function getHuman(FlowItemExecutor $executor): Human {
        $human = $executor->replaceVariables($this->get());

        $variable = $executor->getVariable($human);
        if ($variable instanceof HumanVariable) {
            return $variable->getValue();
        }

        throw new InvalidPlaceholderValueException(Language::get("action.target.not.valid", [["action.target.require.human"], $this->get()]));
    }

    /**
     * @throws InvalidPlaceholderValueException
     */
    public function getOnlineHuman(FlowItemExecutor $executor): Human {
        $human = $this->getHuman($executor);
        if ($human instanceof Player and !$human->isOnline()) {
            throw new InvalidPlaceholderValueException(Language::get("action.error.human.offline"));
        }
        return $human;
    }

    public function createFormElement(array $variables): Element {
        return new HumanVariableDropdown($variables, $this->get(), $this->getDescription(), $this->isOptional());
    }
}