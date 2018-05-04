<?php

namespace Mulwi\Search\Index;

use Magento\Framework\DataObject;

class Document extends DataObject
{
    public function __construct(array $data = [])
    {
        $data['meta'] = [];
        $data['relations'] = [];

        parent::__construct($data);
    }

    public function setExtID($value)
    {
        return $this->setData('extID', $value);
    }

    public function setTitle($value)
    {
        return $this->setData('title', $value);
    }

    public function setUrl($value)
    {
        return $this->setData('url', $value);
    }

    public function setSource($value)
    {
        return $this->setData('source', $value);
    }

    public function setKind($value)
    {
        return $this->setData('kind', $value);
    }

    public function setCreatedAt($value)
    {
        $this->setUpdatedAt($value);

        $value = date('Y-m-d\TH:i:s\Z', strtotime($value));
        return $this->setData('createdAt', $value);
    }

    public function setUpdatedAt($value)
    {
        $value = date('Y-m-d\TH:i:s\Z', strtotime($value));
        return $this->setData('updatedAt', $value);
    }

    public function addMeta($label, $value)
    {
        $meta = $this->getData('meta');
        $meta[] = [
            'field' => $label . "",
            'value' => $value . "",
        ];

        return $this->setData('meta', $meta);
    }

    public function addRelation($source, $extID)
    {
        $relations = $this->getData('relations');
        $relations[] = [
            'source' => $source,
            'extID' => $extID,
        ];

        return $this->setData('relations', $relations);
    }

    public function addContent($content)
    {
        return $this->setData('content', $this->getData('content') . ' ' . $content);
    }
}