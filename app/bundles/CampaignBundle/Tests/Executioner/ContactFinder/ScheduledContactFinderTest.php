<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CampaignBundle\Tests\Executioner\ContactFinder;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\CampaignBundle\Entity\LeadEventLog;
use Mautic\CampaignBundle\Executioner\ContactFinder\ScheduledContactFinder;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadRepository;

class ScheduledContactFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LeadRepository
     */
    private $leadRepository;

    protected function setUp()
    {
        $this->leadRepository = $this->getMockBuilder(LeadRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHydratedLeadsFromRepositoryAreFoundAndPushedIntoLogs()
    {
        $lead1 = $this->getMockBuilder(Lead::class)
            ->getMock();
        $lead1->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);

        $lead2 = $this->getMockBuilder(Lead::class)
            ->getMock();
        $lead2->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(2);

        $log1 = $this->getMockBuilder(LeadEventLog::class)
            ->getMock();
        $log1->expects($this->exactly(2))
            ->method('getLead')
            ->willReturn($lead1);
        $log1->expects($this->once())
            ->method('setLead');

        $log2 = $this->getMockBuilder(LeadEventLog::class)
            ->getMock();
        $log2->expects($this->exactly(2))
            ->method('getLead')
            ->willReturn($lead2);
        $log2->expects($this->once())
            ->method('setLead');

        $logs = new ArrayCollection(
            [
                1 => $log1,
                2 => $log2,
            ]
        );

        $contacs = new ArrayCollection(
            [
                1 => $lead1,
                2 => $lead2,
            ]
        );

        $this->leadRepository->expects($this->once())
            ->method('getContactCollection')
            ->willReturn($contacs);

        $this->getContactFinder()->hydrateContacts($logs);
    }

    /**
     * @return ScheduledContactFinder
     */
    private function getContactFinder()
    {
        return new ScheduledContactFinder(
            $this->leadRepository
        );
    }
}