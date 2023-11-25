<?php

declare(strict_types=1);

namespace Mwop\Art\Form;

use Phly\RuleValidation\Result\Result;
use Phly\RuleValidation\Rule\CallbackRule;
use Phly\RuleValidation\RuleSet\RuleSet;
use Phly\RuleValidation\RuleSet\RuleSetOptions;
use Phly\RuleValidation\ValidationResult;

/** @template-extends RuleSet<UploadResultSet> */
class UploadRuleSet extends RuleSet
{
    final public function __construct()
    {
        $options = new RuleSetOptions();
        $options->setResultSetClass(UploadResultSet::class);
        $options->addRule(new CallbackRule(
            'description', 
            function (mixed $value, array $context): ValidationResult {
                return is_string($value) && ! preg_match('/^\s+$/s', $value)
                    ? Result::forValidValue('description', $value)
                    : Result::forInvalidValue('description', '', 'Please submit a non-empty description');)
            },
            Result::forValidValue('description', ''),
            Result::forMissingValue('description', 'Missing description! Please resubmit with a non-empty description.'),
        ));

        parent::__construct($options);
    }
}
