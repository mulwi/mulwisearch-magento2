<?php

namespace Mulwi\Search\Index\Mirasvit;

use Mulwi\Search\Index\AbstractIndex;
use Mulwi\Search\Index\Context;
use Mulwi\Search\Index\Magento\CustomerIndex;
use Mulwi\Search\Index\Magento\OrderIndex;

class TicketIndex extends AbstractIndex
{
    const IDENTIFIER = 'ticket';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function isAvailable()
    {
        return $this->context->moduleList->has('Mirasvit_Helpdesk');
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->context->objectManager->create('Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('folder', ['neq' => 3]);//not spam
        $collection->addFieldToFilter('ticket_id', ['gt' => $lastEntityId]);

        return $collection;
    }

    public function mapDocument($ticket)
    {
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */

        $doc = $this->makeDocument(
            'Ticket',
            $this->makeExtId(self::IDENTIFIER, $ticket->getId())
        );

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

        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(OrderIndex::IDENTIFIER, $ticket->getOrderId())
        );

        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(CustomerIndex::IDENTIFIER, $ticket->getCustomerId())
        );

        return $doc;

        echo '.';
        $sections = [];
        //        $messages = $entity->getMessages();
        //        foreach ($messages as $message) {
        //            $sections[] = [
        //                'timestamp' => strtotime($message->getCreatedAt()),
        //                'author'    => $message->getUserName(),
        //                'body'      => $message->getBodyPlain(),
        //            ];
        //        }
        return [
            'extID' => $ticket->getId(),
            'title' => $ticket->getSubject(),
            'url' => $this->context->getUrl('catalog/product/edit', ['id' => $ticket->getId()]),
            'type' => $this->getIdentifier(),
            'timestamp' => strtotime($ticket->getCreatedAt()),
            'fields' => [
                [
                    'field' => 'code',
                    'value' => $ticket->getCode(),
                ],
                [
                    'field' => 'customer',
                    'value' => $ticket->getCustomerName(),
                ],
                [
                    'field' => 'status',
                    'value' => $ticket->getStatus()->getName(),
                ],
                [
                    'field' => 'priority',
                    'value' => $ticket->getPriority()->getName(),
                ],
            ],
            'sections' => $sections,
            'relations' => [
                [
                    'customer' => $ticket->getCustomerId(),
                ],
                [
                    'order' => $ticket->getOrderId(),
                ],
            ],
        ];
    }
}