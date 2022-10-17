<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\scoreboard;

use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItem;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\mineflow\ExampleInput;
use aieuo\mineflow\formAPI\element\mineflow\ScoreboardVariableDropdown;
use aieuo\mineflow\utils\Language;
use SOFe\AwaitGenerator\Await;

class SetScoreboardScoreName extends FlowItem implements ScoreboardFlowItem {
    use ScoreboardFlowItemTrait;
    use ActionNameWithMineflowLanguage;

    public function __construct(
        string         $scoreboard = "",
        private string $scoreName = "",
        private string $score = ""
    ) {
        parent::__construct(self::SET_SCOREBOARD_SCORE_NAME, FlowItemCategory::SCOREBOARD);

        $this->setScoreboardVariableName($scoreboard);
    }

    public function getDetailDefaultReplaces(): array {
        return ["scoreboard", "name", "score"];
    }

    public function getDetailReplaces(): array {
        return [$this->getScoreboardVariableName(), $this->getScoreName(), $this->getScore()];
    }

    public function getScoreName(): string {
        return $this->scoreName;
    }

    public function setScoreName(string $scoreName): void {
        $this->scoreName = $scoreName;
    }

    public function getScore(): string {
        return $this->score;
    }

    public function setScore(string $score): void {
        $this->score = $score;
    }

    public function isDataValid(): bool {
        return $this->getScoreboardVariableName() !== "" and $this->getScore() !== "";
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $name = $source->replaceVariables($this->getScoreName());
        $score = $this->getInt($source->replaceVariables($this->getScore()));
        $board = $this->getScoreboard($source);

        $board->setScoreName($name, $score);

        yield Await::ALL;
    }

    public function getEditFormElements(array $variables): array {
        return [
            new ScoreboardVariableDropdown($variables, $this->getScoreboardVariableName()),
            new ExampleInput("@action.setScore.form.name", "aieuo", $this->getScoreName(), false),
            new ExampleInput("@action.setScore.form.score", "100", $this->getScore(), true),
        ];
    }

    public function loadSaveData(array $content): FlowItem {
        $this->setScoreboardVariableName($content[0]);
        $this->setScoreName($content[1]);
        $this->setScore($content[2]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getScoreboardVariableName(), $this->getScoreName(), $this->getScore()];
    }
}
