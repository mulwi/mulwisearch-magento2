<?php

namespace Mulwi\Search\Index\Mirasvit;

use Mulwi\Search\Index\AbstractIndex;
use Mulwi\Search\Index\Context;
use Mulwi\Search\Index\Magento\CustomerIndex;
use Mulwi\Search\Index\Magento\OrderIndex;

class TicketIndex extends AbstractIndex
{
    const IDENTIFIER = 'Ticket';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function isAvailable()
    {
        return $this->context->moduleList->has('Mirasvit_Helpdesk');
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Mirasvit\Helpdesk\Model\Ticket) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->context->create('Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection');
        $collection->setPageSize(100)
            ->addFieldToFilter('ticket_id', ['gt' => $lastId]);

        $docs = [];
        foreach ($collection as $entity) {
            $docs[] = $this->mapDocument($entity);
        }

        return $docs;
    }

    public function getDocument($id)
    {
        return $this->mapDocument(
            $this->context->create('Mirasvit\Helpdesk\Model\Ticket')
                ->load($id)
        );
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return \Mulwi\Search\Model\Document
     */
    public function mapDocument($ticket)
    {
        $doc = $this->context->makeDocument(self::IDENTIFIER, $ticket->getId());

        $doc->setTitle($ticket->getSubject())
            ->setUrl($this->context->getUrl('helpdesk/ticket/edit', ['id' => $ticket->getId()]))
            ->setCreatedAt($ticket->getCreatedAt());

        $doc->addMeta(
            'Code',
            $ticket->getCode()
        );

        $doc->addMeta(
            'Customer',
            $ticket->getCustomerName()
        );

        $doc->addMeta(
            'Status',
            $ticket->getStatus()->getName()
        );

        $doc->addMeta(
            'Priority',
            $ticket->getPriority()->getName()
        );

        $doc->addRelation(OrderIndex::IDENTIFIER, $ticket->getOrderId());

        $doc->addRelation(CustomerIndex::IDENTIFIER, $ticket->getCustomerId());

        return $doc;
    }
}