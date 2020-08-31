<?php

namespace aieuo\mineflow\flowItem\action;

use aieuo\mineflow\exception\InvalidFlowValueException;
use aieuo\mineflow\formAPI\element\CancelToggle;
use aieuo\mineflow\formAPI\element\ExampleInput;
use aieuo\mineflow\formAPI\Form;
use aieuo\mineflow\utils\Language;
use aieuo\mineflow\recipe\Recipe;
use aieuo\mineflow\formAPI\element\Label;
use aieuo\mineflow\formAPI\CustomForm;
use aieuo\mineflow\Main;

class CallRecipe extends ExecuteRecipe {

    protected $id = self::CALL_RECIPE;

    protected $name = "action.callRecipe.name";
    protected $detail = "action.callRecipe.detail";

    public function __construct(string $name = "", string $args = "") {
        parent::__construct($name, $args);
    }

    public function execute(Recipe $origin) {
        $this->throwIfCannotExecute();

        $name = $origin->replaceVariables($this->getRecipeName());

        $recipeManager = Main::getRecipeManager();
        [$recipeName, $group] = $recipeManager->parseName($name);
        if (empty($group)) $group = $origin->getGroup();

        $recipe = $recipeManager->get($recipeName, $group) ?? $recipeManager->get($recipeName, "");
        if ($recipe === null) {
            throw new InvalidFlowValueException($this->getName(), Language::get("action.executeRecipe.notFound"));
        }

        $helper = Main::getVariableHelper();
        $args = [];
        $recipe = clone $recipe;
        foreach ($this->getArgs() as $arg) {
            if (!$helper->isVariableString($arg)) {
                $args[] = $helper->replaceVariables($arg, $origin->getVariables());
                continue;
            }
            $arg = $origin->getVariable(substr($arg, 1, -1)) ?? $helper->get(substr($arg, 1, -1)) ?? $arg;
            $args[] = $arg;
        }
        $recipe->setSourceRecipe($origin)->setSourceContainer($this->getParent());
        $recipe->executeAllTargets($origin->getTarget(), [], $origin->getEvent(), $args);
        yield false;
        return true;
    }

    public function getEditForm(array $default = [], array $errors = []): Form {
        return (new CustomForm($this->getName()))
            ->setContents([
                new Label($this->getDescription()),
                new ExampleInput("@action.executeRecipe.form.name", "aieuo", $default[1] ?? $this->getRecipeName(), true),
                new ExampleInput("@action.callRecipe.form.args", "{target}, 1, aieuo", $default[1] ?? implode(", ", $this->getArgs())),
                new CancelToggle()
            ])->addErrors($errors);
    }

    public function parseFromFormData(array $data): array {
        return ["contents" => [$data[1], array_map("trim", explode(",", $data[2]))], "cancel" => $data[3], "errors" => []];
    }

    public function loadSaveData(array $content): Action {
        $this->setRecipeName($content[0]);
        $this->setArgs($content[1]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getRecipeName(), $this->getArgs()];
    }
}