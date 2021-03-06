<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license GPLv2
 */

namespace Vanilla;

/**
 * Validates body fields to make sure it complies with its format.
 */
class BodyFormatValidator {
    private $validators = [];

    /**
     * BodyFormatValidator constructor.
     */
    public function __construct() {
        $this->validators = [
            'rich' => [$this, 'validateRich'],
        ];
    }

    /**
     * Add a validator for a specific format.
     *
     * The validator must be a callable with the following signature:
     *
     * ```
     * function validate($value, $field, $row = []): mixed|Invalid
     * ```
     *
     * The validator will return value, optionally filtered on success or an instance of `Vanilla\Invalid` on failure.
     *
     * Adding a validator to a format that already exists will replace the existing validator.
     *
     * @param string $format The format to validate.
     * @param callable|null $validator The validation function.
     * @return $this
     */
    public function addFormatValidator(string $format, callable $validator = null) {
        $this->validators[strtolower($format)] = $validator;
        return $this;
    }

    /**
     * Validate richly formatted text.
     *
     * @param string $value The value to validate.
     * @param object $field The field meta data of the value.
     * @param array $row The entire row where the field is.
     * @return string|Invalid Returns the re-encoded string on success or `Invalid` on failure.
     */
    private function validateRich($value, $field, $row = []) {
        $value = json_decode($value, true);
        if ($value === null) {
            $value = new Invalid("%s is not valid rich text.");
        } else {
            // Re-encode the value to escape unicode values.
            $value = json_encode($value);
        }

        return $value;
    }

    /**
     * Validate a body field against its format.
     *
     * @param string $value The value to validate.
     * @param object $field The field meta data of the value.
     * @param array $row The entire row where the field is.
     * @return string|Invalid Returns the valid string on success or `Invalid` on failure.
     */
    public function __invoke($value, $field, $row = []) {
        $format = strtolower($row['Format'] ?? 'raw');

        if (isset($this->validators[$format])) {
            $valid = call_user_func($this->validators[$format], $value, $field, $row);
        } else {
            $valid = $value;
        }

        return $valid;
    }
}
