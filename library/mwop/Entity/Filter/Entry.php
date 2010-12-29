<?php
namespace mwop\Entity\Filter;

use Zend\Filter\InputFilter,
    mwop\Filter\Tags as TagsValidator;

class Entry extends InputFilter
{
    public function __construct()
    {
        $filterRules = array(
            'id'         => 'StringTrim',
            'title'      => array('StringTrim', 'StripTags', 'HtmlEntities'),
            'body'       => 'StringTrim',
            'author'     => 'StringTrim',
            'is_draft'   => 'Boolean',
            'is_public'  => 'Boolean',
            'timezone'   => 'StringTrim',
        );

        $validatorRules = array(
            'id'        => array('NotEmpty', 'message' => 'Missing identifier; most likely, you did not provide a title.'),
            'title'     => array(array('StringLength', 3), 'message' => 'Title must be at least 3 characters in length, and non-empty.'),
            'body'      => array('allowEmpty' => true),
            'author'    => array('NotEmpty', 'message' => 'Please login and provide your nom de plume.'),
            'created'   => array(
                'Int',
                'message'    => 'Invalid timestamp for creation date.',
                'allowEmpty' => true,
            ),
            'updated'   => array(
                'Int',
                'message'    => 'Invalid timestamp for updated date.',
                'allowEmpty' => true,
            ),
            'is_draft'  => array(array('Callback', 'is_bool'), 'presence' => 'required', 'allowEmpty' => true, 'message' => 'Please select a flag indicating draft status.'),
            'is_public' => array(array('Callback', 'is_bool'), 'presence' => 'required', 'allowEmpty' => true, 'message' => 'Please select a flag indicating publication status.'),
            'tags'      => new TagsValidator(),
            'timezone'  => array(array('Callback', function($value) {
                if (!timezone_open($value)) {
                    return false;
                }
                return true;
            }), 'required' => true),
        );

        $options = array(
            'escapeFilter' => 'Zend\Filter\StringTrim',
        );

        parent::__construct($filterRules, $validatorRules, null, $options);
    }
}
