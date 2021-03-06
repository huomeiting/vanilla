<?php
/**
 * @author Adam (charrondev) Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 */

namespace Vanilla\Formatting\Quill\Blots\Lines;

use Vanilla\Formatting\Quill\BlotGroup;

/**
 * Blot for Lists of all kinds. All supported types are constants on this class.
 */
class ListLineBlot extends AbstractLineBlot {

    const LIST_TYPE_BULLET = "bullet";
    const LIST_TYPE_ORDERED = "ordered";
    const LIST_TYPE_UNRECOGNIZED = "unrecognized list value";
    const LIST_TYPES = [self::LIST_TYPE_BULLET, self::LIST_TYPE_ORDERED];

    /**
     * @inheritdoc
     */
    public static function matches(array $operations): bool {
        return static::opAttrsContainKeyWithValue($operations, "list", static::LIST_TYPES);
    }

    /**
     * @inheritdoc
     */
    public function shouldClearCurrentGroup(BlotGroup $group): bool {
        $surroundingBlot = $group->getBlotForSurroundingTags();
        if ($surroundingBlot instanceof ListLineBlot) {
            // If the list types are different we need to clear the block.
            return $surroundingBlot->getListType() !== $this->getListType();
        } else {
            return parent::shouldClearCurrentGroup($group);
        }
    }
    /**
     * @inheritdoc
     */
    public function renderLineStart(): string {
        $classString = "";
        $indentLevel = $this->currentOperation["attributes"]["indent"]
            ?? $this->nextOperation["attributes"]["indent"]
            ?? null;
        if ($indentLevel && filter_var($indentLevel, FILTER_VALIDATE_INT) !== false) {
            $classString = " class=\"ql-indent-$indentLevel\"";
        }

        return "<li$classString>";
    }

    /**
     * @inheritdoc
     */
    public function renderLineEnd(): string {
        return "</li>";
    }

    /**
     * @inheritDoc
     */
    public function getGroupOpeningTag(): string {
        switch ($this->getListType()) {
            case static::LIST_TYPE_BULLET:
                return "<ul>";
            case static::LIST_TYPE_ORDERED:
                return "<ol>";
            default:
                return "";
        }
    }

    /**
     * @inheritDoc
     */
    public function getGroupClosingTag(): string {
        switch ($this->getListType()) {
            case static::LIST_TYPE_BULLET:
                return "</ul>";
            case static::LIST_TYPE_ORDERED:
                return "</ol>";
            default:
                return "";
        }
    }

    /**
     * Determine which type of list we are in.
     *
     * @return string
     */
    private function getListType() {
        $listType = $this->nextOperation["attributes"]["list"] ?? static::LIST_TYPE_UNRECOGNIZED;
        return !in_array($listType, static::LIST_TYPES) ? static::LIST_TYPE_UNRECOGNIZED : $listType;
    }
}
