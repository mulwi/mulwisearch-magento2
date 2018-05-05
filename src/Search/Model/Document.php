<?php

namespace Mulwi\Search\Model;

use Magento\Framework\DataObject;
use Mulwi\Search\Api\Data\DocumentInterface;

class Document extends DataObject implements DocumentInterface
{
    public function __construct(array $data = [])
    {
        $data['source'] = Config::SOURCE;
        $data['meta'] = [];
        $data['relations'] = [];

        parent::__construct($data);
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function setId($value)
    {
        return $this->setData('id', $value);
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

    public function addRelation($kind, $id)
    {
        $relations = $this->getData('relations');
        $relations[] = [
            'source' => Config::SOURCE,
            'extID' => $kind . '_' . $id,
        ];

        return $this->setData('relations', $relations);
    }

    public function addSearchable($content)
    {
        return $this->setData('searchable', $this->getData('searchable') . ' ' . $content);
    }
}