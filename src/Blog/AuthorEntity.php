<?php
namespace Mwop\Blog;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Stdlib\ArraySerializableInterface;

class AuthorEntity implements
    ArraySerializableInterface,
    InputFilterAwareInterface
{
    protected $filter;

    protected $id;
    protected $name;
    protected $email;
    protected $url;

    /**
     * Set value for id
     *
     * This is a "short name" identifier for the author.
     *
     * @param  string id
     * @return AuthorEntity
     */
    public function setId($id)
    {
        $this->id = (string) $id;
        return $this;
    }
    
    /**
     * Get value for id
     *
     * This is a "short name" identifier for the author.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set full name of author
     *
     * @param  string name
     * @return AuthorEntity
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param  string email
     * @return AuthorEntity
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
        return $this;
    }
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set author url
     *
     * @param  string url
     * @return AuthorEntity
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;
        return $this;
    }
    
    /**
     * Get author url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setInputFilter(InputFilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function getInputFilter()
    {
        if (null === $this->filter) {
            $this->setInputFilter(new Filter\AuthorFilter());
        }
        return $this->filter;
    }

    public function isValid()
    {
        // Validate against the input filter
        $filter = $this->getInputFilter();
        $filter->setData($this->getArrayCopy());
        $valid = $filter->isValid();

        // If valid, push the filtered values back into the object
        if ($valid) {
            $this->exchangeArray($filter->getValues());
        }

        // Return validation result
        return $valid;
    }

    /**
     * Cast object to array
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'email' => $this->getEmail(),
            'url'   => $this->getUrl(),
        );
    }

    /**
     * Populate object from array
     * 
     * @param  array $array 
     * @return AuthorEntity
     */
    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'id':
                case 'name':
                case 'email':
                case 'url':
                    $method = 'set' . $key;
                    $this->$method($value);
                    break;
                default:
                    // Unknown; ignore
                    break;
            }
        }
        return $this;
    }
}
