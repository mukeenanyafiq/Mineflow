<?php

namespace aieuo\mineflow\formAPI\element\mineflow;

use aieuo\mineflow\flowItem\FlowItemIds;
use aieuo\mineflow\variable\DummyVariable;

class PositionVariableDropdown extends VariableDropdown {

    protected $variableType = DummyVariable::POSITION;

    protected $actions = [
        FlowItemIds::CREATE_POSITION_VARIABLE,
        FlowItemIds::GET_ENTITY_SIDE,
    ];

    public function __construct(array $variables = [], string $default = "", string $text = "@action.form.target.position") {
        parent::__construct($text, $variables, [DummyVariable::POSITION, DummyVariable::LOCATION, DummyVariable::PLAYER, DummyVariable::ENTITY, DummyVariable::BLOCK], $default);
    }
}