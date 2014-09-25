<?php

namespace stefantalen\OmniKassa\Tests;

use stefantalen\OmniKassa\OmniKassaEvents;

class OmniKassaEventsTest extends \PHPUnit_Framework_TestCase
{
    public function testCodes()
    {
        $this->assertSame('00', OmniKassaEvents::SUCCESS);
        $this->assertSame('05', OmniKassaEvents::FAILURE);
        $this->assertSame('17', OmniKassaEvents::CANCELLED);
        $this->assertSame('60', OmniKassaEvents::OPEN);
        $this->assertSame('90', OmniKassaEvents::SERVER_UNREACHABLE);
        $this->assertSame('97', OmniKassaEvents::EXPIRED);
    }
}
