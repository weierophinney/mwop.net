<?php
namespace PhlyBlog\Filter;

use PhlyCommon\Filter\Timezone as TimezoneValidator,
    Zend\Filter\InputFilter;

class EntryFilter extends InputFilter
{
    public function __construct()
    {
        $filterRules = array(
            'id'         => 'string_trim',
            'title'      => array('string_trim', 'strip_tags', 'html_entities'),
            'body'       => 'string_trim',
            'extended'   => 'string_trim',
            'author'     => 'string_trim',
            'is_public'  => 'boolean',
            'is_draft'   => 'boolean',
            'timezone'   => 'string_trim',
        );

        $validatorRules = array(
            'id'        => array('not_empty', 'message' => 'Missing identifier; most likely, you did not provide a title.'),
            'title'     => array(array('string_length', 3), 'message' => 'Title must be at least 3 characters in length, and non-empty.'),
            'body'      => array('allowEmpty' => true),
            'extended'  => array('allowEmpty' => true),
            'author'    => array('not_empty', 'message' => 'Please login and provide your nom de plume.'),
            'created'   => array(
                'int',
                'message'    => 'Invalid timestamp for creation date.',
                'allowEmpty' => true,
            ),
            'updated'   => array(
                'int',
                'message'    => 'Invalid timestamp for updated date.',
                'allowEmpty' => true,
            ),
            'is_draft'  => array(array('callback', 'is_bool'), 'presence' => 'required', 'allowEmpty' => true, 'message' => 'Please select a flag indicating draft status.'),
            'is_public' => array(array('callback', 'is_bool'), 'presence' => 'required', 'allowEmpty' => true, 'message' => 'Please select a flag indicating publication status.'),
            'tags'      => new Tags(),
            'timezone'  => array(new TimezoneValidator(), 'required' => true),
        );

        $options = array(
            'escapeFilter' => 'string_trim',
        );

        parent::__construct($filterRules, $validatorRules, null, $options);
    }
}
